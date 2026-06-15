<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Encounter — {{ $encounter->patient->user->name }}</title>
<style>
:root {
  --navy: #1E3A5F;
  --green: #2C5F2D;
  --red: #B23A48;
  --grey-500: #6B7280;
  --grey-200: #E5E7EB;
  --grey-100: #F3F4F6;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: "Segoe UI", Arial, sans-serif; font-size: 13px; color: #1A1F26; padding: 32px; max-width: 800px; margin: 0 auto; }
.header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid var(--navy); padding-bottom: 14px; margin-bottom: 20px; }
.brand { font-size: 20px; font-weight: 800; color: var(--navy); }
.brand .sub { font-size: 11px; font-weight: 400; color: var(--grey-500); }
.meta { text-align: right; font-size: 12px; color: var(--grey-500); }
.section { margin-bottom: 20px; }
.section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--grey-500); border-bottom: 1px solid var(--grey-200); padding-bottom: 4px; margin-bottom: 10px; }
.patient-banner { background: var(--grey-100); padding: 12px 14px; border-radius: 8px; margin-bottom: 20px; display: grid; grid-template-columns: 1fr auto; gap: 16px; }
.patient-name { font-size: 18px; font-weight: 700; color: var(--navy); }
.patient-meta { font-size: 12px; color: var(--grey-500); margin-top: 2px; }
.allergy-flag { display: inline-block; background: #FBEAEC; color: #B23A48; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; margin-top: 6px; margin-right: 4px; }
.row { display: flex; gap: 24px; margin-bottom: 8px; }
.field { flex: 1; }
.field .label { font-size: 11px; font-weight: 600; color: var(--grey-500); text-transform: uppercase; letter-spacing: 0.04em; }
.field .value { margin-top: 2px; line-height: 1.5; }
.note-box { background: var(--grey-100); padding: 12px 14px; border-radius: 6px; line-height: 1.6; white-space: pre-wrap; }
.rx-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.rx-table th, .rx-table td { padding: 6px 8px; text-align: left; border-bottom: 1px solid var(--grey-200); }
.rx-table th { font-weight: 600; color: var(--grey-500); font-size: 11px; text-transform: uppercase; }
.signature-row { display: flex; justify-content: space-between; margin-top: 32px; padding-top: 16px; border-top: 1px solid var(--grey-200); font-size: 12px; color: var(--grey-500); }
.sig-line { border-bottom: 1px solid #aaa; width: 200px; margin-bottom: 4px; height: 24px; }
.watermark { color: var(--green); font-weight: 700; font-size: 12px; }
@media print {
  body { padding: 0; }
  .no-print { display: none !important; }
}
</style>
</head>
<body>

<div class="no-print" style="margin-bottom:20px;display:flex;gap:10px;">
  <button id="ep-print" style="background:#1E3A5F;color:#fff;border:none;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">🖨 Print / Save as PDF</button>
  <button id="ep-back"  style="background:#F3F4F6;color:#3D4754;border:1px solid #D1D5DB;padding:9px 18px;border-radius:8px;font-size:13px;cursor:pointer;">← Back</button>
</div>

<div class="header">
  <div>
    <div class="brand">MedRecords <span class="sub">— Clinical Encounter Summary</span></div>
    <div style="font-size:12px;color:var(--grey-500);margin-top:4px;">{{ $encounter->facility->name }} · {{ $encounter->created_at->format('d M Y, H:i') }}</div>
  </div>
  <div class="meta">
    <div>Encounter type: <strong>{{ ucfirst($encounter->type) }}</strong></div>
    <div>Status: <strong>{{ $encounter->status }}</strong></div>
    @if($encounter->signed_at)
      <div>Signed: {{ $encounter->signed_at->format('d M Y H:i') }}</div>
    @endif
  </div>
</div>

<div class="patient-banner">
  <div>
    <div class="patient-name">{{ $encounter->patient->user->name }}</div>
    <div class="patient-meta">
      MedID: {{ $encounter->patient->med_id }}
      @if($encounter->patient->dob) · DOB: {{ $encounter->patient->dob->format('d M Y') }} ({{ $encounter->patient->age }} yrs) @endif
      @if($encounter->patient->gender) · {{ ucfirst($encounter->patient->gender) }} @endif
      @if($encounter->patient->blood_type) · {{ $encounter->patient->blood_type }} @endif
    </div>
    @foreach($encounter->patient->allergies->where('is_active', true) as $a)
      <span class="allergy-flag">⚠ {{ $a->allergen }} ({{ $a->severity }})</span>
    @endforeach
  </div>
  <div style="text-align:right;font-size:12px;color:var(--grey-500);">
    <div>Clinician: <strong>{{ $encounter->provider->name }}</strong></div>
  </div>
</div>

@if($encounter->chief_complaint)
<div class="section">
  <div class="section-title">Chief complaint</div>
  <div>{{ $encounter->chief_complaint }}</div>
</div>
@endif

@if($encounter->vitals)
<div class="section">
  <div class="section-title">Vital signs</div>
  <div>{{ $encounter->vitals }}</div>
</div>
@endif

@if($encounter->history)
<div class="section">
  <div class="section-title">History of presenting illness</div>
  <div class="note-box">{{ $encounter->history }}</div>
</div>
@endif

@if($encounter->diagnosis)
<div class="section">
  <div class="row">
    <div class="field"><div class="label">Diagnosis</div><div class="value">{{ $encounter->diagnosis }}</div></div>
    @if($encounter->icd10_code)<div class="field"><div class="label">ICD-10</div><div class="value">{{ $encounter->icd10_code }}</div></div>@endif
  </div>
</div>
@endif

@if($encounter->assessment_plan)
<div class="section">
  <div class="section-title">Assessment & plan</div>
  <div class="note-box">{{ $encounter->assessment_plan }}</div>
</div>
@endif

@if($encounter->notes)
<div class="section">
  <div class="section-title">Clinical notes</div>
  <div class="note-box">{{ $encounter->notes }}</div>
</div>
@endif

@if($encounter->prescriptions->count())
<div class="section">
  <div class="section-title">Prescriptions issued ({{ $encounter->prescriptions->count() }})</div>
  <table class="rx-table">
    <thead><tr><th>Drug</th><th>Dose</th><th>Frequency</th><th>Duration</th><th>Rx ID</th></tr></thead>
    <tbody>
      @foreach($encounter->prescriptions as $rx)
        <tr>
          <td><strong>{{ $rx->drug_name }}</strong></td>
          <td>{{ $rx->dose }}</td>
          <td>{{ $rx->frequency }}</td>
          <td>{{ $rx->duration_days ? $rx->duration_days . ' days' : '—' }}</td>
          <td style="font-family:monospace;font-size:11px;">{{ $rx->rx_id }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

<div class="signature-row">
  <div>
    <div class="sig-line"></div>
    <div>{{ $encounter->provider->name }}</div>
    @if(auth()->user()?->provider?->mdcn_folio)
      <div style="color:var(--grey-500);">{{ auth()->user()?->provider?->mdcn_folio }}</div>
    @endif
  </div>
  <div style="text-align:right;">
    <div class="watermark">MedRecords — verified clinical document</div>
    <div style="margin-top:4px;">Generated: {{ now()->format('d M Y H:i') }}</div>
  </div>
</div>

<script>
document.getElementById('ep-print').addEventListener('click', function () { window.print(); });
document.getElementById('ep-back').addEventListener('click', function () { history.back(); });
</script>
</body>
</html>
