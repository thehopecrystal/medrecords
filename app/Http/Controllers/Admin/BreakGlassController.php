<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BreakGlassEvent;
use App\Models\Notification;
use Illuminate\Http\Request;

class BreakGlassController extends Controller
{
    public function approve(Request $request, int $id)
    {
        $request->validate(['review_notes' => 'nullable|string|max:1000']);
        $event = BreakGlassEvent::with('patient.user')->findOrFail($id);
        $event->status       = 'approved';
        $event->reviewed_at  = now();
        $event->reviewed_by  = (int) auth()->id();
        $event->review_notes = $request->review_notes;
        $event->save();

        Notification::create([
            'user_id' => $event->provider_id,
            'type'    => 'break_glass_reviewed',
            'title'   => 'Break-glass access approved',
            'body'    => "Your emergency access to {$event->patient->user->name}'s record has been reviewed and approved.",
            'data'    => ['event_id' => $event->id],
        ]);

        return back()->with('success', 'Break-glass event approved.');
    }

    public function dispute(Request $request, int $id)
    {
        $request->validate(['review_notes' => 'nullable|string|max:1000']);
        $event = BreakGlassEvent::with('patient.user')->findOrFail($id);
        $event->status       = 'disputed';
        $event->reviewed_at  = now();
        $event->reviewed_by  = (int) auth()->id();
        $event->review_notes = $request->review_notes;
        $event->save();

        // Notify patient that their dispute is being investigated
        Notification::create([
            'user_id' => $event->patient->user_id,
            'type'    => 'break_glass_disputed',
            'title'   => 'Break-glass access disputed',
            'body'    => "The break-glass access to your record on {$event->created_at->format('d M Y')} is under investigation.",
            'data'    => ['event_id' => $event->id],
        ]);

        return back()->with('success', 'Break-glass event marked as disputed. Patient has been notified.');
    }

    public function close(int $id)
    {
        $event = BreakGlassEvent::findOrFail($id);
        $event->status      = 'closed';
        $event->reviewed_at = now();
        $event->reviewed_by = (int) auth()->id();
        $event->save();

        return back()->with('success', 'Break-glass event closed.');
    }
}
