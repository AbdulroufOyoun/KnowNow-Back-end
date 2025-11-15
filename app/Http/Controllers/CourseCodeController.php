<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\CourseIdRequest;
use App\Http\Requests\CourseCode\CourseCodeIdRequest;
use App\Http\Requests\CourseCode\CourseCodeRequest;
use App\Http\Resources\Course\CourseBarrenResource;
use App\Http\Resources\CourseCode\CourseCodeResource;
use App\Models\Collection;
use App\Models\CollectionCode;
use App\Models\Course;
use App\Models\CourseCode;
use App\Models\CourseCollection;
use App\Repositories\PublicRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

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

    public function courseSubscriptions(CourseIdRequest $request)
    {
        $perPage = \returnPerPage();
        $arr = Arr::only($request->validated(), ['courseId']);
         $courses = CourseCode::onlyTrashed()->where('course_id',$arr['courseId'])->paginate($perPage);
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
        // $arr['code'] = Str::upper(Str::random(3)) . Str::lower(Str::random(2)) . rand(0, 9);
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $random = substr(str_shuffle(str_repeat($characters, 8)), 0, 8);
        $arr['code']= $random;
        $course = $this->publicRepository->ShowById(Course::class,$arr['course_id']);
        $arr['price']=$course->price;
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
        $courses = $this->publicRepository->ShowAll(CourseCode::class, $where) ->orderByRaw('expire_at IS NULL DESC')->orderBy('created_at', 'desc')

        ->paginate($perPage);
        CourseCodeResource::Collection($courses);
        return \Pagination($courses);
    }

    public function barren(Request $request){
        $results = CourseCode::onlyTrashed()->where('course_id',$request->course_id)->where('is_free',0)->whereBetween('deleted_at', [$request->startDate, $request->endDate]);
        $barren['count']= $results->count();
        $totalMony= $results->sum('price');
        $barren['totalMony']=$totalMony;
        $doctorRatio= Course::where('id',$request->course_id)->pluck('ratio')[0];
        $barren['doctorBarren']=$totalMony*($doctorRatio/100);
        return \SuccessData(__('public.Delete'),$barren);
    }

    public function doctorBarren(Request $request){
        $courses = $this->publicRepository->ShowAll(Course::class, ['doctor_id'=>$request->doctorId])->get();
        $collection=[];
        foreach ($courses as $key => $course) {
            $results = CourseCode::onlyTrashed()->where('course_id',$course->id)->where('is_free',0)->whereBetween('deleted_at', [$request->startDate, $request->endDate])->get();
            $count = $results->count();
            if ($count === 0) {
                unset($courses[$key]);
            }else{
                    $course['count']= $count;
                    $totalMony= $results->sum('price');
                    $course['totalMony']=$totalMony;
                    $course['doctorBarren']=$totalMony*($course->ratio/100);
                }
            $collectionCourses = CourseCollection::where('course_id',$course->id)->get();
            if($collectionCourses->isNotEmpty()){

            foreach ($collectionCourses as $collectionCourse) {

                $collection = Collection::where('id',$collectionCourse->collection_id)->first();
                    # code...
                $collectionCourse['collection']= $collection;
            $collectionsCount = CollectionCode::onlyTrashed()->where('collection_id',$collection->id)->whereBetween('deleted_at', [$request->startDate, $request->endDate])->where('is_free',0)->count();
                $collectionCourse['count']=$collectionsCount;
                $collectionCourse['totalMony']=$collectionsCount*$collectionCourse->price;
                $collectionCourse['doctorBarren']=($collectionsCount*$collectionCourse->price)*($course->ratio/100);
                $collections[] = $collectionCourse;

            }

            }
        }
        // return $collections;
        $barren['collections']=$collections;
        $barren['courses']=CourseBarrenResource::collection($courses);
        return \SuccessData(__('public.Show') , $barren);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseCodeIdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['codeId']);
        $this->publicRepository->ShowById(CourseCode::class, $arr['codeId'])->forceDelete();
        return \Success(__('public.Delete'));
    }
}