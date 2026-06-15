<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Dispense;
use App\Models\Prescription;
use App\Services\NhiaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DispenseController extends Controller
{
    public function dashboard()
    {
        $recentDispenses = Dispense::where('pharmacist_id', auth()->id())
            ->with(['prescription.patient.user', 'prescription.prescribedBy', 'facility'])
            ->latest()
            ->take(15)
            ->get();

        return view('pharmacy.dashboard', compact('recentDispenses'));
    }

    public function lookup(Request $request)
    {
        $rxId       = $request->input('rx_id', '');
        $facilityId = $this->currentUser()->provider?->facility_id;
        $prescription = null;

        if ($rxId && $facilityId !== null) {
            $prescription = Prescription::where('rx_id', $rxId)
                ->where('facility_id', $facilityId)
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

        return view('pharmacy.lookup', compact('prescription', 'rxId', 'nhia'));
    }

    public function dispense(Request $request, int $prescriptionId)
    {
        $request->validate([
            'quantity'         => 'required|integer|min:1|max:9999',
            'batch_lot'        => 'nullable|string|max:100',
            'price'            => 'nullable|numeric|min:0|max:9999999',
            'payment_method'   => 'nullable|in:cash,card,insurance,transfer,waived',
            'counseling_notes' => 'nullable|string|max:1000',
        ]);

        $pharmacist = $this->currentUser();
        $facilityId = $pharmacist->provider?->facility_id;
        abort_if($facilityId === null, 422, 'Pharmacist is not linked to a facility.');

        $dispense = DB::transaction(function () use ($request, $prescriptionId, $pharmacist, $facilityId) {
            // Lock the row so concurrent requests can't double-dispense.
            $prescription = Prescription::with('patient')->lockForUpdate()->findOrFail($prescriptionId);

            // Require facility match even when prescription has no facility_id (no open-network dispense).
            abort_unless(
                $prescription->facility_id === $facilityId,
                403,
                'This prescription was not issued at your facility.'
            );

            abort_if(
                $prescription->status === 'dispensed' || Dispense::where('prescription_id', $prescription->id)->exists(),
                422,
                'This prescription has already been dispensed and cannot be dispensed again.'
            );

            $dispense = Dispense::create([
                'prescription_id'  => $prescription->id,
                'pharmacist_id'    => $pharmacist->id,
                'facility_id'      => $facilityId,
                'quantity'         => $request->quantity,
                'batch_lot'        => $request->batch_lot,
                'price'            => $request->price,
                'payment_method'   => $request->payment_method,
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

            return $dispense;
        });

        $prescription = Prescription::findOrFail($prescriptionId);

        return redirect()->route('pharmacy.lookup', ['rx_id' => $prescription->rx_id])
            ->with('success', 'Dispense recorded successfully.');
    }

    public function history()
    {
        $dispenses = Dispense::where('pharmacist_id', auth()->id())
            ->with(['prescription.patient.user', 'prescription.prescribedBy', 'facility'])
            ->latest()
            ->paginate(30);

        return view('pharmacy.history', compact('dispenses'));
    }
}
