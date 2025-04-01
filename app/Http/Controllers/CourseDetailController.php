<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\CourseIdRequest;
use App\Http\Requests\CourseDetail\CourseDetailIdRequest;
use App\Http\Requests\CourseDetail\CourseDetailRequest;
use App\Http\Resources\CourseDetail\CourseDetailResource;
use App\Models\CourseDetail;
use App\Repositories\PublicRepository;
use Illuminate\Support\Arr;

class CourseDetailController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(CourseIdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['courseId']);
        $where = ['course_id' => $arr['courseId']];
        $ads = $this->publicRepository->ShowAll(CourseDetail::class, $where)->get();
        return \SuccessData(__('public.Show'), CourseDetailResource::collection($ads));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseDetailRequest $request)
    {
        $arr = Arr::only($request->validated(), ['title', 'course_id']);
        $this->publicRepository->Create(CourseDetail::class, $arr);
        return \Success(__('public.Create'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseDetailIdRequest $request)
    {
        $courseRequest = Arr::only($request->validated(), ['courseDetailId']);
        $this->publicRepository->DeleteById(CourseDetail::class, $courseRequest['courseDetailId']);
        return \Success(__('public.Delete'));
    }
}
