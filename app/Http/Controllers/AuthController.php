<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\CreateDoctorRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Requests\Auth\UpdatefcmTokenRequest;
use App\Http\Requests\Auth\UserIdRequest;
use App\Http\Requests\Notification\UpdatefcmtokenRequest as NotificationUpdatefcmtokenRequest;
use App\Http\Requests\User\UpdatefcmTokenRequest as UserUpdatefcmTokenRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\User\DoctorResource;
use App\Http\Resources\User\StudentResource;
use App\Repositories\PublicRepository;

use App\Models\User;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(public PublicRepository $publicRepository) {}

    public function login(LoginRequest $request)
    {
        $arr = Arr::only($request->validated(), ['email', 'password', 'mobile_uuid']);
        $where = ['email' => $arr['email']];
        $user = $this->publicRepository->ShowAll(User::class, $where)->first();
        if (!Hash::check($arr['password'], $user->password)) {
            throw ValidationException::withMessages([__('public.password_wrong')]);
        }
        if ($user->mobile_uuid == null && $arr['mobile_uuid'] != null) {
            $user->mobile_uuid = $arr['mobile_uuid'];
            $user->save();
        }
        $user['role'] = $user->getRoleNames();
        if (count($user['role']) > 0) {
            $user['role'] = $user['role'][0];
        } else {
            $user['role'] = null;
        }
        $user->tokens()->delete();
        if (($user['role'] == null && $arr['mobile_uuid'] != $user->mobile_uuid) || ($user->mobile_uuid == null && $arr['mobile_uuid'] == null) || ($user->is_active == 0)) {
            $arr['mobile_uuid'];
            $disActiveAccount = $this->publicRepository->ShowAll(User::class, $where)->first();
            $disActiveAccount->is_active = 0;
            $disActiveAccount->save();
            return response()->json([
                'success' => false,
                'message' => 'تم حظر الحساب لتسجيل الدخول من اكثر من جيهاز',
                'code' => 422,
                'data' => null,
            ], 422);
        }
        $user['token'] = $user->createToken('authToken');
        return \SuccessData(__('public.login'), new LoginResource($user));
    }
    public function SignUp(SignUpRequest $request)
    {
        $userArr = Arr::only(
            $request->validated(),
            ['email', 'password', 'name', 'phone', 'mobile_uuid', 'university_id']
        );
        try {
            $user = $this->publicRepository->Create(User::class, $userArr);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                // Duplicate entry
                return response()->json([
                    'success' => false,
                    'message' => 'هذا البريد الالكتروني مستخدم من قبل',
                    'code' => 409,
                    'data' => null,

                ], 409);
            } else {
                return response()->json(['error' => 'An error occurred'], 500);
            }
        }
        $user['role'] = null;
        $user['token'] = $user->createToken('authToken');
        return \SuccessData(__('public.login'), new LoginResource($user));
    }
    public function Doctors()
    {
        $perPage = \returnPerPage();
        $users = User::role('doctor')->paginate($perPage);
        DoctorResource::collection($users);
        return \Pagination($users);
    }
    public function makeDoctor(CreateDoctorRequest $request)
    {
        $userArr = Arr::only($request->validated(), ['email',  'name', 'phone']);
        try {
            $userArr['password'] = 123456789;
            $user = $this->publicRepository->Create(User::class, $userArr);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا البريد الالكتروني مستخدم من قبل',
                    'code' => 409,
                    'data' => null,
                ], 409);
            } else {
                return response()->json(['error' => 'An error occurred'], 500);
            }
        }
        $user->assignRole('doctor');
        return \Success(__('public.Show'));
    }
    public function DeleteAdmin(UserIdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['userId']);
        $user = $this->publicRepository->ShowById(User::class, $arr['userId']);
        $user->tokens()->where('scopes')->delete();
        $user->delete();
        return \Success(__('public.Delete'));
    }
    public function Logout()
    {
        $user = auth()->user();
        $user->tokens()->delete();
        return \Success(__('public.logout'));
    }

    public function UpdateToken(NotificationUpdatefcmtokenRequest $request)
    {
        $userArr = Arr::only(
            $request->validated(),
            ['fcm_token']
        );
        $user = \Auth::user();
        $user->fcm_token = $userArr['fcm_token'];
        $user->save();
        return \Success(__('public.Show'));
    }



    public function loginError()
    {
        return response()->json([
            'success' => false,
            'message' => 'تحتاج الى تسجيل دخول قبل القيام بهذا الأمر ',
            'code' => 403,
        ], 403);
    }

    public function SelfChangePassword(ChangePasswordRequest $request)
    {
        $arr = Arr::only($request->validated(), ['old_password', 'new_password']);
        $person = \auth()->user();
        $model = get_class($person);
        if (!Hash::check($arr['old_password'], $person->password)) {
            throw ValidationException::withMessages([__('public.authFailed')]);
        }
        $this->publicRepository->update($model, $person->id, ['password' => $arr['new_password']]);
        return \Success(__('public.password_update'));
    }
}
