@extends('layouts.app')
@section('title','Family Health Summary')
@section('topbar-title','Family Health Summary')

@section('content')
<div class="page-header">
  <h1>Family Health Summary</h1>
  <p>A consolidated overview of health records for you and your dependents.</p>
</div>

@php
  $members = collect([['record' => $self, 'name' => $self->user->name, 'relation' => 'You', 'dob' => $self->dob]])
    ->concat($dependents->map(fn($d) => [
        'record'   => $d->dependent,
        'name'     => $d->dependent?->user?->name ?? 'Unknown',
        'relation' => ucfirst($d->relationship ?? 'Dependent'),
        'dob'      => $d->dependent?->dob ?? null,
    ]));
@endphp

<div class="family-grid">
  @foreach($members as $m)
  @php $rec = $m['record']; @endphp
  <div class="card">
    <div class="card-pad">
      <div class="member-head">
        <div class="member-avatar">{{ strtoupper(substr($m['name'], 0, 1)) }}</div>
        <div>
          <div class="member-name">{{ $m['name'] }}</div>
          <div class="member-meta">
            {{ $m['relation'] }}
            @if($m['dob']) · Age {{ \Carbon\Carbon::parse($m['dob'])->age }} @endif
          </div>
        </div>
      </div>

      <div class="member-stat-group">
        <div class="member-stat-label">Active Conditions</div>
        @forelse($rec->conditions->whereIn('status',['active','chronic'])->take(3) as $c)
          <span class="pill red" style="margin:2px;">{{ $c->name }}</span>
        @empty
          <span class="stat-none">None recorded</span>
        @endforelse
      </div>

      <div class="member-stat-group">
        <div class="member-stat-label">Active Prescriptions</div>
        @forelse($rec->prescriptions->where('status','active')->take(3) as $rx)
          <span class="pill amber" style="margin:2px;">{{ $rx->drug_name }}</span>
        @empty
          <span class="stat-none">None</span>
        @endforelse
      </div>

      <div class="member-stat-group">
        <div class="member-stat-label">Vaccinations</div>
        <span class="td-fw text-navy">{{ $rec->vaccinations->count() }} recorded</span>
      </div>

      @php $lastEncounter = $rec->encounters->first(); @endphp
      @if($lastEncounter)
      <div class="member-stat-group">
        <div class="member-stat-label">Last Visit</div>
        <div class="td-muted">{{ $lastEncounter->created_at->format('d M Y') }} — {{ $lastEncounter->type }}</div>
      </div>
      @endif

      @if($m['relation'] === 'You')
        <a href="{{ route('patient.record') }}" class="btn btn-secondary btn-sm btn-full mt-8">View Full Records</a>
      @else
        <a href="{{ route('patient.family') }}" class="btn btn-secondary btn-sm btn-full mt-8">Manage Dependents</a>
      @endif
    </div>
  </div>
  @endforeach
</div>
@endsection
