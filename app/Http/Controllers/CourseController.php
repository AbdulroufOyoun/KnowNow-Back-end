<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\CourseIdRequest;
use App\Http\Requests\Course\CourseRequest;
use App\Http\Requests\Public\SearchRequest;
use App\Http\Resources\Course\CourseAdminsResource;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Course\SearchCourseResource;
use App\Http\Resources\Public\Search\SearchNameResource;
use App\Models\CollectionCode;
use App\Models\course;
use App\Models\CourseCode;
use App\Models\CourseCollection;
use App\Models\CourseContain;
use App\Models\SpecializationCourse;
use App\Models\UserCode;
use App\Repositories\PublicRepository;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perPage = \returnPerPage();
        $courses = $this->publicRepository->ShowAll(course::class, ['is_active' => 1])->paginate($perPage);
        CourseResource::Collection($courses);
        return \Pagination($courses);
    }
    public function adminIndex()
    {
        $perPage = \returnPerPage();
        $courses = $this->publicRepository->ShowAll(course::class, [])->paginate($perPage);
        CourseAdminsResource::Collection($courses);
        return \Pagination($courses);
    }

    public function userCourses()
    {
        $user = \Auth::user();
        $userSubscribes = $this->publicRepository->ShowAll(UserCode::class, ['user_id' => $user->id])->get();
        $userCoursesIds = [];

        foreach ($userSubscribes as $userSubscribe) {
            if ($userSubscribe->course_code_id) {
                $courseCodes = CourseCode::onlyTrashed()
                    ->where('id', $userSubscribe->course_code_id)
                    ->where('expire_at','>',Carbon::now())
                    ->pluck('course_id')
                    ->toArray();
                $userCoursesIds = array_merge($userCoursesIds, $courseCodes);
            }
            if ($userSubscribe->collection_code_id) {
                $collectionCodes = CollectionCode::onlyTrashed()
                    ->where('id', $userSubscribe->collection_code_id)
                    ->pluck('collection_id')
                    ->where('expire_at','>',Carbon::now())
                    ->toArray();

                $collectionCourses = CourseCollection::whereIn('collection_id', $collectionCodes)
                    ->pluck('course_id')
                    ->toArray();

                $userCoursesIds = array_merge($userCoursesIds, $collectionCourses);
            }
        }

        $courses = course::whereIn('id', $userCoursesIds)->get();
        $coursesData = []; // Create a separate array to store modified course data

        foreach ($courses as $course) {
            $courseData = $course->toArray(); // Convert the course object to an array for safe modifications
            $courseData['image'] = URL::to('Images/Courses', $course->poster); // Use Eloquent attribute directly

            $where = ['course_id' => $course->id];
            $courseContains = $this->publicRepository->ShowAll(CourseContain::class, $where)->get();

            $courseData['theoretical'] = [];
            $courseData['practical'] = [];

            foreach ($courseContains as $courseContain) {
                if ($courseContain->is_theoretical) {
                    $courseData['theoretical'][] = $courseContain;
                } else {
                    $courseData['practical'][] = $courseContain;
                }
            }

            $coursesData[] = $courseData; // Collect the modified course data
        }

        return \SuccessData(__('public.Show'), $coursesData);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseRequest $request)
    {
        $arr = Arr::only($request->validated(), ['name', 'ratio', 'poster', 'description', 'price', 'university_id', 'is_active', 'doctor_id']);
        $specialization=Arr::only($request->validated(),['year','chapter','specialization_id']);
        $path = 'Images/Courses/';
        $arr['poster'] = \uploadImage($arr['poster'], $path);

        $course =$this->publicRepository->Create(course::class, $arr);
        $specialization['course_id'] = $course->id;
        $this->publicRepository->Create(SpecializationCourse::class, $specialization);

        return \Success(__('public.Create'));
    }

    public function search(SearchRequest $request)
    {
        $searchArr = Arr::only($request->validated(), ['name']);
        $cities = course::where('name', 'LIKE', "%{$searchArr['name']}%")->orWhere('name', $searchArr['name'])->where('is_active', 1)->get();
        return \SuccessData(__('public.Show'), CourseResource::collection($cities));
    }

    public function find(CourseIdRequest $request)
    {
        $courseArr = Arr::only($request->validated(), ['courseId']);
        $course = $this->publicRepository->ShowAll(course::class, ['id' => $courseArr['courseId'], 'is_active' => 1])->first();
        if (!$course) {
            return \SuccessData(__('public.Show'), $course);
        }
        return \SuccessData(__('public.Show'), new  CourseResource($course));
    }

    public function adminSearch(SearchRequest $request)
    {
        $searchArr = Arr::only($request->validated(), ['name']);
        $cities = course::where('name', 'LIKE', "%{$searchArr['name']}%")->orWhere('name', $searchArr['name'])->get();
        return \SuccessData(__('public.Show'), SearchNameResource::collection($cities));
    }

    public function adminFind(CourseIdRequest $request)
    {
        $courseArr = Arr::only($request->validated(), ['courseId']);
        $course = $this->publicRepository->ShowById(course::class, $courseArr['courseId']);
        return \SuccessData(__('public.Show'), new  CourseResource($course));
    }

        public function toggleStatus(CourseIdRequest $request)
    {
        $courseArr = Arr::only($request->validated(), ['courseId']);
        $course = $this->publicRepository->ShowById(course::class, $courseArr['courseId']);
        $course->is_active = !$course->is_active;
        $course->save();
        return \Success(__('public.Show'));
    }

    public function Update(Request $request)
    {
        $course = $this->publicRepository->ShowById(course::class, $request->courseId);
        $course->is_active = !$course->is_active;
        $course->save();
        return \Success(__('public.Show'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseIdRequest $request)
    {
        $courseRequest = Arr::only($request->validated(), ['courseId']);
        $this->publicRepository->ActiveOrNot(course::class, $courseRequest['courseId']);
        return \Success(__('public.Delete'));
    }
}