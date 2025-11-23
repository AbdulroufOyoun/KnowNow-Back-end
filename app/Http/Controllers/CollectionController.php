<?php

namespace App\Http\Controllers;

use App\Http\Requests\Collection\CollectionIdRequest;
use App\Http\Requests\Collection\CollectionRequest;
use App\Http\Requests\Public\SearchRequest;
use App\Http\Resources\Collection\CollectionAdminResource;
use App\Http\Resources\Collection\CollectionBarrenResource;
use App\Http\Resources\Collection\CollectionResource;
use App\Http\Resources\Public\Search\SearchNameResource;
use App\Models\Collection;
use App\Models\CollectionCode;
use App\Models\Course;
use App\Models\CourseCollection;
use App\Models\UserCode;
use App\Repositories\PublicRepository;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perPage = \returnPerPage();
        $courses = $this->publicRepository->ShowAll(Collection::class, ['is_active' => 1])->paginate($perPage);
        CollectionResource::Collection($courses);
        return \Pagination($courses);
    }
    public function adminIndex()
    {
        $perPage = \returnPerPage();
        $courses = $this->publicRepository->ShowAll(Collection::class, [])->paginate($perPage);
        CollectionAdminResource::Collection($courses);
        return \Pagination($courses);
    }
    public function checkSubscribe(CollectionIdRequest $request)
    {
        $collectionArr = Arr::only($request->validated(), ['collectionId']);
        $collectionCodes = $this->publicRepository->ShowAll(UserCode::class, ['user_id' => \Auth::user()->id, 'course_code_id' => null])->pluck('collection_code_id');
        $checkCollection = CollectionCode::whereIn('id', $collectionCodes)->where('collection_id', $collectionArr)->exists();

        return response()->json([
            'success' => true,
            'message' => __('public.Show'),
            'code' => 200,
            'data' => $checkCollection,
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CollectionRequest $request)
    {
        $arr = Arr::only($request->validated(), ['name', 'price', 'is_active']);
        $this->publicRepository->Create(Collection::class, $arr);
        return \Success(__('public.Create'));
    }

    public function search(SearchRequest $request)
    {
        $searchArr = Arr::only($request->validated(), ['name']);
        $cities = Collection::where('name', 'LIKE', "%{$searchArr['name']}%")->orWhere('name', $searchArr['name'])->where('is_active', 1)->get();
        return \SuccessData(__('public.Show'), SearchNameResource::collection($cities));
    }

    public function find(CollectionIdRequest $request)
    {
        $courseArr = Arr::only($request->validated(), ['collectionId']);
        $course = $this->publicRepository->ShowAll(Collection::class, ['id' => $courseArr['collectionId'], 'is_active' => 1])->first();
        if (!$course) {
            return \SuccessData(__('public.Show'), $course);
        }
        return \SuccessData(__('public.Show'), new  CollectionResource($course));
    }

    public function adminSearch(SearchRequest $request)
    {
        $searchArr = Arr::only($request->validated(), ['name']);
        $cities = Collection::where('name', 'LIKE', "%{$searchArr['name']}%")->orWhere('name', $searchArr['name'])->get();
        return \SuccessData(__('public.Show'), SearchNameResource::collection($cities));
    }

    public function adminFind(CollectionIdRequest $request)
    {
        $courseArr = Arr::only($request->validated(), ['collectionId']);
        $course = $this->publicRepository->ShowById(Collection::class, $courseArr['collectionId']);
        return \SuccessData(__('public.Show'), new  CollectionAdminResource($course));
    }

    public function toggle(CollectionIdRequest $request){
        $courseRequest = Arr::only($request->validated(), ['collectionId']);
        $collection =$this->publicRepository->ShowById(Collection::class,$courseRequest['collectionId']);
        $collection->is_active=!$collection->is_active;
        $collection->save();
        return \Success(__('public.Update'));

    }
    public function update(CollectionIdRequest $request){
        $courseRequest = Arr::only($request->validated(), ['collectionId']);
        $collection =$this->publicRepository->ShowById(Collection::class,$courseRequest['collectionId']);
        $collection->name = $request->name;
        $collection->price = $request->price;
        $collection->save();
        return \Success(__('public.Update'));
    }
    public function barren(Request $request){
        $arr['collectionId'] = $request->collectionId;
        $collectionCourse = Collection::where('id',$arr['collectionId'])->first();
        $collectionCourses = CourseCollection::where('collection_id',$arr['collectionId'])->get();
        $collectionsCount = CollectionCode::onlyTrashed()->where('collection_id',$arr['collectionId'])->whereBetween('deleted_at', [$request->startDate, $request->endDate])->where('is_free',0)->count();
        foreach ($collectionCourses as  $collectionCourse) {
            $course = Course::where('id',$collectionCourse->course_id)->first();
            $course->price = $collectionCourse->price;
            $totalMony= $collectionCourses->sum('price');
            $course['totalMony']=$totalMony;
            $course['doctorBarren']=$totalMony*($course->ratio/100);
            $collectionCourse['count']=$collectionsCount;
            $collectionCourse['totalMony']=$collectionsCount*$collectionCourse->price;
            $collectionCourse['doctorBarren']=($collectionsCount*$collectionCourse->price)*($course->ratio/100);
            $collections[] = $collectionCourse;
        }
        return \SuccessData(__('public.Show') , CollectionBarrenResource::collection($collectionCourses));

    }
    public function destroy(CollectionIdRequest $request)
    {
        $courseRequest = Arr::only($request->validated(), ['collectionId']);
        $this->publicRepository->DeleteById(Collection::class, $courseRequest['collectionId']);
        return \Success(__('public.Delete'));
    }
}
