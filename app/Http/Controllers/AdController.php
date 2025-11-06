<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ad\AdRequest;
use App\Http\Resources\Ad\AdResource;
use App\Models\Ad;
use App\Repositories\PublicRepository;
use Illuminate\Support\Arr;

class AdController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \SuccessData(__('public.Show'),AdResource::collection(Ad::get()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['image']);
        $path = 'Images/Ads/';
        $arr['image'] = \uploadImage($arr['image'], $path);
        $this->publicRepository->Create(Ad::class, $arr);
        return \Success(__('public.Create'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ad $ad)
    {
        $ad->delete();
        return \Success(__('public.Delete'));
    }
}
