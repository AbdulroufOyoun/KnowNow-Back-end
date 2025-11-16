<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notification\NotificationRequest;
use App\Models\User;
use Illuminate\Support\Arr;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;


class NotificationController extends Controller
{



    public function sendBroadcastToAllUsers(NotificationRequest $request)
    {
        $arr = Arr::only($request->validated(), ['title', 'description','university_id']);
        $firebase = (new Factory)->withServiceAccount(storage_path('app/firebase/athar-59955-firebase-adminsdk-fbsvc-2d9d820f40.json'));
        $messaging = $firebase->createMessaging();
        if ($arr['university_id']==0) {
            $userTokens = User::pluck('fcm_token')->filter()->all();
        }else{
            $userTokens = User::where('university_id',$arr['university_id'])->pluck('fcm_token')->filter()->all();
        }
        $notification = Notification::create($arr['title'], $arr['description']);
        foreach ($userTokens as $token) {
            $message = CloudMessage::withTarget('token', $token)->withNotification($notification)->withAndroidConfig([
            'notification' => [
                'sound' => 'default',
                'channel_id' => 'default',
            ],
            'priority' => 'high',
            ]);
            try {
                $messaging->send($message);
            } catch (\Throwable $e) {
                \Log::warning('Failed to send notification', [
                    'token' => substr($token, 0, 20) . '...',
                    'error' => $e->getMessage()
                ]);
            }
        }
        return response()->json(['message' => 'Broadcast sent to all users']);
    }
}
