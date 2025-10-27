<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\UserIdRequest;
use App\Http\Resources\User\StudentResource;
use App\Models\User;
use App\Repositories\PublicRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    public function students()
    {
        $perPage = \returnPerPage();
        $users = User::doesntHave('roles')->paginate($perPage);
        StudentResource::collection($users);
        return \Pagination($users);
    }
            public function toggleStatus(UserIdRequest $request)
    {
        $courseArr = Arr::only($request->validated(), ['userId']);
        $course = $this->publicRepository->ShowById(User::class, $courseArr['userId']);
        $course->is_active = !$course->is_active;
        $course->save();
        return \Success(__('public.Show'));
    }


}
