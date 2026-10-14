<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $user = $this->currentUser();
        $notifications = Notification::where('user_id', $user->id)
            ->latest()->paginate(30);

        // Mark all unread as read
        Notification::where('user_id', $user->id)->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['notifications' => $notifications]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = $this->currentUser();
        Notification::where('user_id', $user->id)->where('id', $id)->delete();
        return response()->json(['message' => 'Notification deleted.']);
    }
}
