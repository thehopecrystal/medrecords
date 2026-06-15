@extends('layouts.app')
@section('title','My Appointments')
@section('topbar-title','Appointments')

@section('content')
<div class="page-header-row">
  <div><h1>My Appointments</h1><p>View upcoming and past appointments.</p></div>
  <a href="{{ route('patient.appointments.create') }}" class="btn btn-primary">+ Book Appointment</a>
</div>

{{-- Upcoming --}}
<div class="mb-28">
  <div class="section-label">Upcoming</div>
  @forelse($upcoming as $apt)
  <div class="card mb-12">
    <div class="card-pad card-row">
      <div class="apt-date-box">
        <div class="apt-date-day">{{ $apt->appointment_date->format('d') }}</div>
        <div class="apt-date-mon">{{ $apt->appointment_date->format('M') }}</div>
      </div>
      <div class="vdivider"></div>
      <div class="flex-1">
        <div class="person-name-lg text-navy">
          {{ ucfirst($apt->type) }} — Dr. {{ $apt->provider->name }}
        </div>
        <div class="td-muted">
          🕐 {{ \Carbon\Carbon::parse($apt->appointment_time)->format('g:i A') }}
          @if($apt->facility) · 🏥 {{ $apt->facility->name }} @endif
        </div>
        <div class="td-muted">{{ $apt->reason }}</div>
      </div>
      <div class="btn-group-row flex-shrink-0">
        @php $colors = ['pending'=>'amber','confirmed'=>'green','cancelled'=>'red','completed'=>'navy','no_show'=>'grey'] @endphp
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
  <div class="card">
    <div class="empty-state">
      <div class="empty-icon">📅</div>
      <h3>No upcoming appointments</h3>
      <p><a href="{{ route('patient.appointments.create') }}" class="text-navy-link">Book your first appointment</a></p>
    </div>
  </div>
  @endforelse
</div>

{{-- Past --}}
@if($past->count())
<div>
  <div class="section-label">Past Appointments</div>
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Date</th><th>Type</th><th>Provider</th><th>Reason</th><th>Status</th></tr></thead>
        <tbody>
          @foreach($past as $apt)
          <tr>
            <td class="td-date">{{ $apt->appointment_date->format('d M Y') }}</td>
            <td><span class="pill navy pill-xs">{{ ucfirst($apt->type) }}</span></td>
            <td class="td-sm">{{ $apt->provider->name }}</td>
            <td class="td-muted td-max-200">{{ $apt->reason }}</td>
            <td><span class="pill {{ $colors[$apt->status] ?? 'grey' }}">{{ ucfirst(str_replace('_',' ',$apt->status)) }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if($past->hasPages())<div class="pager-row">{{ $past->links() }}</div>@endif
  </div>
</div>
@endif
@endsection
