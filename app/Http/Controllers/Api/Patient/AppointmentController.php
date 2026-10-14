<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(): JsonResponse
    {
        $patient = $this->currentPatient();
        $upcoming = Appointment::with(['provider', 'facility'])
            ->where('patient_id', $patient->id)
            ->upcoming()->get();
        $past = Appointment::with(['provider', 'facility'])
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['completed', 'no_show', 'cancelled'])
            ->latest('appointment_date')->paginate(10);

        return response()->json(['upcoming' => $upcoming, 'past' => $past]);
    }

    /** Providers available for booking (the web booking-form data). */
    public function create(): JsonResponse
    {
        $providers = User::where('role', 'provider')->with('provider.facility')->get(['id', 'name', 'email']);

        return response()->json([
            'providers' => $providers,
            'types'     => ['consultation', 'followup', 'lab', 'dental', 'eye', 'procedure', 'other'],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $patient = $this->currentPatient();

        $data = $request->validate([
            'provider_id'      => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|date_format:H:i',
            'type'             => 'required|in:consultation,followup,lab,dental,eye,procedure,other',
            'reason'           => 'required|string|max:300',
            'notes'            => 'nullable|string|max:500',
        ]);

        $data['patient_id'] = $patient->id;
        $data['status']     = 'pending';

        $appointment = Appointment::create($data);

        Notification::send(
            $data['provider_id'],
            'appointment_request',
            'New Appointment Request',
            "{$patient->user->name} has requested a {$data['type']} appointment on " .
            \Carbon\Carbon::parse($data['appointment_date'])->format('d M Y') . " at " .
            \Carbon\Carbon::parse($data['appointment_time'])->format('g:i A') . ".",
            ['appointment_id' => $appointment->id, 'patient_id' => $patient->id]
        );

        return response()->json([
            'message'     => 'Appointment request submitted. You will be notified when confirmed.',
            'appointment' => $appointment->load(['provider', 'facility']),
        ], 201);
    }

    public function cancel(int $id, Request $request): JsonResponse
    {
        $patient     = $this->currentPatient();
        $appointment = Appointment::where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->findOrFail($id);

        $appointment->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $request->input('reason', ''),
            'cancelled_at'        => now(),
        ]);

        return response()->json(['message' => 'Appointment cancelled.', 'appointment' => $appointment]);
    }
}
