<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\RefillRequest;
use Illuminate\Http\Request;

class RefillRequestController extends Controller
{
    public function index()
    {
        $user     = $this->currentUser();
        $requests = RefillRequest::with(['prescription', 'patient.user'])
            ->where('provider_id', $user->id)
            ->latest()->paginate(20);

        $pendingCount = RefillRequest::where('provider_id', $user->id)
            ->where('status', 'pending')->count();

        return view('provider.refills', compact('requests', 'pendingCount'));
    }

    public function approve(int $id, Request $request)
    {
        $user   = $this->currentUser();
        $refill = RefillRequest::where('provider_id', $user->id)
            ->where('status', 'pending')->findOrFail($id);

        $refill->update([
            'status'         => 'approved',
            'provider_notes' => $request->input('notes', ''),
            'actioned_at'    => now(),
        ]);

        Notification::send(
            $refill->patient->user_id,
            'refill_approved',
            'Refill Request Approved',
            "Your refill request for {$refill->prescription->drug_name} has been approved. Please visit the pharmacy.",
            ['refill_id' => $refill->id]
        );

        return back()->with('success', 'Refill approved and patient notified.');
    }

    public function deny(int $id, Request $request)
    {
        $user   = $this->currentUser();
        $refill = RefillRequest::where('provider_id', $user->id)
            ->where('status', 'pending')->findOrFail($id);

        $refill->update([
            'status'         => 'denied',
            'provider_notes' => $request->input('notes', ''),
            'actioned_at'    => now(),
        ]);

        Notification::send(
            $refill->patient->user_id,
            'refill_denied',
            'Refill Request Denied',
            "Your refill request for {$refill->prescription->drug_name} was declined. Reason: " .
            ($request->input('notes', 'No reason provided.')),
            ['refill_id' => $refill->id]
        );

        return back()->with('success', 'Refill denied and patient notified.');
    }
}
