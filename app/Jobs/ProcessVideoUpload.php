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

class ProcessVideoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // وقت التنفيذ المسموح (ساعة واحدة للفيديوهات الطويلة)
    public $timeout = 3600;

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
                // فلتر تصحيح الألوان لمنع البهتان في S24 Ultra
                $filters->custom('eq=contrast=1.1:saturation=1.3,format=yuv420p');
                $filters->resize(1280, 720);
            })
            ->toDisk('obs')
            ->save("videos/{$this->uniqueName}.m3u8");

        // بعد انتهاء المعالجة، نقوم بحفظ البيانات في قاعدة البيانات
        $this->data['video'] = "{$this->uniqueName}.m3u8";
        CourseContain::create($this->data);

        // اختياري: حذف الفيديو الأصلي لتوفير المساحة
        // Storage::disk('obs')->delete($this->originalVideoPath);
    }
}
