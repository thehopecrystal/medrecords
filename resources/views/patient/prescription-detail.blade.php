@extends('layouts.app')
@section('title', $prescription->drug_name)
@section('topbar-title', 'Prescription Detail')


@section('content')
<div style="margin-bottom:16px;">
  <a href="{{ route('patient.record', ['tab'=>'prescriptions']) }}" class="btn btn-secondary btn-sm">← Back to record</a>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;">
  <div class="card">
    <div class="card-header">
      <div class="card-title">{{ $prescription->drug_name }} {{ $prescription->dose }}
        <span class="pill {{ $prescription->statusColor }}" style="margin-left:8px;">{{ $prescription->status }}</span>
      </div>
      <div class="card-sub">{{ $prescription->rx_id }}</div>
    </div>

    <div class="detail-row"><span class="k">Prescribed by</span><span class="v">{{ $prescription->prescribedBy->name }}</span></div>
    <div class="detail-row"><span class="k">Date</span><span class="v">{{ $prescription->created_at->format('d M Y') }}</span></div>
    <div class="detail-row"><span class="k">Dose</span><span class="v">{{ $prescription->dose }}</span></div>
    <div class="detail-row"><span class="k">Frequency</span><span class="v">{{ $prescription->frequency }}</span></div>
    <div class="detail-row"><span class="k">Duration</span><span class="v">{{ $prescription->duration_days ? $prescription->duration_days . ' days' : '—' }}</span></div>
    <div class="detail-row"><span class="k">Diagnosis</span><span class="v">{{ $prescription->diagnosis_context ?: '—' }}</span></div>
    @if($prescription->instructions)
      <div style="margin-top:14px;padding:12px;background:var(--grey-50);border-radius:8px;font-size:13px;color:var(--grey-700);line-height:1.6;">
        {{ $prescription->instructions }}
      </div>
    @endif
  </div>

  <div>
    <div class="card">
      <div style="font-size:11px;color:var(--grey-500);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px;">Dispense history</div>
      @forelse($prescription->dispenses as $d)
        <div style="padding:10px 0;border-bottom:1px solid var(--grey-100);">
          <strong style="font-size:13px;">{{ $d->facility->name }}</strong><br>
          <span style="font-size:11px;color:var(--grey-500);">{{ $d->created_at->format('d M Y') }} · {{ $d->quantity }} units</span>
          @if($d->price)
            <div style="font-size:11px;color:var(--grey-500);">NGN {{ number_format($d->price) }}</div>
          @endif
        </div>
      @empty
        <div style="font-size:12px;color:var(--grey-500);">Not yet dispensed.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
