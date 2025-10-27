<?php

namespace App\Http\Controllers;

use App\Http\Requests\Collection\CollectionIdRequest;
use App\Http\Requests\courseCollection\CourseCollectionIdRequest;
use App\Http\Requests\courseCollection\CourseCollectionRequest;
use App\Http\Resources\CollectionCourses\AdminCollectionCoursesResource;
use App\Http\Resources\CollectionCourses\CollectionCoursesResource;
use App\Models\Collection;
use App\Models\course;
use App\Models\CourseCollection;
use App\Repositories\PublicRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CourseCollectionController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perPage = \returnPerPage();
        $collections = $this->publicRepository->ShowAll(Collection::class, ['is_active' => 1])->paginate($perPage);
        CollectionCoursesResource::collection($collections);
        return \Pagination($collections);
    }
    public function adminIndex(CollectionIdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['collectionId']);
        $collections = $this->publicRepository->ShowAll(CourseCollection::class, ['collection_id' => $arr['collectionId']])->get();
        return \SuccessData(__('public.Create'),AdminCollectionCoursesResource::collection($collections)) ;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseCollectionRequest $request)
    {
        $arr = Arr::only($request->validated(), ['collection_id', 'course_id','price']);
        $this->publicRepository->Create(CourseCollection::class, $arr);
        return \Success(__('public.Create'));
    }

    public function update(Request $request){
         $course=$this->publicRepository->ShowById(CourseCollection::class,$request->collectionCourseId);
        $course->price = $request->price;
        $course->save();
        return \Success(__('public.Update'));

    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseCollectionIdRequest $request)
    {
        $courseRequest = Arr::only($request->validated(), ['collectionCourseId']);
        $this->publicRepository->DeleteById(CourseCollection::class, $courseRequest['collectionCourseId']);
        return \Success(__('public.Delete'));
    }
}
