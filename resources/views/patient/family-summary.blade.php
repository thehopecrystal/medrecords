@extends('layouts.app')
@section('title','Family Health Summary')
@section('topbar-title','Family Health Summary')

@section('content')
<div class="page-header"><h1>Family Health Summary</h1><p>A consolidated overview of health records for you and your dependents.</p></div>

@php
  $members = collect([['record' => $self, 'name' => $self->user->name, 'relation' => 'You', 'dob' => $self->dob]])
    ->concat($dependents->map(fn($d) => ['record' => $d, 'name' => $d->first_name.' '.$d->last_name, 'relation' => ucfirst($d->relationship ?? 'Dependent'), 'dob' => $d->dob ?? null]));
@endphp

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px;">
  @foreach($members as $m)
  @php $rec = $m['record']; @endphp
  <div class="card">
    <div class="card-pad">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
        <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#1E3A5F,#2C5F2D);color:#fff;font-size:16px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          {{ strtoupper(substr($m['name'],0,1)) }}
        </div>
        <div>
          <div style="font-size:15px;font-weight:700;color:var(--navy);">{{ $m['name'] }}</div>
          <div style="font-size:12px;color:var(--text-2);">{{ $m['relation'] }}
            @if($m['dob']) · Age {{ \Carbon\Carbon::parse($m['dob'])->age }} @endif
          </div>
        </div>
      </div>

      {{-- Active conditions --}}
      <div style="margin-bottom:12px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--text-2);margin-bottom:6px;">Active Conditions</div>
        @forelse($rec->conditions->whereIn('status',['active','chronic'])->take(3) as $c)
          <span class="pill red" style="margin:2px;">{{ $c->name }}</span>
        @empty
          <span style="font-size:12px;color:var(--text-3);">None recorded</span>
        @endforelse
      </div>

      {{-- Active prescriptions --}}
      <div style="margin-bottom:12px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--text-2);margin-bottom:6px;">Active Prescriptions</div>
        @forelse($rec->prescriptions->where('status','active')->take(3) as $rx)
          <span class="pill amber" style="margin:2px;">{{ $rx->drug_name }}</span>
        @empty
          <span style="font-size:12px;color:var(--text-3);">None</span>
        @endforelse
      </div>

      {{-- Vaccinations --}}
      <div style="margin-bottom:12px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--text-2);margin-bottom:6px;">Vaccinations</div>
        <span style="font-size:13px;font-weight:600;color:var(--green);">{{ $rec->vaccinations->count() }} recorded</span>
      </div>

      {{-- Last encounter --}}
      @if($rec->encounters->first())
      <div style="margin-bottom:12px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--text-2);margin-bottom:4px;">Last Visit</div>
        <div style="font-size:12px;color:var(--text-2);">{{ $rec->encounters->first()->created_at->format('d M Y') }} — {{ $rec->encounters->first()->type }}</div>
      </div>
      @endif

      @if($m['relation'] === 'You')
      <a href="{{ route('patient.record') }}" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;margin-top:4px;">View Full Records</a>
      @else
      <a href="{{ route('patient.family') }}" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;margin-top:4px;">Manage Dependents</a>
      @endif
    </div>
  </div>
  @endforeach
</div>
@endsection
