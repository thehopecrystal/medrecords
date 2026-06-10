<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $user = $this->currentUser();
        $notifications = Notification::where('user_id', $user->id)
            ->latest()->paginate(30);

        // Mark all unread as read
        Notification::where('user_id', $user->id)->whereNull('read_at')
            ->update(['read_at' => now()]);

        return Inertia::render('Patient/Notifications', compact('notifications'));
    }

    public function markRead(int $id)
    {
        $user = $this->currentUser();
        Notification::where('user_id', $user->id)->where('id', $id)
            ->update(['read_at' => now()]);
        return back();
    }

    public function destroy(int $id)
    {
        $user = $this->currentUser();
        Notification::where('user_id', $user->id)->where('id', $id)->delete();
        return back()->with('success', 'Notification deleted.');
    }
}
