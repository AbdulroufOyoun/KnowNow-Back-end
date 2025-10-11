<?php

namespace App\Http\Controllers;

use App\Http\Requests\Specialization\ShowSpecializationRequest;
use App\Http\Requests\Specialization\SpecializationIdRequest;
use App\Http\Requests\University\UniversityIdRequest;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Specialization\YearCoursesResource;
use App\Models\Specialization;
use App\Models\SpecializationCourse;
use App\Repositories\PublicRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SpecializationCourseController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(ShowSpecializationRequest $request )
    {
        $arr = Arr::only($request->validated(), ['year','chapter','specialization_id']);
        $courses = SpecializationCourse::where(['year'=>$arr['year'],'chapter'=>$arr['chapter'],'specialization_id'=>$arr['specialization_id']])
            ->with('Course')
            ->get()
            ->pluck('Course');
        return \SuccessData(__('public.Show'), CourseResource::collection($courses) );
    }

        public function year(SpecializationIdRequest $request ){
            $arr = Arr::only($request->validated(), ['specialization_id']);
$years = SpecializationCourse::where('specialization_id', $arr['specialization_id'])
  ->select('year')
  ->distinct()
  ->get()
  ->map(function ($item) {
      return ['year' => $item->year];
  });
          return \SuccessData(__('public.Show'), $years );
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SpecializationCourse $specializationCourse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpecializationCourse $specializationCourse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SpecializationCourse $specializationCourse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpecializationCourse $specializationCourse)
    {
        //
    }
}