<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Storage;
use App\Models\CourseContain;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ProcessVideoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // وقت التنفيذ المسموح (ساعة واحدة للفيديوهات الطويلة)
    public $timeout = 3600;
    // عدد مرات إعادة المحاولة في حال الفشل
    public $tries = 3;

    // عدد الثواني التي يجب انتظارها قبل محاولة تنفيذ المهمة مرة أخرى
    public $backoff = 60;
    protected $data;
    protected $originalVideoPath;
    protected $uniqueName;

    public function __construct($data, $originalVideoPath, $uniqueName)
    {
        $this->data = $data;
        $this->originalVideoPath = $originalVideoPath;
        $this->uniqueName = $uniqueName;
    }

    public function handle()
    {
        // إعدادات الفورمات مع تحسين الألوان (لمنع البهتان)
        $highFormat = (new X264('aac'))->setKiloBitrate(720);
        $highFormat->setAdditionalParameters([
            '-preset',
            'superfast',
            '-threads',
            '0',
            '-pix_fmt',
            'yuv420p',
            '-movflags',
            '+faststart',
        ]);

        // تنفيذ عملية التشفير والتحويل
        FFMpeg::fromDisk('obs')
            ->open($this->originalVideoPath)
            ->exportForHLS()
            ->withRotatingEncryptionKey(function ($fileName, $contents) {
                Storage::disk('secrets')->put("$fileName", $contents);
            })
            ->addFormat($highFormat, function ($filters) {
                $filters->resize(1280, 720);
            })
            ->toDisk('obs')
            ->save("videos/{$this->uniqueName}.m3u8");

        // بعد انتهاء المعالجة، نقوم بحفظ البيانات في قاعدة البيانات
        $this->data['video'] = "{$this->uniqueName}.m3u8";
        CourseContain::create($this->data);

        // اختياري: حذف الفيديو الأصلي لتوفير المساحة
        Storage::disk('obs')->delete($this->originalVideoPath);
    }

    /**
     * معالجة فشل الجوب
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        $videoName = $this->uniqueName;
        $errorMessage = $exception->getMessage();

        // إرسال إيميل بسيط (يمكنك إنشاء Mailable مخصص لاحقاً)
        Mail::raw("فشلت عملية معالجة الفيديو: {$videoName}. \n\n الخطأ هو: \n {$errorMessage}", function ($message) use ($videoName) {
            $message->to('oyoun26@gmail.com') // ضع بريدك هنا
                ->subject("خطأ في معالجة الفيديو: {$videoName}");
        });
        Mail::raw("فشلت عملية معالجة الفيديو: {$videoName}. \n\n الخطأ هو: \n {$errorMessage}", function ($message) use ($videoName) {
            $message->to('Molhamaboud0@gmail.com') // ضع بريدك هنا
                ->subject("خطأ في معالجة الفيديو: {$videoName}");
        });
        Mail::raw("فشلت عملية معالجة الفيديو: {$videoName}. \n\n الخطأ هو: \n {$errorMessage}", function ($message) use ($videoName) {
            $message->to('wjz1823@gmail.com') // ضع بريدك هنا
                ->subject("خطأ في معالجة الفيديو: {$videoName}");
        });


        // ملاحظة: يُفضل أيضاً تسجيل الخطأ في اللوج
        \Log::error("Job Failed for video {$videoName}: " . $errorMessage);
    }
}
