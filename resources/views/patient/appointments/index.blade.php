@extends('layouts.app')
@section('title','My Appointments')
@section('topbar-title','Appointments')

@section('content')
<div class="page-header-row">
  <div><h1>My Appointments</h1><p>View upcoming and past appointments.</p></div>
  <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary">+ Book Appointment</a>
</div>

{{-- Upcoming --}}
<div style="margin-bottom:28px;">
  <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-2);margin-bottom:14px;">Upcoming</div>
  @forelse($upcoming as $apt)
  <div class="card" style="margin-bottom:12px;">
    <div class="card-pad" style="display:flex;align-items:center;gap:16px;">
      <div style="width:56px;text-align:center;flex-shrink:0;">
        <div style="font-size:22px;font-weight:800;color:var(--navy);line-height:1;">{{ $apt->appointment_date->format('d') }}</div>
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--text-2);">{{ $apt->appointment_date->format('M') }}</div>
      </div>
      <div style="width:1px;background:var(--border);align-self:stretch;flex-shrink:0;"></div>
      <div style="flex:1;min-width:0;">
        <div style="font-size:14px;font-weight:700;color:var(--navy);">
          {{ ucfirst($apt->type) }} — Dr. {{ $apt->provider->name }}
        </div>
        <div style="font-size:12px;color:var(--text-2);margin-top:3px;">
          🕐 {{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i A') }}
          @if($apt->facility) · 🏥 {{ $apt->facility->name }} @endif
        </div>
        <div style="font-size:12px;color:var(--text-2);margin-top:2px;">{{ $apt->reason }}</div>
      </div>
      <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
        @php $colors=['pending'=>'amber','confirmed'=>'green','cancelled'=>'red','completed'=>'navy','no_show'=>'grey'] @endphp
        <span class="pill {{ $colors[$apt->status] ?? 'grey' }}">{{ ucfirst(str_replace('_',' ',$apt->status)) }}</span>
        @if($apt->isPending() || $apt->isConfirmed())
        <form method="POST" action="{{ route('patient.appointments.cancel', $apt->id) }}"
              data-confirm="Cancel this appointment?"
              data-confirm-ok="Cancel">
          @csrf
          <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
        </form>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div class="card"><div class="empty-state"><div class="empty-icon">📅</div><h3>No upcoming appointments</h3><p><a href="{{ route('patient.appointments.create') }}" style="color:var(--navy);">Book your first appointment</a></p></div></div>
  @endforelse
</div>

{{-- Past --}}
@if($past->count())
<div>
  <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-2);margin-bottom:14px;">Past Appointments</div>
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Date</th><th>Type</th><th>Provider</th><th>Reason</th><th>Status</th></tr></thead>
        <tbody>
          @foreach($past as $apt)
          <tr>
            <td style="font-size:13px;">{{ $apt->appointment_date->format('d M Y') }}</td>
            <td><span class="pill navy" style="font-size:10px;">{{ ucfirst($apt->type) }}</span></td>
            <td>{{ $apt->provider->name }}</td>
            <td style="color:var(--text-2);font-size:12px;max-width:200px;">{{ $apt->reason }}</td>
            <td><span class="pill {{ $colors[$apt->status] ?? 'grey' }}">{{ ucfirst(str_replace('_',' ',$apt->status)) }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if($past->hasPages())<div style="padding:14px 22px;border-top:1px solid var(--border);">{{ $past->links() }}</div>@endif
  </div>
</div>
@endif
@endsection
