<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BroadcastNotificationController extends Controller
{
    public function index(): JsonResponse
    {
        // One row per broadcast batch, most recent first (batch id in data JSON).
        $rows = Notification::where('type', 'admin_broadcast')
            ->latest()
            ->get()
            ->unique(fn($n) => $n->data['broadcast_id'] ?? $n->id)
            ->take(30)
            ->values();

        $rows = $rows->map(function ($n) {
            $bid = $n->data['broadcast_id'] ?? null;
            $n->recipient_count = $bid
                ? Notification::where('type', 'admin_broadcast')
                    ->whereJsonContains('data->broadcast_id', $bid)
                    ->count()
                : 1;
            $sentBy = User::find($n->data['sent_by'] ?? null);
            $n->sent_by_name = $sentBy?->name ?? 'System';
            return $n;
        });

        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'email', 'role']);

        return response()->json(['broadcasts' => $rows, 'users' => $users]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'audience'        => 'required|in:all,role,user',
            'role'            => 'required_if:audience,role|nullable|in:patient,provider,pharmacist,facility_admin,super_admin',
            'user_identifier' => 'required_if:audience,user|nullable|string|max:120',
            'title'           => 'required|string|max:120',
            'body'            => 'required|string|max:2000',
        ]);

        $recipients = $this->resolveAudience($request);

        if ($recipients->isEmpty()) {
            return response()->json(['message' => 'No active users matched that identifier.', 'errors' => ['user_identifier' => ['No active users matched that identifier.']]], 422);
        }

        $broadcastId = (string) Str::uuid();

        foreach ($recipients as $user) {
            Notification::send(
                $user->id,
                'admin_broadcast',
                $request->title,
                $request->body,
                ['broadcast_id' => $broadcastId, 'sent_by' => auth()->id()]
            );
        }

        return response()->json(['message' => "Notification dispatched to {$recipients->count()} user(s).", 'recipients' => $recipients->count()], 201);
    }

    private function resolveAudience(Request $request)
    {
        return match ($request->audience) {
            'all'  => User::where('is_active', true)->get(),
            'role' => User::where('role', $request->role)->where('is_active', true)->get(),
            'user' => User::where('is_active', true)
                ->where(function ($q) use ($request) {
                    $id = $request->user_identifier;
                    $q->where('email', $id)
                      ->orWhereHas('patient', fn($p) => $p->where('med_id', $id));
                })
                ->get(),
            default => collect(),
        };
    }
}
