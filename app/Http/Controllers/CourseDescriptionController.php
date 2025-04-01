<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseDescription\CourseDescriptionIdRequest;
use App\Http\Requests\CourseDescription\CourseDescriptionRequest;
use App\Http\Requests\CourseDetail\CourseDetailIdRequest;
use App\Http\Resources\CourseDescription\CourseDescriptionResource;
use App\Models\CourseDescription;
use App\Repositories\PublicRepository;
use Illuminate\Support\Arr;

class CourseDescriptionController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(CourseDetailIdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['courseDetailId']);
        $where = ['course_detail_id' => $arr['courseDetailId']];
        $courseDescription = $this->publicRepository->ShowAll(CourseDescription::class, $where)->get();
        return \SuccessData(__('public.Show'), CourseDescriptionResource::collection($courseDescription));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseDescriptionRequest $request)
    {
        $arr = Arr::only($request->validated(), ['description', 'course_detail_id']);
        $this->publicRepository->Create(CourseDescription::class, $arr);
        return \Success(__('public.Create'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseDescriptionIdRequest $request)
    {
        $courseRequest = Arr::only($request->validated(), ['courseDescriptionId']);
        $this->publicRepository->DeleteById(CourseDescription::class, $courseRequest['courseDescriptionId']);
        return \Success(__('public.Delete'));
    }
}
