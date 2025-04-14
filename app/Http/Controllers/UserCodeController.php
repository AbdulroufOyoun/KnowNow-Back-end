<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCode\UserCodeRequest;
use App\Models\CollectionCode;
use App\Models\CourseCode;
use App\Models\UserCode;
use App\Repositories\PublicRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use PhpParser\Node\Stmt\TryCatch;

class UserCodeController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(UserCodeRequest $request)
    {
        $arr = Arr::only($request->validated(), ['collection_code', 'course_code', 'item_id']);
        if ($arr['course_code'] && $arr['collection_code']) {
            \Success('There is no Code!!', false);
        }
        $arr['user_id'] = \Auth::user()->id;
        try {
            if ($arr['collection_code']) {
                $collection = $this->publicRepository->ShowAll(CollectionCode::class, ['code' => $arr['collection_code']])->first();
                if ($collection->id != $arr['item_id']) {
                    \Success('الرمز غير صحيح', false);
                }
                $arr['is_free'] = $collection->is_free;
                $arr['collection_code_id'] = $collection->id;
                $collection->delete();
            }
            if ($arr['course_code']) {
                $course = $this->publicRepository->ShowAll(CourseCode::class, ['code' => $arr['course_code']])->first();
                if ($course->id != $arr['item_id']) {
                    \Success('الرمز غير صحيح', false);
                }
                $arr['is_free'] = $course->is_free;
                $arr['course_code_id'] = $course->id;
                $course->delete();
            }
            $this->publicRepository->Create(UserCode::class, $arr);
            return \Success(__('public.Create'));
        } catch (\Throwable $th) {
            return \Success('الرمز مستخدم من قبل مستخدم اخر', false);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UserCode $userCode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserCode $userCode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserCode $userCode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserCode $userCode)
    {
        //
    }
}