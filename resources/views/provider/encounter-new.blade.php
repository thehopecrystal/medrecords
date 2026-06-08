@extends('layouts.app')
@section('title', 'New Encounter')
@section('topbar-title', 'New Encounter')


@section('content')
<div style="margin-bottom:16px;">
  <a href="{{ route('provider.patient', $patient->id) }}" class="btn btn-secondary btn-sm">← Back to {{ $patient->user->name }}</a>
</div>

<div class="page-header">
  <h1>New encounter — {{ $patient->user->name }}</h1>
  <p>{{ $provider?->facility->name ?? '' }} · {{ now()->format('d M Y H:i') }}</p>
</div>

<form method="POST" action="{{ route('provider.encounter.store', $patient->id) }}">
  @csrf
  <div class="card">
    <div class="form-grid">
      <div class="form-field">
        <label>Encounter type</label>
        <select name="type" required>
          <option value="outpatient">Outpatient</option>
          <option value="inpatient">Inpatient</option>
          <option value="emergency">Emergency</option>
          <option value="telemedicine">Telemedicine</option>
          <option value="dental">Dental</option>
          <option value="optical">Optical</option>
        </select>
      </div>
      <div class="form-field">
        <label>Chief complaint</label>
        <input type="text" name="chief_complaint" placeholder="e.g. Routine diabetes follow-up" required>
      </div>
      <div class="form-field full">
        <label>History of presenting illness</label>
        <textarea name="history" placeholder="Patient history…"></textarea>
      </div>
      <div class="form-field">
        <label>Diagnosis (ICD-10)</label>
        <input type="text" name="diagnosis" placeholder="e.g. Type 2 diabetes mellitus">
      </div>
      <div class="form-field">
        <label>ICD-10 code</label>
        <input type="text" name="icd10_code" placeholder="e.g. E11.9">
      </div>
      <div class="form-field">
        <label>Vital signs</label>
        <input type="text" name="vitals" placeholder="BP 120/80 · HR 72 · Temp 36.6 · Wt 70kg">
      </div>
      <div class="form-field full">
        <label>Assessment & plan</label>
        <textarea name="assessment_plan" rows="4" placeholder="Your clinical assessment and management plan…" required></textarea>
      </div>
      <div class="form-field full">
        <label>Additional notes</label>
        <textarea name="notes" placeholder="Any other notes…"></textarea>
      </div>
    </div>

    <div style="border-top:1px solid var(--grey-200);margin-top:20px;padding-top:20px;">
      <div style="font-size:13px;font-weight:700;color:var(--navy);margin-bottom:14px;">Add prescription (optional)</div>
      <div class="form-grid">
        <div class="form-field">
          <label>Drug name</label>
          <input type="text" name="drug_name" placeholder="e.g. Metformin">
        </div>
        <div class="form-field">
          <label>Dose</label>
          <input type="text" name="drug_dose" placeholder="e.g. 500mg">
        </div>
        <div class="form-field">
          <label>Frequency</label>
          <input type="text" name="drug_frequency" placeholder="e.g. Twice daily with meals">
        </div>
        <div class="form-field">
          <label>Duration (days)</label>
          <input type="number" name="drug_duration" placeholder="e.g. 30">
        </div>
        <div class="form-field full">
          <label>Instructions</label>
          <input type="text" name="drug_instructions" placeholder="e.g. Take with food, avoid alcohol">
        </div>
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" name="draft" class="btn btn-secondary">Save draft</button>
      <button type="submit" name="sign" class="btn btn-green">Sign & close</button>
    </div>
  </div>
</form>
@endsection
