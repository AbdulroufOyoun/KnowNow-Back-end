<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Repositories\PublicRepository;
use Illuminate\Http\Request;

class MediaController extends Controller
{
        public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \SuccessData(__('public.Show'), Media::get());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Media $media)
    {
        $media->url = $request->url;
        $media->save();
        return \Success(__('public.Show'));
    }

}