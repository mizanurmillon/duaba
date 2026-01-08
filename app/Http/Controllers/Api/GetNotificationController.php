<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class GetNotificationController extends Controller
{
    use ApiResponse;

    public function getNotifications()
    {

        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $data = $user->notifications()->select('id', 'data', 'read_at', 'created_at')->latest()->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Notification not found', 200);
        }

        return $this->success($data, 'Notification fetched successfully', 200);
    }

    public function removeNotification($notificationId)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            return $this->error([], 'Notification not found', 404);
        }

        $notification->delete();

        return $this->success([], 'Notification deleted successfully', 200);
    }
}
