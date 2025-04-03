<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\CourseIdRequest;
use App\Http\Requests\Course\CourseRequest;
use App\Http\Requests\Public\SearchRequest;
use App\Http\Resources\Course\CourseAdminsResource;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Public\Search\SearchNameResource;
use App\Models\course;
use App\Repositories\PublicRepository;
use Illuminate\Support\Arr;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseRequest $request)
    {
        $arr = Arr::only($request->validated(), ['name', 'ratio', 'poster', 'description', 'price', 'university_id', 'is_active', 'doctor_id']);
        $path = 'Images/Courses/';
        $arr['poster'] = \uploadImage($arr['poster'], $path);
        $this->publicRepository->Create(course::class, $arr);
        return \Success(__('public.Create'));
    }

    public function search(SearchRequest $request)
    {
        $searchArr = Arr::only($request->validated(), ['name']);
        $cities = course::where('name', 'LIKE', "%{$searchArr['name']}%")->orWhere('name', $searchArr['name'])->where('is_active', 1)->get();
        return \SuccessData(__('public.Show'), SearchNameResource::collection($cities));
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
