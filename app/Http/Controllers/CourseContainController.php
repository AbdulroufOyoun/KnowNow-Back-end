<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoruseContain\PdfRequest;
use App\Http\Requests\CoruseContain\VideoRequest;
use App\Http\Requests\Course\CourseIdRequest;
use App\Http\Requests\CourseContain\CourseContainIdRequest;
use App\Http\Requests\CourseContain\CourseContainRequest;
use App\Http\Resources\CourseContain\CourseContainResource;
use App\Models\CollectionCode;
use App\Models\course;
use App\Models\CourseCode;
use App\Models\CourseCollection;
use App\Models\CourseContain;
use App\Models\UserCode;
use App\Repositories\PublicRepository;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Exporters\HLSVideoFilters;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class CourseContainController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(CourseIdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['courseId']);
        $course = $this->publicRepository->ShowAll(course::class, ['id' => $arr['courseId']])->first();
        if ($course->is_active == false &&  count(\Auth::user()->getRoleNames()) == 0) {
            return \Success('No thing to see here.');
        }
        $where = ['course_id' => $arr['courseId']];
        $courseContains = $this->publicRepository->ShowAll(CourseContain::class, $where)->get();
        $contain['theoretical'] = [];
        $contain['practical'] = [];

        foreach ($courseContains as $courseContain) {
            if ($courseContain->is_theoretical) {
                $contain['theoretical'][] = $courseContain;
            } else {
                $contain['practical'][] = $courseContain;
            }
        }
        return \SuccessData(__('public.Show'), $contain);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseContainRequest $request)
    {
        $arr = Arr::only($request->validated(), ['name', 'video', 'pdf', 'course_id', 'is_free', 'is_theoretical']);

        $pdfName = $arr['pdf']->getClientOriginalName();

        $pdfNewName = rand(9999999999, 99999999999) . $pdfName;
        $arr['pdf']->storeAs('pdfFiles', $pdfNewName, 'pdf');
        $arr['pdf'] = $pdfNewName;
        $videoName = pathinfo($arr['video']->getClientOriginalName(), PATHINFO_FILENAME);
        $newName = rand(9999999999, 99999999999) . $videoName;
        $arr['video']->storeAs('uploads', "{$newName}.mp4", 'uploads');
        $lowFormat  = (new X264('aac'))->setKiloBitrate(360);
        $highFormat = (new X264('aac'))->setKiloBitrate(720);

        FFMpeg::fromDisk('uploads')
            ->open("uploads/{$newName}.mp4")
            ->exportForHLS()
            ->withRotatingEncryptionKey(function ($fileName, $contents) {
                Storage::disk('secrets')->put("$fileName", $contents);
            })
            ->addFormat($lowFormat, function (HLSVideoFilters $filters) {
                $filters->resize(1280, 720);
            })
            ->addFormat($highFormat)
            ->toDisk('public')
            ->save("videos/{$newName}.m3u8");
        $arr['video'] = "{$newName}.m3u8";
        if (Storage::disk('uploads')->exists("uploads")) {
            File::deleteDirectory(storage_path('uploads/uploads'));
        }
        $this->publicRepository->Create(CourseContain::class, $arr);
        return \Success(__('public.Create'));
    }

    public function getSecretKey($key)
    {
        return Storage::disk('secrets')->download($key);
    }

    public function getPdf(PdfRequest $request)
    {
        $arr = Arr::only($request->validated(), ['pdf']);

        $courseContains = $this->publicRepository->ShowAll(CourseContain::class, ['pdf' => $arr['pdf']])->first()->course_id;
        $courseCodes = CourseCode::onlyTrashed()->where('course_id', $courseContains)->pluck('id');

        $courseCollections =  $this->publicRepository->ShowAll(CourseCollection::class, ['course_id' => $courseContains])->pluck('collection_id');
        $collectionCodes = CollectionCode::onlyTrashed()->whereIn('collection_id', $courseCollections)->pluck('id');

        $userCodes = UserCode::where('user_id', \Auth::user()->id)->where(function ($query) use ($courseCodes, $collectionCodes) {
            $query->whereIn('course_code_id', $courseCodes)
                ->orWhereIn('collection_code_id', $collectionCodes);
        })->first();
        if ($userCodes) {
            return Storage::disk('pdf')->download("pdfFiles/{$arr['pdf']}");
        } else {
            return \Success('لست مشترك بهذه الدورة', false);
        }
    }

    public function getPlaylist($playlist)
    {
        return FFMpeg::dynamicHLSPlaylist()
            ->fromDisk('public')
            ->open("videos/{$playlist}")
            ->setKeyUrlResolver(function ($key) {
                return route('api.video.key', ['key' => $key]);
            })
            ->setMediaUrlResolver(function ($mediaFilename) {
                return Storage::disk('public')->url("videos/{$mediaFilename}");
            })
            ->setPlaylistUrlResolver(function ($playlistFilename) {
                return route('api.video.playlist', ['playlist' => $playlistFilename]);
            });
    }
    public function showPlaylist(VideoRequest $request)
    {
        $arr = Arr::only($request->validated(), ['video']);

        $courseContains = $this->publicRepository->ShowAll(CourseContain::class, ['video' => $arr['video']])->first()->course_id;
        $courseCodes = CourseCode::onlyTrashed()->where('course_id', $courseContains)->pluck('id');

        $courseCollections =  $this->publicRepository->ShowAll(CourseCollection::class, ['course_id' => $courseContains])->pluck('collection_id');
        $collectionCodes = CollectionCode::onlyTrashed()->whereIn('collection_id', $courseCollections)->pluck('id');

        $userCodes = UserCode::where('user_id', \Auth::user()->id)->where(function ($query) use ($courseCodes, $collectionCodes) {
            $query->whereIn('course_code_id', $courseCodes)
                ->orWhereIn('collection_code_id', $collectionCodes);
        })->first();
        if ($userCodes) {
            return $this->getPlaylist($arr['video']);
        } else {
            return \Success('لست مشترك بهذه الدورة', false);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseContainIdRequest $request)
    {
        $courseRequest = Arr::only($request->validated(), ['courseContainId']);

        $courseContain = $this->publicRepository->ShowAll(CourseContain::class, ['id' => $courseRequest['courseContainId']])->first();
        $baseVideoName = substr($courseContain->video, 0, -6); // Remove last 4 characters (e.g., '.mp4')
        $files = Storage::disk('public')->files('videos'); // Get all files in the 'videos' directory
        foreach ($files as $file) {
            // Check if the file starts with $baseVideoName
            if (str_starts_with(basename($file), $baseVideoName)) {
                Storage::disk('public')->delete($file); // Delete the file
            }
        }
        Storage::disk('pdf')->delete("pdfFiles/{$courseContain->pdf}"); // Delete the file

        $this->publicRepository->DeleteById(CourseContain::class, $courseRequest['courseContainId']);
        return \Success(__('public.Delete'));
    }
}