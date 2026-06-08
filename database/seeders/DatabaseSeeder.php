<?php

namespace Database\Seeders;

use App\Models\Allergy;
use App\Models\AuditLog;
use App\Models\BreakGlassEvent;
use App\Models\Condition;
use App\Models\Consent;
use App\Models\DentalRecord;
use App\Models\Dispense;
use App\Models\Encounter;
use App\Models\EyeCareRecord;
use App\Models\Facility;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Provider;
use App\Models\User;
use App\Models\Vaccination;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Facilities ─────────────────────────────────────────────────────
        $reddington = Facility::create([
            'name' => 'Reddington Hospital', 'type' => 'hospital',
            'license_no' => 'LSMOH/2018/H001',
            'address' => '12 Idowu Martins Street, Victoria Island',
            'city' => 'Lagos', 'state' => 'Lagos',
        ]);

        $healthplus = Facility::create([
            'name' => 'HealthPlus Pharmacy Lekki', 'type' => 'pharmacy',
            'license_no' => 'PCN/2015/P0091',
            'address' => 'Lekki Phase 1', 'city' => 'Lagos', 'state' => 'Lagos',
        ]);

        $smileBright = Facility::create([
            'name' => 'SmileBright Dental Clinic', 'type' => 'dental',
            'license_no' => 'MDCN/2016/D042',
            'address' => '7 Kofo Abayomi Street', 'city' => 'Lagos', 'state' => 'Lagos',
        ]);

        $synlab = Facility::create([
            'name' => 'Synlab Diagnostics Lekki', 'type' => 'lab',
            'city' => 'Lagos', 'state' => 'Lagos',
        ]);

        // ── Users ──────────────────────────────────────────────────────────
        $patientUser = User::create([
            'name' => 'Adaeze Okonkwo', 'email' => 'adaeze@gmail.com',
            'phone' => '08045678901', 'nin' => '12345678901',
            'role' => 'patient', 'password' => Hash::make('password'),
        ]);

        $patient = Patient::create([
            'user_id' => $patientUser->id, 'med_id' => 'MR-9482-1735-2210',
            'dob' => '1992-03-15', 'gender' => 'female',
            'blood_type' => 'O+', 'genotype' => 'AA',
            'address' => '14 Adesola Street', 'city' => 'Lagos', 'state' => 'Lagos',
            'nin_verified' => true,
        ]);

        $doctorUser = User::create([
            'name' => 'Dr Tunde Adeyemi', 'email' => 'tunde@reddington.ng',
            'phone' => '08012345678', 'role' => 'provider',
            'password' => Hash::make('password'),
        ]);
        Provider::create([
            'user_id' => $doctorUser->id, 'facility_id' => $reddington->id,
            'specialty' => 'General Practitioner', 'mdcn_folio' => 'MDCN/2018/4471',
        ]);

        $dentistUser = User::create([
            'name' => 'Dr Bola Adekunle', 'email' => 'bola@smilebright.ng',
            'phone' => '08034567890', 'role' => 'provider',
            'password' => Hash::make('password'),
        ]);
        Provider::create([
            'user_id' => $dentistUser->id, 'facility_id' => $smileBright->id,
            'specialty' => 'Dentist', 'mdcn_folio' => 'MDCN/2016/D204',
        ]);

        $pharmacistUser = User::create([
            'name' => 'Mrs Okafor', 'email' => 'okafor@healthplus.ng',
            'phone' => '08023456789', 'role' => 'pharmacist',
            'password' => Hash::make('password'),
        ]);
        Provider::create([
            'user_id' => $pharmacistUser->id, 'facility_id' => $healthplus->id,
            'specialty' => 'Pharmacist', 'license_no' => 'PCN/2015/0987',
        ]);

        User::create([
            'name' => 'MedRecords Admin', 'email' => 'admin@medrecords.ng',
            'phone' => '08000000001', 'role' => 'super_admin',
            'password' => Hash::make('password'),
        ]);

        // ── Consents ───────────────────────────────────────────────────────
        Consent::create([
            'patient_id' => $patient->id, 'grantee_id' => $doctorUser->id,
            'scope' => 'full', 'status' => 'active',
            'granted_at' => now()->subYear(), 'expires_at' => now()->addYear(),
        ]);
        Consent::create([
            'patient_id' => $patient->id, 'grantee_id' => $dentistUser->id,
            'scope' => 'dental', 'status' => 'active',
            'granted_at' => now()->subMonths(3), 'expires_at' => now()->addMonths(3),
        ]);

        // ── Clinical data ──────────────────────────────────────────────────
        Allergy::create([
            'patient_id' => $patient->id, 'recorded_by' => $doctorUser->id,
            'allergen' => 'Penicillin', 'severity' => 'severe',
            'reaction' => 'Anaphylaxis', 'confirmed_at' => '2019-05-10', 'is_active' => true,
        ]);

        Condition::create([
            'patient_id' => $patient->id, 'recorded_by' => $doctorUser->id,
            'name' => 'Type 2 diabetes mellitus', 'icd10_code' => 'E11.9',
            'status' => 'chronic', 'diagnosed_at' => '2023-03-01',
        ]);
        Condition::create([
            'patient_id' => $patient->id, 'recorded_by' => $doctorUser->id,
            'name' => 'Hypertension', 'icd10_code' => 'I10',
            'status' => 'active', 'diagnosed_at' => '2024-01-15',
        ]);

        $enc1 = Encounter::create([
            'patient_id' => $patient->id, 'provider_id' => $doctorUser->id,
            'facility_id' => $reddington->id, 'type' => 'outpatient',
            'chief_complaint' => 'Routine diabetes follow-up; mild fatigue',
            'history' => 'Patient 12 weeks post initiation of metformin. Good adherence.',
            'diagnosis' => 'Type 2 diabetes mellitus without complications',
            'icd10_code' => 'E11.9',
            'vitals' => 'BP 132/86 · HR 78 · Temp 36.7°C · Wt 78kg',
            'assessment_plan' => 'HbA1c trending down from 8.2 to 7.4. Continue metformin 500mg BD. Increase lisinopril to 10mg OD.',
            'status' => 'signed', 'signed_at' => now()->subDays(10),
            'created_at' => now()->subDays(10), 'updated_at' => now()->subDays(10),
        ]);

        $enc2 = Encounter::create([
            'patient_id' => $patient->id, 'provider_id' => $dentistUser->id,
            'facility_id' => $smileBright->id, 'type' => 'dental',
            'chief_complaint' => 'Cavity filling — molar restoration',
            'diagnosis' => 'Caries of dentine',
            'notes' => 'Composite filling on tooth 36. Patient tolerated procedure well.',
            'status' => 'signed', 'signed_at' => now()->subDays(52),
            'created_at' => now()->subDays(52), 'updated_at' => now()->subDays(52),
        ]);

        $rx1 = Prescription::create([
            'rx_id' => 'RX-4471-2210', 'patient_id' => $patient->id,
            'encounter_id' => $enc1->id, 'prescribed_by' => $doctorUser->id,
            'drug_name' => 'Metformin', 'dose' => '500mg',
            'frequency' => 'Twice daily with meals', 'duration_days' => 90,
            'diagnosis_context' => 'Type 2 diabetes (E11.9)',
            'instructions' => 'Take with food. Avoid alcohol. Report nausea or muscle pain.',
            'status' => 'dispensed',
        ]);

        Prescription::create([
            'rx_id' => 'RX-4472-2210', 'patient_id' => $patient->id,
            'encounter_id' => $enc1->id, 'prescribed_by' => $doctorUser->id,
            'drug_name' => 'Lisinopril', 'dose' => '10mg',
            'frequency' => 'Once daily', 'duration_days' => 90,
            'diagnosis_context' => 'Hypertension (I10)', 'status' => 'active',
        ]);

        Prescription::create([
            'rx_id' => 'RX-4473-2210', 'patient_id' => $patient->id,
            'encounter_id' => $enc1->id, 'prescribed_by' => $doctorUser->id,
            'drug_name' => 'Atorvastatin', 'dose' => '20mg',
            'frequency' => 'Once nightly', 'duration_days' => 90,
            'status' => 'active',
        ]);

        Dispense::create([
            'prescription_id' => $rx1->id, 'pharmacist_id' => $pharmacistUser->id,
            'facility_id' => $healthplus->id, 'quantity' => 60,
            'batch_lot' => 'MET-2604-A', 'price' => 4200.00,
            'payment_method' => 'NHIA covered',
            'counseling_notes' => 'Take with food. Avoid alcohol. Report nausea or breathing changes.',
        ]);

        foreach ([
            ['Fasting Blood Glucose', '1558-6', '142', 'mg/dL', '70-100 mg/dL', 'high'],
            ['HbA1c',                 '4548-4', '7.4',  '%',     '< 5.7%',       'high'],
            ['Total Cholesterol',     '2093-3', '198',  'mg/dL', '< 200 mg/dL',  'normal'],
            ['LDL Cholesterol',       '13457-7','118',  'mg/dL', '< 100 mg/dL',  'high'],
        ] as [$name, $loinc, $result, $unit, $ref, $flag]) {
            LabResult::create([
                'patient_id' => $patient->id, 'encounter_id' => $enc1->id,
                'ordered_by' => $doctorUser->id, 'test_name' => $name,
                'loinc_code' => $loinc, 'result' => $result, 'unit' => $unit,
                'reference_range' => $ref, 'flag' => $flag,
                'collected_at' => now()->subDays(12),
            ]);
        }

        foreach ([
            ['COVID-19 Booster', '2023-11-20', null],
            ['Influenza',        '2024-10-05', '2025-10-05'],
            ['Hepatitis B',      '2022-06-15', null],
        ] as [$vaccine, $given, $due]) {
            Vaccination::create([
                'patient_id' => $patient->id, 'administered_by' => $doctorUser->id,
                'vaccine_name' => $vaccine, 'administered_at' => $given, 'next_due' => $due,
            ]);
        }

        DentalRecord::create([
            'patient_id' => $patient->id, 'provider_id' => $dentistUser->id,
            'facility_id' => $smileBright->id,
            'procedure' => 'Composite filling — tooth 36',
            'notes' => 'Pre-op X-ray taken. Patient tolerated well.',
            'visit_date' => now()->subDays(52),
            'next_cleaning_due' => now()->addMonths(4),
        ]);

        EyeCareRecord::create([
            'patient_id' => $patient->id, 'provider_id' => $doctorUser->id,
            'facility_id' => $reddington->id,
            'od_prescription' => '-2.25 / -0.50 × 175', 'os_prescription' => '-2.00 / -0.75 × 165',
            'visual_acuity_od' => '20/25', 'visual_acuity_os' => '20/20',
            'iop_od' => '15', 'iop_os' => '14',
            'fundus_notes' => 'No retinopathy. Annual screening due given T2DM.',
            'notes' => 'Mild myopia, stable.',
            'exam_date' => now()->subMonths(4),
            'next_exam_due' => now()->addMonths(8),
        ]);

        AuditLog::record($patient->id, $doctorUser->id, 'record_viewed', 'Dr Tunde Adeyemi viewed full record');
        AuditLog::record($patient->id, $doctorUser->id, 'encounter_signed', 'Dr Tunde wrote encounter notes', 'Encounter', $enc1->id);
        AuditLog::record($patient->id, $pharmacistUser->id, 'dispensed', 'HealthPlus Pharmacy dispensed Metformin', 'Dispense', 1);
        AuditLog::record($patient->id, $dentistUser->id, 'record_viewed', 'Dr Bola viewed dental record');

        BreakGlassEvent::create([
            'patient_id' => $patient->id, 'provider_id' => $doctorUser->id,
            'reason_code' => 'life_threatening_emergency',
            'justification' => 'Patient presented unconscious. Suspected hypoglycaemia.',
            'status' => 'closed',
            'expires_at' => now()->subDays(20),
            'created_at' => now()->subDays(22),
            'updated_at' => now()->subDays(22),
        ]);
    }
}
