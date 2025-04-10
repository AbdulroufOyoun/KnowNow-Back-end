<?php

namespace App\Http\Controllers;

use App\Http\Requests\Collection\CollectionIdRequest;
use App\Http\Requests\CollectionCode\CollectionCodeIdRequest;
use App\Http\Requests\CollectionCode\CollectionCodeRequest;
use App\Http\Resources\CollectionCode\CollectionCodeResource;
use App\Models\CollectionCode;
use App\Repositories\PublicRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CollectionCodeController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    public function index()
    {
        $perPage = \returnPerPage();
        $where = ['created_by' => \Auth::user()->id];
        $courses = $this->publicRepository->ShowAll(CollectionCode::class, $where)->paginate($perPage);
        CollectionCodeResource::Collection($courses);
        return \Pagination($courses);
    }

    public function indexAll()
    {
        $perPage = \returnPerPage();
        $courses = $this->publicRepository->ShowAll(CollectionCode::class, [])->paginate($perPage);
        CollectionCodeResource::Collection($courses);
        return \Pagination($courses);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CollectionCodeRequest $request)
    {
        $arr = Arr::only($request->validated(), ['collection_id', 'is_free', 'expire_at']);
        $arr['created_by'] = \Auth::user()->id;
        $arr['code'] = Str::upper(Str::random(3)) . Str::lower(Str::random(2)) . rand(0, 9);
        $this->publicRepository->Create(CollectionCode::class, $arr);
        return \Success(__('public.Create'));
    }

    /**
     * Display the specified resource.
     */
    public function show(CollectionIdRequest $request)
    {
        $perPage = \returnPerPage();
        $arr = Arr::only($request->validated(), ['collectionId']);
        $where = ['created_by' => \Auth::user()->id, 'collection_id' => $arr['collectionId']];
        $courses = $this->publicRepository->ShowAll(CollectionCode::class, $where)->paginate($perPage);
        CollectionCodeResource::Collection($courses);
        return \Pagination($courses);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CollectionCodeIdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['codeId']);
        $this->publicRepository->DeleteById(CollectionCode::class, $arr['codeId']);
        return \Success(__('public.Delete'));
    }
}