<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Prescription;
use App\Models\RefillRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefillRequestController extends Controller
{
    public function index(): JsonResponse
    {
        $patient  = $this->currentPatient();
        $requests = RefillRequest::with(['prescription', 'provider'])
            ->where('patient_id', $patient->id)
            ->latest()->paginate(20);

        return response()->json(['requests' => $requests]);
    }

    public function store(Request $request): JsonResponse
    {
        $patient = $this->currentPatient();

        $request->validate([
            'prescription_id' => 'required|integer',
            'reason'          => 'nullable|string|max:500',
        ]);

        $prescription = Prescription::where('patient_id', $patient->id)
            ->findOrFail($request->prescription_id);

        // Block duplicate pending request
        $existing = RefillRequest::where('prescription_id', $prescription->id)
            ->where('status', 'pending')->exists();

        if ($existing) {
            return response()->json(['message' => 'A refill request for this prescription is already pending.'], 422);
        }

        $refill = RefillRequest::create([
            'prescription_id' => $prescription->id,
            'patient_id'      => $patient->id,
            'provider_id'     => $prescription->prescribed_by,
            'reason'          => $request->reason,
            'status'          => 'pending',
        ]);

        Notification::send(
            $prescription->prescribed_by,
            'refill_request',
            'Prescription Refill Request',
            "{$patient->user->name} has requested a refill for {$prescription->drug_name}.",
            ['refill_id' => $refill->id, 'patient_id' => $patient->id]
        );

        return response()->json(['message' => 'Refill request sent to your provider.', 'refill_request' => $refill], 201);
    }
}
