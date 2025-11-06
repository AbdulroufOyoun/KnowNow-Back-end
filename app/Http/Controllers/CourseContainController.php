<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\CourseIdRequest;
use App\Http\Requests\CourseContain\CourseContainIdRequest;
use App\Http\Requests\CourseContain\CourseContainRequest;
use App\Http\Requests\CourseContain\UpdateContainRequest;
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
        $contain['theoretical'] = [];
        $contain['practical'] = [];
        $contain['is_subscribed'] = false;
        $user = \Auth::user();
        $userRole = $user->getRoleNames();
        $userCodes =  $this->publicRepository->ShowAll(UserCode::class, ['user_id' => $user->id])->get();
        if (count($userRole) > 0 && ($userRole[0] === 'superAdmin' || $userRole[0] === 'admin')) {
            $contain['is_subscribed'] = true;
            $courseContains = $this->publicRepository->ShowAll(CourseContain::class, $where)->get();
        } else {
            $courseContains = $this->publicRepository->ShowAll(CourseContain::class, $where)->where('is_active', 1)->get();
            foreach ($userCodes as $userCode) {
                if ($userCode->course_code_id) {
                    $courseCodes = CourseCode::onlyTrashed()->where('id', $userCode->course_code_id)->pluck('course_id');
                    foreach ($courseCodes as $courseCode) {
                        if ($courseCode == $arr['courseId']) {
                            $contain['is_subscribed'] = true;
                        }
                    }
                }
                if ($userCode->collection_code_id) {
                    $collectionCodes = CollectionCode::onlyTrashed()->where('id', $userCode->user_code_id)->pluck('collection_id');
                    $collectionCourses = CourseCollection::whereIn('id', $collectionCodes)->get();
                    foreach ($collectionCourses as $collectionCourse) {
                        if ($collectionCourse->course_id == $arr['courseId']) {
                            $contain['is_subscribed'] = true;
                        }
                    }
                }
            }
        }
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
    public function pdfs(Request $request)
    {
        $arr = $request->query('coursesIds');

        $courses = CourseContain::whereIn('course_id', json_decode($arr))->get();
        $pdfs = [];
        foreach ($courses as $course) {
            $pdfs[] = $course->pdf;
        }
        return \SuccessData(__('public.Show'), $pdfs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseContainRequest $request)
    {
        $arr = Arr::only($request->validated(), ['name', 'video', 'pdf', 'course_id', 'is_free', 'is_theoretical']);
        if (isset($arr['pdf'])) {
            $pdfName = $arr['pdf']->getClientOriginalName();

            $pdfNewName = rand(9999999999, 99999999999) . $pdfName;
            $arr['pdf']->storeAs('pdfFiles', $pdfNewName, 'pdf');
            $arr['pdf'] = $pdfNewName;
        }


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
            ->addFormat($highFormat, function (HLSVideoFilters $filters) {
                $filters->resize(1280, 720);
            })
            // ->addFormat($highFormat)
            ->toDisk('public')
            ->save("videos/{$newName}.m3u8");
        $arr['video'] = "{$newName}.m3u8";
        if (Storage::disk('uploads')->exists("uploads")) {
            File::deleteDirectory(storage_path('uploads/uploads'));
        }
        $this->publicRepository->Create(CourseContain::class, $arr);
        return \Success(__('public.Create'));
    }

    public function getSecretKey($key, $playlist)
    {
        return Storage::disk('secrets')->download($key);
    }

    public function getPdf(Request $request)
    {
        // $arr = Arr::only($request->validated(), ['pdf']);
        $arr = $request->route('pdf');

        $courseContains = $this->publicRepository->ShowAll(CourseContain::class, ['pdf' => $arr])->first();
        if ($courseContains->is_free) {
            return Storage::disk('pdf')->download("pdfFiles/{$arr}");
        }
        $courseCodes = CourseCode::onlyTrashed()->where('course_id', $courseContains->course_id)->pluck('id');

        $courseCollections =  $this->publicRepository->ShowAll(CourseCollection::class, ['course_id' => $courseContains->course_id])->pluck('collection_id');
        $collectionCodes = CollectionCode::onlyTrashed()->whereIn('collection_id', $courseCollections)->pluck('id');

        $userCodes = UserCode::where('user_id', \Auth::user()->id)->where(function ($query) use ($courseCodes, $collectionCodes) {
            $query->whereIn('course_code_id', $courseCodes)
                ->orWhereIn('collection_code_id', $collectionCodes);
        })->first();
        if ($userCodes) {
            return Storage::disk('pdf')->download("pdfFiles/{$arr}");
        } else {
            return \Success('لست مشترك بهذه الدورة', false);
        }
    }
    public function storeLinked(Request $request)
    {
        // return $arr = Arr::only($containId->validated(), ['courseContainId']);
        $contain = $this->publicRepository->ShowById(CourseContain::class, $request->courseContainId);
        $contain->name = $request->name;
        $contain->is_free = $request->is_free;
        $contain->is_theoretical = $request->is_theoretical;
        $contain->course_id = $request->course_id;
        $contain->is_free = 0;
        // Convert to array
        $data = $contain->toArray();

        // Remove unwanted keys
        unset($data['id']);

        // Create new record
        $this->publicRepository->Create(CourseContain::class, $data);

        return \Success(__('public.Create'));
    }

    public function getPlaylist($playlist)
    {
        return FFMpeg::dynamicHLSPlaylist()
            ->fromDisk('public')
            ->open("videos/{$playlist}")
            ->setKeyUrlResolver(function ($key) use ($playlist) {
                return route('web.video.key', [
                    'key' => $key,
                    'playlist' => $playlist
                ]);
            })
            ->setMediaUrlResolver(function ($mediaFilename) use ($playlist) {
                return Storage::disk('public')->url("videos/{$mediaFilename}");
            })
            ->setPlaylistUrlResolver(function ($playlistFilename) use ($playlist) {
                return route('web.video.playlist', [
                    'playlist' => $playlistFilename
                ]);
            });
    }

    public function showPlaylist(Request $request)
    {
        $playlist = $request->route('playlist');
        return $this->getPlaylist($playlist);
    }

    public function toggle(CourseContainIdRequest $request)
    {
        $courseRequest = Arr::only($request->validated(), ['courseContainId']);
        $courseContain = $this->publicRepository->ShowAll(CourseContain::class, ['id' => $courseRequest['courseContainId']])->first();
        $courseContain->is_free = !$courseContain->is_free;
        $courseContain->save();
        return \Success(__('public.Update'));
    }
    public function toggleActive(CourseContainIdRequest $request)
    {
        $courseRequest = Arr::only($request->validated(), ['courseContainId']);
        $courseContain = $this->publicRepository->ShowAll(CourseContain::class, ['id' => $courseRequest['courseContainId']])->first();
        $courseContain->is_active = !$courseContain->is_active;
        $courseContain->save();
        return \Success(__('public.Update'));
    }
    public function update(UpdateContainRequest $request)
    {
        $validated = $request->validated();
        $courseContain = $this->publicRepository->ShowAll(CourseContain::class, ['id' => $validated['courseContainId']])->first();

        if (!$courseContain) {
            return;
        }
        $arr = Arr::only($validated, ['name']);

        if (isset($validated['name'])) {
            $courseContain->name = $arr['name'];
            $courseContain->save();
        }

        // ✅ Handle PDF update if provided
        if (isset($validated['pdf'])) {
            // Delete old PDF
            Storage::disk('pdf')->delete("pdfFiles/{$courseContain->pdf}");

            $pdfName = $validated['pdf']->getClientOriginalName();
            $pdfNewName = rand(9999999999, 99999999999) . $pdfName;
            $validated['pdf']->storeAs('pdfFiles', $pdfNewName, 'pdf');
            $arr['pdf'] = $pdfNewName;
        }

        // ✅ Handle video update if provided
        if (isset($validated['video'])) {
            // Delete old HLS video files
            $baseVideoName = substr($courseContain->video, 0, -5); // remove ".m3u8"
            $files = Storage::disk('public')->files('videos');
            foreach ($files as $file) {
                if (str_starts_with(basename($file), $baseVideoName)) {
                    Storage::disk('public')->delete($file);
                }
            }

            $videoName = pathinfo($validated['video']->getClientOriginalName(), PATHINFO_FILENAME);
            $newName = rand(9999999999, 99999999999) . $videoName;
            $validated['video']->storeAs('uploads', "{$newName}.mp4", 'uploads');

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

            // Clean up temp upload
            if (Storage::disk('uploads')->exists("uploads")) {
                File::deleteDirectory(storage_path('uploads/uploads'));
            }
        }

        // $this->publicRepository->Update(CourseContain::class, ['id' => $validated['courseContainId']], $arr);
        return \Success(__('public.Update'));
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
