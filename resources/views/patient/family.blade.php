@extends('layouts.app')
@section('title', 'My Family')
@section('topbar-title', 'My Family')

@section('content')
<div class="page-header-row">
  <div>
    <h1>My Family</h1>
    <p>Dependents under your guardianship</p>
  </div>
  <a href="{{ route('patient.family.add') }}" class="btn btn-primary">+ Add dependent</a>
</div>

@forelse($dependents as $dep)
  <div class="card mb-14">
    <div class="card-pad member-card-head">
      <div>
        <div class="member-card-name">
          {{ $dep->dependent->user->name }}
          @if($dep->dependent->dob)
            <span class="member-card-age">, {{ $dep->dependent->age }} yrs</span>
          @endif
        </div>
        <div class="member-card-sub">{{ ucfirst($dep->relationship) }} · MedID: {{ $dep->dependent->med_id }}</div>
        <div class="member-pills">
          @php
            $vaxDue = $dep->dependent ? $dep->dependent->vaccinations
              ->where('next_due', '!=', null)
              ->filter(fn($v) => $v->next_due->isPast() || $v->next_due->diffInDays() < 30)
              ->count() : 0;
          @endphp
          @if($vaxDue)
            <span class="pill amber">{{ $vaxDue }} vaccine{{ $vaxDue > 1 ? 's' : '' }} due soon</span>
          @else
            <span class="pill green">Vaccines up to date</span>
          @endif
          @if($dep->dependent->activePrescriptions->count())
            <span class="pill navy">{{ $dep->dependent->activePrescriptions->count() }} active Rx</span>
          @endif
        </div>
      </div>
      <div class="member-icon">{{ $dep->dependent->gender === 'female' ? '👧' : '👦' }}</div>
    </div>
  </div>
@empty
  <div class="card">
    <div class="empty-state">
      <div class="empty-icon">👨‍👩‍👧‍👦</div>
      <h3>No dependents added yet</h3>
      <p>Add children, elderly parents, or anyone under your care.</p>
      <a href="{{ route('patient.family.add') }}" class="btn btn-primary">+ Add first dependent</a>
    </div>
  </div>
@endforelse
@endsection
