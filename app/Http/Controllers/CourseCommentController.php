<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\CourseIdRequest;
use App\Http\Requests\CourseComments\CourseCommentIdRequest;
use App\Http\Requests\CourseComments\CourseCommentRequest;
use App\Http\Requests\CourseContain\CourseContainIdRequest;
use App\Http\Resources\CourseComments\CourseCommentResource;
use App\Models\CourseComment;
use App\Repositories\PublicRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CourseCommentController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    /**
     * Display a listing of the resource.
     */
    public function index(CourseContainIdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['courseContainId']);
        $mainWhere = ['video_id' => $arr['courseContainId'], 'comment_id' => null];
        $mainComments = $this->publicRepository->ShowAll(CourseComment::class, $mainWhere)->get();
        foreach ($mainComments as $mainComment) {
            if (!isset($mainComment['subComments']) || !is_array($mainComment['subComments'])) {
            }
        }
        return \SuccessData(__('public.Show'), CourseCommentResource::collection($mainComments));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseCommentRequest $request)
    {
        $arr = Arr::only($request->validated(), ['comment', 'sub_comment', 'video_id', 'comment_id']);
        $user = \Auth::user();
        $arr['user_id'] = $user->id;
        $protectWhere = ['user_id' => $user->id, 'comment_id' => $arr['comment_id']];
        $userRoles = count($user->getRoleNames());
        $checkComments = $this->publicRepository->ShowAll(CourseComment::class, $protectWhere)->get();
        if (count($checkComments) >= 4 && $userRoles == 0) {
            return \Success('لا يمكنك اضافة المزيد من التعليقات', false);
        }
        if ($arr['comment_id'] != null) {
            $where = ['id' => $arr['comment_id']];
            $comment = $this->publicRepository->ShowAll(CourseComment::class, $where)->first();
            if ($comment->user_id == $user->id || $userRoles == 0) {
                $this->publicRepository->Create(CourseComment::class, $arr);
                return \Success(__('public.Create'));
            } else {
                return \Success(__('public.needPermission'), false);
            }
        }

        $this->publicRepository->Create(CourseComment::class, $arr);
        return \Success(__('public.Create'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseCommentIdRequest $request)
    {
        $commentRequest = Arr::only($request->validated(), ['commentId']);

        $user = \Auth::user();
        $comment = $this->publicRepository->ShowAll(CourseComment::class, ['id' => $commentRequest['commentId']])->first();
        if ($user->id == $comment->user_id || count($user->getRoleNames()) != 0) {
            $subComments = $this->publicRepository->ShowAll(CourseComment::class, ['comment_id' => $commentRequest['commentId']])->get();
            if (count($subComments) > 0) {
                foreach ($subComments as $subComment) {
                    $this->publicRepository->DeleteById(CourseComment::class, $subComment['id']);
                }
            }
            $this->publicRepository->DeleteById(CourseComment::class, $commentRequest['commentId']);
            return \Success(__('public.Delete'));
        }
        return \Success(__('public.needPermission'), false);
    }
}