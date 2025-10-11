<?php

namespace App\Http\Controllers;

use App\Http\Requests\Specialization\SpecializationRequest;
use App\Http\Requests\University\UniversityIdRequest;
use App\Models\Specialization;
use App\Repositories\PublicRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SpecializationController extends Controller
{
        public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(UniversityIdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['university_id']);
        $where = ['university_id' => $arr['university_id']];
        $ads = $this->publicRepository->ShowAll(Specialization::class, $where)->get();
        return \SuccessData(__('public.Show'), $ads);
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
    public function store(SpecializationRequest $request)
    {
        $arr = Arr::only($request->validated(), ['name', 'university_id']);
        $this->publicRepository->Create(Specialization::class, $arr);
        return \Success(__('public.Create'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialization $specialization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specialization $specialization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Specialization $specialization)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specialization $specialization)
    {
        //
    }
}
