<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\BreakGlassEvent;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BreakGlassController extends Controller
{
    private function authorizeEvent(BreakGlassEvent $event): void
    {
        /** @var Admin $admin */
        $admin = auth()->user();
        if ($admin->role === 'facility_admin') {
            abort_unless(
                $event->patient->facility_id === $admin->facility_id,
                403,
                'You may only review break-glass events for patients at your facility.'
            );
        }
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $event = BreakGlassEvent::with('patient.user', 'patient')->findOrFail($id);
        $this->authorizeEvent($event);
        $event->status       = 'approved';
        $event->reviewed_at  = now();
        $event->reviewed_by  = (int) auth()->id();
        $event->review_notes = 'Reviewed and approved by compliance.';
        $event->save();

        Notification::create([
            'user_id' => $event->provider_id,
            'type'    => 'break_glass_reviewed',
            'title'   => 'Break-glass access approved',
            'body'    => "Your emergency access to {$event->patient->user->name}'s record has been reviewed and approved.",
            'data'    => ['event_id' => $event->id],
        ]);

        return response()->json(['message' => 'Break-glass event approved.', 'event' => $event]);
    }

    public function dispute(Request $request, int $id): JsonResponse
    {
        $event = BreakGlassEvent::with('patient.user', 'patient')->findOrFail($id);
        $this->authorizeEvent($event);
        $event->status       = 'disputed';
        $event->reviewed_at  = now();
        $event->reviewed_by  = (int) auth()->id();
        $event->review_notes = 'Under investigation by compliance team.';
        $event->save();

        // Notify patient that their dispute is being investigated
        Notification::create([
            'user_id' => $event->patient->user_id,
            'type'    => 'break_glass_disputed',
            'title'   => 'Break-glass access disputed',
            'body'    => "The break-glass access to your record on {$event->created_at->format('d M Y')} is under investigation.",
            'data'    => ['event_id' => $event->id],
        ]);

        return response()->json(['message' => 'Break-glass event marked as disputed. Patient has been notified.', 'event' => $event]);
    }

    public function close(int $id): JsonResponse
    {
        $event = BreakGlassEvent::with('patient')->findOrFail($id);
        $this->authorizeEvent($event);
        $event->status      = 'closed';
        $event->reviewed_at = now();
        $event->reviewed_by = (int) auth()->id();
        $event->save();

        return response()->json(['message' => 'Break-glass event closed.', 'event' => $event]);
    }
}
