@extends('layouts.app')
@section('title', $prescription->drug_name)
@section('topbar-title', 'Prescription Detail')

@section('content')
<div class="mb-16">
  <a href="{{ route('patient.record', ['tab'=>'prescriptions']) }}" class="btn btn-secondary btn-sm">← Back to record</a>
</div>

<div class="rx-layout">
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title">
          {{ $prescription->drug_name }} {{ $prescription->dose }}
          <span class="pill {{ $prescription->statusColor }}" style="margin-left:8px;">{{ $prescription->status }}</span>
        </div>
        <div class="card-sub">{{ $prescription->rx_id }}</div>
      </div>
    </div>
    <div class="detail-row"><span class="k">Prescribed by</span><span class="v">{{ $prescription->prescribedBy->name }}</span></div>
    <div class="detail-row"><span class="k">Date</span><span class="v">{{ $prescription->created_at->format('d M Y') }}</span></div>
    <div class="detail-row"><span class="k">Dose</span><span class="v">{{ $prescription->dose }}</span></div>
    <div class="detail-row"><span class="k">Frequency</span><span class="v">{{ $prescription->frequency }}</span></div>
    <div class="detail-row"><span class="k">Duration</span><span class="v">{{ $prescription->duration_days ? $prescription->duration_days . ' days' : '—' }}</span></div>
    <div class="detail-row"><span class="k">Diagnosis</span><span class="v">{{ $prescription->diagnosis_context ?: '—' }}</span></div>
    @if($prescription->instructions)
      <div class="rx-instructions">{{ $prescription->instructions }}</div>
    @endif
  </div>

  <div>
    <div class="card">
      <div class="aside-card">
        <div class="aside-label">Dispense history</div>
        @forelse($prescription->dispenses as $d)
          <div class="dispense-item">
            <div class="dispense-name">{{ $d->facility->name }}</div>
            <div class="dispense-meta">{{ $d->created_at->format('d M Y') }} · {{ $d->quantity }} units</div>
            @if($d->price)
              <div class="dispense-meta">NGN {{ number_format($d->price) }}</div>
            @endif
          </div>
        @empty
          <div class="td-muted">Not yet dispensed.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
