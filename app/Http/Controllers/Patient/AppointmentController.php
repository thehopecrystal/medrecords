<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $patient = $this->currentPatient();
        $upcoming = Appointment::with(['provider', 'facility'])
            ->where('patient_id', $patient->id)
            ->upcoming()->get();
        $past = Appointment::with(['provider', 'facility'])
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['completed', 'no_show', 'cancelled'])
            ->latest('appointment_date')->paginate(10);

        return view('patient.appointments.index', compact('patient', 'upcoming', 'past'));
    }

    public function create()
    {
        $providers = User::where('role', 'provider')
            ->with('provider.facility')
            ->get();
        return view('patient.appointments.create', compact('providers'));
    }

    public function store(Request $request)
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

        // Notify provider
        Notification::send(
            $data['provider_id'],
            'appointment_request',
            'New Appointment Request',
            "{$patient->user->name} has requested a {$data['type']} appointment on " .
            \Carbon\Carbon::parse($data['appointment_date'])->format('d M Y') . " at " .
            \Carbon\Carbon::parse($data['appointment_time'])->format('g:i A') . ".",
            ['appointment_id' => $appointment->id, 'patient_id' => $patient->id]
        );

        return redirect()->route('patient.appointments')
            ->with('success', 'Appointment request submitted. You will be notified when confirmed.');
    }

    public function cancel(int $id, Request $request)
    {
        $patient     = $this->currentPatient();
        $appointment = Appointment::where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->findOrFail($id);

        $appointment->update([
            'status'               => 'cancelled',
            'cancellation_reason'  => $request->input('reason', ''),
            'cancelled_at'         => now(),
        ]);

        return back()->with('success', 'Appointment cancelled.');
    }
}
