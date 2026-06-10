<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Dispense;
use App\Models\Prescription;
use App\Services\NhiaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DispenseController extends Controller
{
    public function dashboard()
    {
        $recentDispenses = Dispense::where('pharmacist_id', auth()->id())
            ->with(['prescription.patient.user', 'prescription.prescribedBy', 'facility'])
            ->latest()
            ->take(15)
            ->get();

        return Inertia::render('Pharmacy/Dashboard', compact('recentDispenses'));
    }

    public function lookup(Request $request)
    {
        $rxId = $request->input('rx_id', '');
        $prescription = null;

        if ($rxId) {
            $prescription = Prescription::where('rx_id', $rxId)
                ->with([
                    'patient.user',
                    'patient.allergies',
                    'patient.activePrescriptions',
                    'patient.labResults',
                    'prescribedBy',
                    'dispenses',
                    'encounter',
                ])
                ->first();
        }

        $nhia = null;
        if ($prescription) {
            $phone = $prescription->patient->user->phone ?? '';
            $nhia  = (new NhiaService())->checkEligibility($phone);
        }

        return Inertia::render('Pharmacy/Lookup', compact('prescription', 'rxId', 'nhia'));
    }

    public function dispense(Request $request, int $prescriptionId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $prescription = Prescription::with('patient')->findOrFail($prescriptionId);

        $pharmacist = $this->currentUser();
        $facilityId = $pharmacist->provider?->facility_id;
        abort_if($facilityId === null, 422, 'Pharmacist is not linked to a facility.');

        // Scope check: only dispense prescriptions issued by a provider at this facility.
        abort_if(
            $prescription->facility_id && $prescription->facility_id !== $facilityId,
            403,
            'This prescription was not issued at your facility.'
        );

        abort_if(
            $prescription->status === 'dispensed' || Dispense::where('prescription_id', $prescription->id)->exists(),
            422,
            'This prescription has already been dispensed and cannot be dispensed again.'
        );

        $dispense = Dispense::create([
            'prescription_id' => $prescription->id,
            'pharmacist_id'   => $pharmacist->id,
            'facility_id'     => $facilityId,
            'quantity'        => $request->quantity,
            'batch_lot'       => $request->batch_lot,
            'price'           => $request->price,
            'payment_method'  => $request->payment_method,
            'counseling_notes' => $request->counseling_notes,
        ]);

        $prescription->update(['status' => 'dispensed']);

        AuditLog::record(
            $prescription->patient_id,
            $pharmacist->id,
            'dispensed',
            "Dispensed {$prescription->drug_name} ({$request->quantity} units)",
            'Dispense',
            $dispense->id
        );

        return redirect()->route('pharmacy.lookup', ['rx_id' => $prescription->rx_id])
            ->with('success', 'Dispense recorded successfully.');
    }

    public function history()
    {
        $dispenses = Dispense::where('pharmacist_id', auth()->id())
            ->with(['prescription.patient.user', 'prescription.prescribedBy', 'facility'])
            ->latest()
            ->paginate(30);

        return Inertia::render('Pharmacy/History', compact('dispenses'));
    }
}
