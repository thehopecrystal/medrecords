<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Health Record — {{ $patient->med_id }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }
  .page { padding: 28px 32px; }
  .header { border-bottom: 2px solid #1E3A5F; padding-bottom: 14px; margin-bottom: 18px; }
  .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
  .site-name { font-size: 18px; font-weight: 700; color: #1E3A5F; }
  .doc-title { font-size: 13px; color: #475569; margin-top: 2px; }
  .export-meta { text-align: right; font-size: 10px; color: #64748B; }
  .patient-card { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px; padding: 14px 18px; margin-bottom: 20px; display: flex; gap: 24px; }
  .patient-field { font-size: 10px; color: #64748B; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.04em; }
  .patient-value { font-size: 12px; font-weight: 600; color: #0F172A; }
  .section { margin-bottom: 22px; }
  .section-title { font-size: 12px; font-weight: 700; color: #1E3A5F; text-transform: uppercase; letter-spacing: 0.06em; border-bottom: 1px solid #CBD5E1; padding-bottom: 5px; margin-bottom: 10px; }
  table { width: 100%; border-collapse: collapse; font-size: 10px; }
  th { background: #EFF6FF; color: #1E3A5F; font-weight: 700; text-align: left; padding: 6px 8px; border: 1px solid #BFDBFE; }
  td { padding: 5px 8px; border: 1px solid #E2E8F0; vertical-align: top; }
  tr:nth-child(even) td { background: #F8FAFC; }
  .badge { display: inline-block; padding: 1px 7px; border-radius: 10px; font-size: 9px; font-weight: 700; }
  .badge-green { background: #DCFCE7; color: #166534; }
  .badge-red { background: #FEE2E2; color: #991B1B; }
  .badge-amber { background: #FEF3C7; color: #92400E; }
  .badge-grey { background: #F1F5F9; color: #475569; }
  .empty { color: #94A3B8; font-style: italic; font-size: 10px; padding: 8px 0; }
  .footer { margin-top: 30px; border-top: 1px solid #E2E8F0; padding-top: 10px; font-size: 9px; color: #94A3B8; text-align: center; }
  .confidential { background: #FFF7ED; border: 1px solid #FED7AA; border-radius: 4px; padding: 6px 12px; font-size: 10px; color: #92400E; text-align: center; margin-bottom: 16px; }
</style>
</head>
<body>
<div class="page">

  {{-- Header --}}
  <div class="header">
    <div class="header-top">
      <div>
        <div class="site-name">MedRecords</div>
        <div class="doc-title">Patient Health Record Export</div>
      </div>
      <div class="export-meta">
        Generated: {{ now()->format('d M Y, H:i') }}<br>
        MED-ID: <strong>{{ $patient->med_id }}</strong>
      </div>
    </div>
  </div>

  <div class="confidential">
    CONFIDENTIAL — This document contains protected health information. Unauthorised disclosure is prohibited.
  </div>

  {{-- Patient demographics --}}
  <div class="patient-card">
    <div>
      <div class="patient-field">Full Name</div>
      <div class="patient-value">{{ $patient->user->name }}</div>
    </div>
    <div>
      <div class="patient-field">Date of Birth</div>
      <div class="patient-value">{{ $patient->dob ? $patient->dob->format('d M Y') : 'N/A' }}</div>
    </div>
    <div>
      <div class="patient-field">Age</div>
      <div class="patient-value">{{ $patient->age }} yrs</div>
    </div>
    <div>
      <div class="patient-field">Gender</div>
      <div class="patient-value">{{ ucfirst($patient->gender ?? 'N/A') }}</div>
    </div>
    <div>
      <div class="patient-field">Blood Type</div>
      <div class="patient-value">{{ $patient->blood_type ?? 'N/A' }}</div>
    </div>
    <div>
      <div class="patient-field">Genotype</div>
      <div class="patient-value">{{ $patient->genotype ?? 'N/A' }}</div>
    </div>
  </div>

  {{-- Allergies --}}
  <div class="section">
    <div class="section-title">Allergies</div>
    @if($patient->allergies->isEmpty())
      <div class="empty">No allergies recorded.</div>
    @else
      <table>
        <thead><tr><th>Allergen</th><th>Type</th><th>Severity</th><th>Reaction</th></tr></thead>
        <tbody>
          @foreach($patient->allergies as $a)
          <tr>
            <td>{{ $a->allergen }}</td>
            <td>{{ $a->type ?? '—' }}</td>
            <td><span class="badge {{ $a->severity === 'severe' ? 'badge-red' : ($a->severity === 'moderate' ? 'badge-amber' : 'badge-grey') }}">{{ ucfirst($a->severity ?? 'unknown') }}</span></td>
            <td>{{ $a->reaction ?? '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  {{-- Active conditions --}}
  <div class="section">
    <div class="section-title">Conditions &amp; Diagnoses</div>
    @if($patient->conditions->isEmpty())
      <div class="empty">No conditions recorded.</div>
    @else
      <table>
        <thead><tr><th>Condition</th><th>ICD-10</th><th>Status</th><th>Diagnosed</th></tr></thead>
        <tbody>
          @foreach($patient->conditions as $c)
          <tr>
            <td>{{ $c->name }}</td>
            <td>{{ $c->icd10_code ?? '—' }}</td>
            <td><span class="badge {{ in_array($c->status, ['active','chronic']) ? 'badge-red' : 'badge-grey' }}">{{ ucfirst($c->status) }}</span></td>
            <td>{{ $c->diagnosed_at ? \Carbon\Carbon::parse($c->diagnosed_at)->format('M Y') : '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  {{-- Prescriptions --}}
  <div class="section">
    <div class="section-title">Prescriptions</div>
    @if($patient->prescriptions->isEmpty())
      <div class="empty">No prescriptions recorded.</div>
    @else
      <table>
        <thead><tr><th>RX ID</th><th>Drug</th><th>Dose</th><th>Frequency</th><th>Duration</th><th>Status</th><th>Prescribed By</th></tr></thead>
        <tbody>
          @foreach($patient->prescriptions as $rx)
          <tr>
            <td>{{ $rx->rx_id }}</td>
            <td>{{ $rx->drug_name }}</td>
            <td>{{ $rx->dose }}</td>
            <td>{{ $rx->frequency }}</td>
            <td>{{ $rx->duration_days ? $rx->duration_days . ' days' : '—' }}</td>
            <td><span class="badge {{ $rx->status === 'active' ? 'badge-green' : 'badge-grey' }}">{{ ucfirst($rx->status) }}</span></td>
            <td>{{ $rx->prescribedBy?->name ?? '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  {{-- Encounters --}}
  <div class="section">
    <div class="section-title">Encounters</div>
    @if($patient->encounters->isEmpty())
      <div class="empty">No encounters recorded.</div>
    @else
      <table>
        <thead><tr><th>Date</th><th>Type</th><th>Complaint</th><th>Diagnosis</th><th>Provider</th><th>Facility</th></tr></thead>
        <tbody>
          @foreach($patient->encounters->sortByDesc('created_at') as $enc)
          <tr>
            <td>{{ $enc->created_at->format('d M Y') }}</td>
            <td>{{ ucfirst($enc->type) }}</td>
            <td>{{ \Illuminate\Support\Str::limit($enc->chief_complaint, 60) }}</td>
            <td>{{ $enc->diagnosis ? \Illuminate\Support\Str::limit($enc->diagnosis, 60) : '—' }}</td>
            <td>{{ $enc->provider?->name ?? '—' }}</td>
            <td>{{ $enc->facility?->name ?? '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  {{-- Lab Results --}}
  <div class="section">
    <div class="section-title">Lab Results</div>
    @if($patient->labResults->isEmpty())
      <div class="empty">No lab results recorded.</div>
    @else
      <table>
        <thead><tr><th>Test</th><th>Result</th><th>Flag</th><th>Collected</th></tr></thead>
        <tbody>
          @foreach($patient->labResults->sortByDesc('collected_at') as $lab)
          <tr>
            <td>{{ $lab->test_name }}</td>
            <td>{{ \Illuminate\Support\Str::limit($lab->result, 80) }}</td>
            <td><span class="badge {{ in_array($lab->flag, ['high','critical']) ? 'badge-red' : ($lab->flag === 'low' ? 'badge-amber' : 'badge-green') }}">{{ strtoupper($lab->flag ?? 'normal') }}</span></td>
            <td>{{ $lab->collected_at ? $lab->collected_at->format('d M Y') : '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  {{-- Vaccinations --}}
  @if($patient->vaccinations->isNotEmpty())
  <div class="section">
    <div class="section-title">Vaccinations</div>
    <table>
      <thead><tr><th>Vaccine</th><th>Dose</th><th>Date Given</th><th>Lot No.</th><th>Next Due</th></tr></thead>
      <tbody>
        @foreach($patient->vaccinations as $vax)
        <tr>
          <td>{{ $vax->vaccine_name }}</td>
          <td>{{ $vax->dose_number ?? '—' }}</td>
          <td>{{ $vax->given_at ? \Carbon\Carbon::parse($vax->given_at)->format('d M Y') : '—' }}</td>
          <td>{{ $vax->lot_number ?? '—' }}</td>
          <td>{{ $vax->next_due_at ? \Carbon\Carbon::parse($vax->next_due_at)->format('d M Y') : '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif

  {{-- Footer --}}
  <div class="footer">
    MedRecords Electronic Health Record System &bull; Exported {{ now()->format('d M Y H:i') }} &bull;
    This record is for the personal use of {{ $patient->user->name }} ({{ $patient->med_id }}) only.
  </div>

</div>
</body>
</html>
