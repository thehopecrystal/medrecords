@extends('layouts.app')
@section('title', 'My Medical Record')
@section('topbar-title', 'My Medical Record')

@section('content')
<div class="page-header-row">
  <div>
    <h1>My Medical Record</h1>
    <p class="td-muted">All categories · MedID {{ $patient->med_id }}</p>
  </div>
</div>

{{-- Tab strip --}}
<div class="tab-strip">
  @foreach(['all'=>'All','encounters'=>'Encounters','prescriptions'=>'Medications','labs'=>'Labs','dental'=>'Dental','eye'=>'Eye Care','vaccines'=>'Vaccines','allergies'=>'Allergies & Conditions'] as $key => $label)
    <a href="{{ route('patient.record', ['tab' => $key]) }}" class="tab-item {{ $tab === $key ? 'active' : '' }}">{{ $label }}</a>
  @endforeach
</div>

@if($patient->activeAllergies->count())
  <div class="alert red mb-16">
    <span>⚠</span>
    <span><strong>Allergy alert:</strong> {{ $patient->activeAllergies->pluck('allergen')->join(', ') }}</span>
  </div>
@endif

<div class="record-layout">
  <div>

  {{-- ENCOUNTERS --}}
  @if(in_array($tab, ['all', 'encounters']))
    <div class="card mb-20">
      <div class="card-header">
        <div class="card-title">Encounters & Visits <span class="pill navy">{{ $patient->encounters->count() }}</span></div>
      </div>
      @forelse($patient->encounters->sortByDesc('created_at') as $enc)
        <div class="entry">
          <div class="entry-head">
            <div>
              <div class="entry-title">{{ $enc->chief_complaint ?: ucfirst($enc->type) }}</div>
              <div class="entry-meta">{{ $enc->provider->name }} · {{ $enc->facility->name }}</div>
            </div>
            <div class="btn-group-row">
              <div class="entry-date">{{ $enc->created_at->format('d M Y') }}</div>
              <a href="{{ route('patient.encounter.print', $enc->id) }}" target="_blank" class="btn btn-ghost btn-sm" title="Print / Export PDF">🖨</a>
            </div>
          </div>
          @if($enc->assessment_plan)
            <div class="entry-body">{{ Str::limit($enc->assessment_plan, 200) }}</div>
          @endif
          @if($enc->diagnosis)
            <div class="entry-foot">{{ $enc->diagnosis }}</div>
          @endif
        </div>
      @empty
        <div class="card-empty-body">No encounters on record.</div>
      @endforelse
    </div>
  @endif

  {{-- PRESCRIPTIONS --}}
  @if(in_array($tab, ['all', 'prescriptions']))
    <div class="card mb-20">
      <div class="card-header">
        <div class="card-title">Prescriptions <span class="pill green">{{ $patient->activePrescriptions->count() }} active</span></div>
      </div>
      @forelse($patient->prescriptions->sortByDesc('created_at') as $rx)
        <div class="entry">
          <div class="entry-head">
            <div>
              <div class="entry-title">
                <a href="{{ route('patient.prescription', $rx->id) }}" style="color:inherit;text-decoration:none;">{{ $rx->drug_name }} {{ $rx->dose }}</a>
              </div>
              <div class="entry-meta">{{ $rx->frequency }} · {{ $rx->prescribedBy->name }}</div>
            </div>
            <span class="pill {{ $rx->statusColor }}">{{ $rx->status }}</span>
          </div>
        </div>
      @empty
        <div class="card-empty-body">No prescriptions on record.</div>
      @endforelse
    </div>
  @endif

  {{-- LAB RESULTS --}}
  @if(in_array($tab, ['all', 'labs']))
    <div class="card mb-20">
      <div class="card-header">
        <div class="card-title">Lab Results <span class="pill navy">{{ $patient->labResults->count() }}</span></div>
        <a href="{{ route('patient.lab-upload.create') }}" class="btn btn-secondary btn-sm">+ Upload result</a>
      </div>
      @forelse($patient->labResults->sortByDesc('collected_at') as $lab)
        <div class="entry">
          <div class="entry-head">
            <div>
              <div class="entry-title">{{ $lab->test_name }}</div>
              <div class="entry-meta">{{ $lab->collected_at->format('d M Y') }}</div>
            </div>
            <span class="pill {{ $lab->flagColor }}">{{ $lab->result }} {{ $lab->unit }}</span>
          </div>
          @if($lab->reference_range)
            <div class="entry-body entry-foot">Ref: {{ $lab->reference_range }}</div>
          @endif
        </div>
      @empty
        <div class="card-empty-body">No lab results on record.</div>
      @endforelse
    </div>
  @endif

  {{-- VACCINATIONS --}}
  @if(in_array($tab, ['all', 'vaccines']))
    <div class="card mb-20">
      <div class="card-header">
        <div class="card-title">Vaccinations <span class="pill navy">{{ $patient->vaccinations->count() }}</span></div>
      </div>
      @forelse($patient->vaccinations->sortByDesc('administered_at') as $vax)
        <div class="entry">
          <div class="entry-head">
            <div>
              <div class="entry-title">{{ $vax->vaccine_name }}</div>
              <div class="entry-meta">{{ $vax->administered_at->format('d M Y') }} · {{ $vax->administeredBy->name }}</div>
            </div>
            @if($vax->next_due)
              <span class="pill {{ $vax->next_due->isPast() ? 'red' : 'amber' }}">Due {{ $vax->next_due->format('M Y') }}</span>
            @else
              <span class="pill green">Up to date</span>
            @endif
          </div>
        </div>
      @empty
        <div class="card-empty-body">No vaccination records.</div>
      @endforelse
    </div>
  @endif

  {{-- DENTAL --}}
  @if(in_array($tab, ['all', 'dental']))
    <div class="card mb-20">
      <div class="card-header">
        <div class="card-title">Dental Records <span class="pill navy">{{ $patient->dentalRecords->count() }}</span></div>
      </div>
      @forelse($patient->dentalRecords->sortByDesc('visit_date') as $dr)
        <div class="entry">
          <div class="entry-head">
            <div>
              <div class="entry-title">{{ $dr->procedure ?: 'Dental visit' }}</div>
              <div class="entry-meta">{{ $dr->visit_date->format('d M Y') }} · {{ $dr->provider->name }}</div>
            </div>
          </div>
          @if($dr->notes)<div class="entry-body">{{ Str::limit($dr->notes, 150) }}</div>@endif
        </div>
      @empty
        <div class="card-empty-body">No dental records.</div>
      @endforelse
    </div>
  @endif

  {{-- EYE CARE --}}
  @if(in_array($tab, ['all', 'eye']))
    <div class="card mb-20">
      <div class="card-header">
        <div class="card-title">Eye Care <span class="pill navy">{{ $patient->eyeCareRecords->count() }}</span></div>
      </div>
      @forelse($patient->eyeCareRecords->sortByDesc('exam_date') as $ec)
        <div class="entry">
          <div class="entry-head">
            <div>
              <div class="entry-title">Eye exam</div>
              <div class="entry-meta">{{ $ec->exam_date->format('d M Y') }} · {{ $ec->provider->name }}</div>
            </div>
          </div>
          <div class="entry-body entry-foot">
            OD: {{ $ec->od_prescription }} · OS: {{ $ec->os_prescription }}<br>
            VA: {{ $ec->visual_acuity_od }} / {{ $ec->visual_acuity_os }} · IOP: {{ $ec->iop_od }} / {{ $ec->iop_os }} mmHg
          </div>
        </div>
      @empty
        <div class="card-empty-body">No eye care records.</div>
      @endforelse
    </div>
  @endif

  {{-- ALLERGIES & CONDITIONS --}}
  @if(in_array($tab, ['all', 'allergies']))
    <div class="card mb-20">
      <div class="card-header">
        <div class="card-title">Allergies & Conditions</div>
      </div>
      @foreach($patient->allergies as $a)
        <div class="entry">
          <div class="entry-head">
            <div>
              <div class="entry-title">{{ $a->allergen }}</div>
              <div class="entry-meta">{{ $a->reaction }} · confirmed {{ $a->confirmed_at?->format('Y') }}</div>
            </div>
            <span class="pill {{ $a->severityColor }}">{{ $a->severity }}</span>
          </div>
        </div>
      @endforeach
      @foreach($patient->conditions as $c)
        <div class="entry">
          <div class="entry-head">
            <div>
              <div class="entry-title">{{ $c->name }}</div>
              <div class="entry-meta">{{ $c->icd10_code }} · diagnosed {{ $c->diagnosed_at?->format('M Y') }}</div>
            </div>
            <span class="pill {{ $c->status === 'active' || $c->status === 'chronic' ? 'amber' : 'grey' }}">{{ $c->status }}</span>
          </div>
        </div>
      @endforeach
      @if(!$patient->allergies->count() && !$patient->conditions->count())
        <div class="card-empty-body">No allergies or conditions recorded.</div>
      @endif
    </div>
  @endif

  </div>

  {{-- ASIDE --}}
  <aside>
    <div class="card mb-16">
      <div class="aside-card">
        <div class="aside-label">Active medications</div>
        @forelse($patient->activePrescriptions as $rx)
          <div class="aside-med-item">
            <div class="aside-med-name">{{ $rx->drug_name }} {{ $rx->dose }}</div>
            <div class="aside-med-freq">{{ $rx->frequency }}</div>
          </div>
        @empty
          <div class="td-muted">None</div>
        @endforelse
      </div>
    </div>

    <div class="card">
      <div class="aside-card">
        <div class="aside-label">Profile</div>
        <div class="detail-row"><span class="k">Blood type</span><span class="v">{{ $patient->blood_type ?: '—' }}</span></div>
        <div class="detail-row"><span class="k">Genotype</span><span class="v">{{ $patient->genotype ?: '—' }}</span></div>
        <div class="detail-row"><span class="k">DOB</span><span class="v">{{ $patient->dob?->format('d M Y') ?: '—' }}</span></div>
        <div class="detail-row"><span class="k">Gender</span><span class="v">{{ ucfirst($patient->gender ?: '—') }}</span></div>
      </div>
    </div>
  </aside>
</div>
@endsection
