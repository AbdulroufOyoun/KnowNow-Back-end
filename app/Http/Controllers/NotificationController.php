<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notification\NotificationRequest;
use App\Http\Requests\User\NotificationRequest as UserNotificationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;


class NotificationController extends Controller
{



    public function sendBroadcastToAllUsers(NotificationRequest $request)
    {
        $arr = Arr::only($request->validated(), ['title', 'description']);

        $firebase = (new Factory)->withServiceAccount(storage_path('app/firebase/athar-59955-firebase-adminsdk-fbsvc-b250dc4a93.json'));
        $messaging = $firebase->createMessaging();

        $userTokens = User::pluck('fcm_token')->filter()->all(); // Adjust table/column names

        $notification = Notification::create($arr['title'], $arr['description']);

        foreach ($userTokens as $token) {
            $message = CloudMessage::withTarget('token', $token)->withNotification($notification);
            try {
                $messaging->send($message);
            } catch (\Throwable $e) {
                // Optionally log or handle failed tokens
            }
        }

        return response()->json(['message' => 'Broadcast sent to all users']);
    }
}
