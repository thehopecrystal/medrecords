@extends('layouts.app')
@section('title', 'My Family')
@section('topbar-title', 'My Family')

@section('sidebar')
<aside class="sidebar">
  <div class="sidebar-brand">MedRecords<div class="sub">Patient Portal</div></div>
  <nav class="sidebar-nav">
    <a href="{{ route('patient.dashboard') }}" class="nav-item">🏠 Home</a>
    <a href="{{ route('patient.record') }}" class="nav-item">📋 My Record</a>
    <a href="{{ route('patient.consents') }}" class="nav-item">🔐 Consents</a>
    <a href="{{ route('patient.access-code') }}" class="nav-item">📱 Share Record</a>
    <a href="{{ route('patient.audit-log') }}" class="nav-item">👁 Access History</a>
    <a href="{{ route('patient.family') }}" class="nav-item active">👥 My Family</a>
  </nav>
  <div class="sidebar-footer"><div class="name">{{ auth()->user()->name }}</div>Patient</div>
</aside>
@endsection

@section('content')
<div class="page-header-row">
  <div>
    <h1>My Family</h1>
    <p style="color:var(--grey-500);font-size:13px;">Dependents under your guardianship</p>
  </div>
  <a href="{{ route('patient.family.add') }}" class="btn btn-primary">+ Add dependent</a>
</div>

@forelse($dependents as $dep)
  <div class="card" style="margin-bottom:14px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;">
      <div>
        <div style="font-size:16px;font-weight:700;color:var(--navy);">
          {{ $dep->dependent->user->name }}
          @if($dep->dependent->dob)
            <span style="font-weight:400;font-size:13px;color:var(--grey-500);">, {{ $dep->dependent->age }} yrs</span>
          @endif
        </div>
        <div style="font-size:12px;color:var(--grey-500);margin-top:2px;">
          {{ ucfirst($dep->relationship) }} · MedID: {{ $dep->dependent->med_id }}
        </div>
        <div style="margin-top:8px;display:flex;gap:6px;flex-wrap:wrap;">
          @php $vaxDue = $dep->dependent->vaccinations->where('next_due', '!=', null)->filter(fn($v) => $v->next_due->isPast() || $v->next_due->diffInDays() < 30)->count(); @endphp
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
      <div style="font-size:22px;">{{ $dep->dependent->gender === 'female' ? '👧' : '👦' }}</div>
    </div>
  </div>
@empty
  <div class="card" style="text-align:center;padding:40px;">
    <div style="font-size:32px;margin-bottom:12px;">👨‍👩‍👧‍👦</div>
    <div style="font-size:15px;font-weight:600;color:var(--navy);margin-bottom:6px;">No dependents added yet</div>
    <div style="font-size:13px;color:var(--grey-500);margin-bottom:16px;">Add children, elderly parents, or anyone under your care.</div>
    <a href="{{ route('patient.family.add') }}" class="btn btn-primary">+ Add first dependent</a>
  </div>
@endforelse
@endsection
