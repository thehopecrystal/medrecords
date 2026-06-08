<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Notification;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $provider = $this->currentProvider();

        $date = $request->input('date', now()->toDateString());

        $appointments = Appointment::with(['patient.user', 'facility'])
            ->forProvider($provider->user_id)
            ->where('appointment_date', $date)
            ->orderBy('appointment_time')
            ->get();

        $upcoming = Appointment::with(['patient.user'])
            ->forProvider($provider->user_id)
            ->upcoming()
            ->limit(15)
            ->get();

        $pendingCount = Appointment::forProvider($provider->user_id)
            ->where('status', 'pending')->count();

        return view('provider.appointments', compact('appointments', 'upcoming', 'date', 'pendingCount'));
    }

    public function confirm(int $id)
    {
        $provider    = $this->currentProvider();
        $appointment = Appointment::forProvider($provider->user_id)
            ->where('status', 'pending')->findOrFail($id);

        $appointment->update(['status' => 'confirmed']);

        Notification::send(
            $appointment->patient->user_id,
            'appointment_confirmed',
            'Appointment Confirmed',
            "Your {$appointment->type} appointment on " .
            $appointment->appointment_date->format('d M Y') . " at " .
            \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') . " has been confirmed.",
            ['appointment_id' => $appointment->id]
        );

        return back()->with('success', 'Appointment confirmed and patient notified.');
    }

    public function complete(int $id)
    {
        $provider    = $this->currentProvider();
        $appointment = Appointment::forProvider($provider->user_id)
            ->where('status', 'confirmed')->findOrFail($id);

        $appointment->update(['status' => 'completed']);

        return back()->with('success', 'Appointment marked as completed.');
    }

    public function noShow(int $id)
    {
        $provider    = $this->currentProvider();
        $appointment = Appointment::forProvider($provider->user_id)
            ->whereIn('status', ['confirmed', 'pending'])->findOrFail($id);

        $appointment->update(['status' => 'no_show']);

        return back()->with('success', 'Appointment marked as no-show.');
    }
}
