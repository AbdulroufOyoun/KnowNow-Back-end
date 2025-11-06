<?php

namespace App\Http\Controllers;

use App\Http\Requests\University\UniversityRequest;
use App\Models\university;
use App\Repositories\PublicRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UniversityController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $universities = $this->publicRepository->ShowAll(university::class, ['is_active'=>1])->get();
        return \SuccessData(__('public.Show'),$universities);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(UniversityRequest $request)
    {
        $arr = Arr::only($request->validated(), ['name']);
        $this->publicRepository->Create(university::class, $arr);
        return \Success(__('public.Create'));
    }

    /**
     * Display the specified resource.
     */
    public function show(university $university)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(university $university)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, university $university)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(university $university)
    {
        //
    }
}
