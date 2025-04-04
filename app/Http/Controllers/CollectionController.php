<?php

namespace App\Http\Controllers;

use App\Http\Requests\Collection\CollectionIdRequest;
use App\Http\Requests\Collection\CollectionRequest;
use App\Http\Requests\Public\SearchRequest;
use App\Http\Resources\Collection\CollectionAdminResource;
use App\Http\Resources\Collection\CollectionResource;
use App\Http\Resources\Public\Search\SearchNameResource;
use App\Models\Collection;
use App\Repositories\PublicRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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
    public function destroy(CollectionIdRequest $request)
    {
        $courseRequest = Arr::only($request->validated(), ['collectionId']);
        $this->publicRepository->ActiveOrNot(Collection::class, $courseRequest['collectionId']);
        return \Success(__('public.Delete'));
    }
}
