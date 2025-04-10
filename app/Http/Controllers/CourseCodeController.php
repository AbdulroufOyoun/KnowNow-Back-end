<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\CourseIdRequest;
use App\Http\Requests\CourseCode\CourseCodeIdRequest;
use App\Http\Requests\CourseCode\CourseCodeRequest;
use App\Http\Resources\CourseCode\CourseCodeResource;
use App\Models\CourseCode;
use App\Repositories\PublicRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CourseCodeController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    public function index()
    {
        $perPage = \returnPerPage();
        $where = ['created_by' => \Auth::user()->id];
        $courses = $this->publicRepository->ShowAll(CourseCode::class, $where)->paginate($perPage);
        CourseCodeResource::Collection($courses);
        return \Pagination($courses);
    }

    public function indexAll()
    {
        $perPage = \returnPerPage();
        $courses = $this->publicRepository->ShowAll(CourseCode::class, [])->paginate($perPage);
        CourseCodeResource::Collection($courses);
        return \Pagination($courses);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseCodeRequest $request)
    {
        $arr = Arr::only($request->validated(), ['course_id', 'is_free', 'expire_at']);
        $arr['created_by'] = \Auth::user()->id;
        $arr['code'] = Str::upper(Str::random(3)) . Str::lower(Str::random(2)) . rand(0, 9);
        $this->publicRepository->Create(CourseCode::class, $arr);
        return \Success(__('public.Create'));
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseIdRequest $request)
    {
        $perPage = \returnPerPage();
        $arr = Arr::only($request->validated(), ['courseId']);
        $where = ['created_by' => \Auth::user()->id, 'course_id' => $arr['courseId']];
        $courses = $this->publicRepository->ShowAll(CourseCode::class, $where)->paginate($perPage);
        CourseCodeResource::Collection($courses);
        return \Pagination($courses);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseCodeIdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['codeId']);
        $this->publicRepository->DeleteById(CourseCode::class, $arr['codeId']);
        return \Success(__('public.Delete'));
    }
}
