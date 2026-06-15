@extends('layouts.app')
@section('title','My Schedule')
@section('topbar-title','Appointments')

@section('content')
<div class="page-header-row">
  <div><h1>My Schedule</h1><p>Manage appointment requests and your daily schedule.</p></div>
  @if($pendingCount > 0)
    <span class="pill amber">{{ $pendingCount }} pending request{{ $pendingCount>1?'s':'' }}</span>
  @endif
</div>

{{-- Date picker --}}
<div class="card" style="margin-bottom:20px;">
  <div class="card-pad">
    <form method="GET" style="display:flex;align-items:flex-end;gap:12px;">
      <div class="form-field" style="flex:0 0 200px;">
        <label>View Schedule For</label>
        <input type="date" name="date" value="{{ $date }}" id="apptDateFilter">
      </div>
      <a href="{{ route('provider.appointments') }}" class="btn btn-secondary btn-sm" style="align-self:flex-end;">Today</a>
    </form>
  </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">

  {{-- Day schedule --}}
  <div class="card">
    <div class="card-header">
      <div><div class="card-title">{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</div><div class="card-sub">{{ $appointments->count() }} appointment(s)</div></div>
    </div>
    <div class="card-body" style="padding:0;">
      @forelse($appointments as $apt)
      <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 22px;border-bottom:1px solid var(--border);">
        <div style="width:52px;text-align:center;flex-shrink:0;">
          <div style="font-size:14px;font-weight:800;color:var(--navy);">{{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i') }}</div>
          <div style="font-size:10px;color:var(--text-3);">{{ \Carbon\Carbon::parse($apt->appointment_time)->format('A') }}</div>
        </div>
        <div style="flex:1;min-width:0;">
          <div style="font-size:14px;font-weight:600;color:var(--navy);">{{ $apt->patient->user->name }}</div>
          <div style="font-size:12px;color:var(--text-2);margin-top:2px;">
            <span class="pill navy" style="font-size:10px;">{{ ucfirst($apt->type) }}</span>
            &nbsp;{{ $apt->reason }}
          </div>
          @if($apt->notes)<div style="font-size:11px;color:var(--text-3);margin-top:3px;">{{ $apt->notes }}</div>@endif
        </div>
        <div style="display:flex;gap:6px;flex-shrink:0;flex-wrap:wrap;justify-content:flex-end;">
          @php $sc=['pending'=>'amber','confirmed'=>'green','cancelled'=>'red','completed'=>'navy','no_show'=>'grey'] @endphp
          <span class="pill {{ $sc[$apt->status] ?? 'grey' }}">{{ ucfirst(str_replace('_',' ',$apt->status)) }}</span>
          @if($apt->status === 'pending')
            <form method="POST" action="{{ route('provider.appointments.confirm', $apt->id) }}">@csrf
              <button class="btn btn-green btn-sm">✓ Confirm</button>
            </form>
          @endif
          @if($apt->status === 'confirmed')
            <form method="POST" action="{{ route('provider.appointments.complete', $apt->id) }}">@csrf
              <button class="btn btn-secondary btn-sm">✔ Complete</button>
            </form>
            <form method="POST" action="{{ route('provider.appointments.no-show', $apt->id) }}">@csrf
              <button class="btn btn-danger btn-sm">No-show</button>
            </form>
          @endif
          <a href="{{ route('provider.patient', $apt->patient->id) }}" class="btn btn-ghost btn-sm">View Patient</a>
        </div>
      </div>
      @empty
      <div class="empty-state"><div class="empty-icon">📅</div><h3>No appointments</h3><p>No appointments scheduled for this day.</p></div>
      @endforelse
    </div>
  </div>

  {{-- Upcoming sidebar --}}
  <div class="card">
    <div class="card-header"><div class="card-title">Upcoming (all pending)</div></div>
    <div class="card-body" style="padding:0;">
      @forelse($upcoming as $apt)
      <div style="padding:12px 18px;border-bottom:1px solid var(--border);font-size:12px;">
        <div style="font-weight:600;color:var(--text);">{{ $apt->patient->user->name }}</div>
        <div style="color:var(--text-2);margin-top:2px;">
          {{ $apt->appointment_date->format('d M') }} at {{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i A') }}
        </div>
        <div style="margin-top:4px;">
          <span class="pill amber" style="font-size:10px;">Pending</span>
          &nbsp;<span class="pill navy" style="font-size:10px;">{{ ucfirst($apt->type) }}</span>
        </div>
        <form method="POST" action="{{ route('provider.appointments.confirm', $apt->id) }}" style="margin-top:6px;">@csrf
          <button class="btn btn-green btn-sm" style="width:100%;">Confirm</button>
        </form>
      </div>
      @empty
      <div class="empty-state" style="padding:32px;"><div class="empty-icon" style="font-size:28px;">✅</div><p>No pending requests.</p></div>
      @endforelse
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
document.getElementById('apptDateFilter').addEventListener('change', function () { this.form.submit(); });
</script>
@endpush
