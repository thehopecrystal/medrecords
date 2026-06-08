@extends('layouts.app')
@section('title', $patient->user->name)
@section('topbar-title', 'Patient Record')

@section('topbar-actions')
  <a href="{{ route('provider.encounter.new', $patient->id) }}" class="btn btn-primary btn-sm">+ New encounter</a>
  <a href="{{ route('provider.referral.create', $patient->id) }}" class="btn btn-secondary btn-sm">📤 Refer</a>
  <a href="{{ route('provider.lab.create', $patient->id) }}" class="btn btn-secondary btn-sm">🧪 Add lab</a>
  @if(!$consent)
    <a href="{{ route('provider.consent-request.create', $patient->id) }}" class="btn btn-secondary btn-sm">🔐 Request consent</a>
    <a href="{{ route('provider.break-glass', $patient->id) }}" class="btn btn-danger btn-sm">⚡ Break-glass</a>
  @endif
@endsection


@section('content')

@if(!$consent)
  <div class="alert amber"><span>⚠</span><span><strong>No active consent.</strong> You do not have an active consent grant from this patient. Request consent or use break-glass for emergencies.</span></div>
@endif

{{-- Patient banner --}}
<div class="patient-banner">
  <div>
    <div class="pb-name">{{ $patient->user->name }}</div>
    <div class="pb-meta">
      {{ $patient->gender ? ucfirst($patient->gender) : '' }}
      {{ $patient->dob ? ' · ' . $patient->age . ' yrs' : '' }}
      · {{ $patient->med_id }}
      {{ $patient->user->phone ? ' · ' . $patient->user->phone : '' }}
    </div>
    <div class="pb-flags">
      @foreach($patient->activeAllergies as $a)
        <span class="pill red">⚠ {{ $a->allergen }} allergy</span>
      @endforeach
      @foreach($patient->activeConditions as $c)
        <span class="pill amber">● {{ $c->name }}</span>
      @endforeach
      @if($patient->activePrescriptions->count())
        <span class="pill navy">{{ $patient->activePrescriptions->count() }} active Rx</span>
      @endif
    </div>
  </div>
  @if($consent)
    <div class="pb-consent">
      <div class="label">Consent scope</div>
      <div class="value">{{ $consent->scopeLabel }}</div>
      <div class="sub">expires {{ $consent->expires_at?->format('d M Y') ?? 'no expiry' }}</div>
    </div>
  @endif
  <div>
    <a href="{{ route('provider.search') }}" class="btn btn-secondary btn-sm">← Back</a>
  </div>
</div>

{{-- Tab strip --}}
<div class="tab-strip">
  @foreach(['summary'=>'Summary','encounters'=>'Encounters','conditions'=>'Conditions','prescriptions'=>'Medications','labs'=>'Labs','vaccinations'=>'Vaccines'] as $key => $label)
    <a href="{{ route('provider.patient', ['id' => $patient->id, 'tab' => $key]) }}" class="tab-item {{ $tab === $key ? 'active' : '' }}">{{ $label }}</a>
  @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:20px;">
  <div>

    @if(in_array($tab, ['summary', 'encounters']))
      <div class="card" style="margin-bottom:20px;">
        <div class="card-header">
          <div class="card-title">Encounters <span class="pill navy">{{ $patient->encounters->count() }}</span></div>
        </div>
        @forelse($patient->encounters->sortByDesc('created_at') as $enc)
          <div class="entry">
            <div class="entry-head">
              <div>
                <div class="entry-title">{{ $enc->chief_complaint ?: ucfirst($enc->type) }}</div>
                <div class="entry-meta">{{ $enc->provider->name }} · {{ $enc->facility->name }}</div>
              </div>
              <div style="text-align:right;">
                <div class="entry-date">{{ $enc->created_at->format('d M Y') }}</div>
                <span class="pill {{ $enc->status === 'signed' ? 'green' : 'amber' }}">{{ $enc->status }}</span>
              </div>
            </div>
            @if($enc->assessment_plan)
              <div class="entry-body">{{ Str::limit($enc->assessment_plan, 200) }}</div>
            @endif
            @if($enc->diagnosis)
              <div class="entry-foot">{{ $enc->diagnosis }}</div>
            @endif
            @if($enc->status === 'signed')
              <div style="margin-top:8px;">
                <a href="{{ route('provider.encounter.print', [$patient->id, $enc->id]) }}" target="_blank" class="btn btn-secondary btn-sm">🖨 Print</a>
              </div>
            @endif
          </div>
        @empty
          <div style="padding:16px;color:var(--grey-500);font-size:13px;">No encounters on record.</div>
        @endforelse
      </div>
    @endif

    @if(in_array($tab, ['summary', 'conditions']))
      <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><div class="card-title">Conditions</div></div>
        @forelse($patient->conditions as $c)
          <div class="entry">
            <div class="entry-head">
              <div>
                <div class="entry-title">{{ $c->name }}</div>
                <div class="entry-meta">{{ $c->icd10_code }} · diagnosed {{ $c->diagnosed_at?->format('M Y') }}</div>
              </div>
              <span class="pill {{ in_array($c->status, ['active','chronic']) ? 'amber' : 'grey' }}">{{ $c->status }}</span>
            </div>
          </div>
        @empty
          <div style="padding:16px;color:var(--grey-500);font-size:13px;">No conditions recorded.</div>
        @endforelse
      </div>
    @endif

    @if(in_array($tab, ['summary', 'prescriptions']))
      <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><div class="card-title">Medications <span class="pill green">{{ $patient->activePrescriptions->count() }} active</span></div></div>
        @forelse($patient->prescriptions->sortByDesc('created_at') as $rx)
          <div class="entry">
            <div class="entry-head">
              <div>
                <div class="entry-title">{{ $rx->drug_name }} {{ $rx->dose }}</div>
                <div class="entry-meta">{{ $rx->frequency }} · {{ $rx->prescribedBy->name }}</div>
              </div>
              <span class="pill {{ $rx->statusColor }}">{{ $rx->status }}</span>
            </div>
          </div>
        @empty
          <div style="padding:16px;color:var(--grey-500);font-size:13px;">No prescriptions.</div>
        @endforelse
      </div>
    @endif

    @if(in_array($tab, ['labs']))
      <div class="card">
        <div class="card-header"><div class="card-title">Lab Results <span class="pill navy">{{ $patient->labResults->count() }}</span></div></div>
        @forelse($patient->labResults->sortByDesc('collected_at') as $lab)
          <div class="entry">
            <div class="entry-head">
              <div>
                <div class="entry-title">{{ $lab->test_name }}</div>
                <div class="entry-meta">{{ $lab->collected_at->format('d M Y') }}</div>
              </div>
              <span>{{ $lab->result }} {{ $lab->unit }} <span class="pill {{ $lab->flagColor }}">{{ $lab->flag }}</span></span>
            </div>
          </div>
        @empty
          <div style="padding:16px;color:var(--grey-500);font-size:13px;">No lab results.</div>
        @endforelse
      </div>
    @endif

  </div>

  <aside>
    <div class="card" style="margin-bottom:16px;">
      <div style="font-size:11px;color:var(--grey-500);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px;">Active medications</div>
      @forelse($patient->activePrescriptions as $rx)
        <div style="padding:8px 0;border-bottom:1px solid var(--grey-100);font-size:13px;">
          <strong>{{ $rx->drug_name }}</strong> {{ $rx->dose }}<br>
          <span style="font-size:11px;color:var(--grey-500);">{{ $rx->frequency }}</span>
        </div>
      @empty
        <div style="font-size:12px;color:var(--grey-500);">None</div>
      @endforelse
    </div>

    @if($patient->activeAllergies->count())
    <div class="card" style="margin-bottom:16px;border-left:3px solid var(--red);">
      <div style="font-size:11px;color:var(--grey-500);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">Allergies</div>
      @foreach($patient->activeAllergies as $a)
        <div style="font-size:13px;padding:4px 0;">
          <strong style="color:var(--red);">⚠ {{ $a->allergen }}</strong>
          <span style="font-size:11px;color:var(--grey-500);"> · {{ $a->severity }}</span>
        </div>
      @endforeach
    </div>
    @endif

    <div class="card">
      <div style="font-size:11px;color:var(--grey-500);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px;">Patient info</div>
      <div class="detail-row"><span class="k">Blood type</span><span class="v">{{ $patient->blood_type ?: '—' }}</span></div>
      <div class="detail-row"><span class="k">Genotype</span><span class="v">{{ $patient->genotype ?: '—' }}</span></div>
      <div class="detail-row"><span class="k">DOB</span><span class="v">{{ $patient->dob?->format('d M Y') ?: '—' }}</span></div>
    </div>
  </aside>
</div>
@endsection
