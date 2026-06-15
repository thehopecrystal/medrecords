@extends('layouts.app')
@section('title', 'Dashboard')
@section('topbar-title', 'Dashboard')

@section('content')
@php
  $firstName  = explode(' ', auth()->user()->name)[0];
  $hour       = now()->hour;
  $greeting   = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
  $nextAppt   = $upcomingAppointments->first();
  $activeMeds = $patient->activePrescriptions->count();
  $activeCond = $patient->activeConditions->count();
@endphp

{{-- ── Welcome banner ─────────────────────────────────────── --}}
<div class="dash-banner">
  <div class="dash-banner-left">
    <p class="dash-banner-greeting">{{ $greeting }},</p>
    <h1 class="dash-banner-name">{{ $firstName }}</h1>
    <p class="dash-banner-date">{{ now()->format('l, F j, Y') }}</p>
  </div>
  <div class="dash-banner-right">
    <div class="dash-medid">
      <p class="dash-medid-label">MedRecords ID</p>
      <p class="dash-medid-value">{{ $patient->med_id }}</p>
      <div class="dash-medid-row">
        @if($patient->dob)<span class="dash-medid-tag">{{ $patient->age }} yrs</span>@endif
        @if($patient->gender)<span class="dash-medid-tag">{{ ucfirst($patient->gender) }}</span>@endif
        @if($patient->blood_type)<span class="dash-medid-tag">{{ $patient->blood_type }}</span>@endif
        @if($patient->genotype)<span class="dash-medid-tag">{{ $patient->genotype }}</span>@endif
        @if($patient->nin_verified)<span class="dash-medid-tag verified">✓ NIN</span>@endif
      </div>
    </div>
  </div>
</div>

{{-- ── Health snapshot stats ───────────────────────────────── --}}
<div class="dash-stats">
  <a href="{{ route('patient.appointments') }}" class="dash-stat">
    <span class="dash-stat-icon">📅</span>
    <div>
      <p class="dash-stat-label">Next visit</p>
      @if($nextAppt)
        <p class="dash-stat-value">{{ $nextAppt->appointment_date->format('d M') }}</p>
        <p class="dash-stat-sub">{{ $nextAppt->appointment_date->format('Y') }}</p>
      @else
        <p class="dash-stat-value">None</p>
        <p class="dash-stat-sub">No upcoming</p>
      @endif
    </div>
  </a>

  <a href="{{ route('patient.record') }}" class="dash-stat">
    <span class="dash-stat-icon">💊</span>
    <div>
      <p class="dash-stat-label">Active meds</p>
      <p class="dash-stat-value">{{ $activeMeds }}</p>
      <p class="dash-stat-sub">prescription{{ $activeMeds !== 1 ? 's' : '' }}</p>
    </div>
  </a>

  <a href="{{ route('patient.record') }}" class="dash-stat">
    <span class="dash-stat-icon">🩺</span>
    <div>
      <p class="dash-stat-label">Conditions</p>
      <p class="dash-stat-value">{{ $activeCond }}</p>
      <p class="dash-stat-sub">being monitored</p>
    </div>
  </a>

  <a href="{{ route('patient.consents') }}" class="dash-stat">
    <span class="dash-stat-icon">🔐</span>
    <div>
      <p class="dash-stat-label">Consents</p>
      <p class="dash-stat-value">{{ $activeConsentCount }}</p>
      <p class="dash-stat-sub">{{ $activeConsentCount === 1 ? 'provider' : 'providers' }} with access</p>
    </div>
  </a>
</div>

{{-- ── Quick actions ───────────────────────────────────────── --}}
<div class="dash-actions">
  <a href="{{ route('patient.access-code') }}" class="dash-action">
    <span class="dash-action-icon">📱</span>
    <span class="dash-action-label">Share<br>Record</span>
  </a>
  <a href="{{ route('patient.record') }}" class="dash-action">
    <span class="dash-action-icon">📋</span>
    <span class="dash-action-label">My Health<br>Record</span>
  </a>
  <a href="{{ route('patient.vitals') }}" class="dash-action">
    <span class="dash-action-icon">❤️</span>
    <span class="dash-action-label">Vitals<br>Diary</span>
  </a>
  <a href="{{ route('patient.refills') }}" class="dash-action">
    <span class="dash-action-icon">🔄</span>
    <span class="dash-action-label">Refill<br>Request</span>
  </a>
  <a href="{{ route('patient.audit-log') }}" class="dash-action">
    <span class="dash-action-icon">👁</span>
    <span class="dash-action-label">Access<br>History</span>
  </a>
</div>

{{-- ── Main 2-column grid ──────────────────────────────────── --}}
<div class="dash-grid-2-1">

  {{-- Left: upcoming appointments + recent activity --}}
  <div class="col-stack">

    <article class="card">
      <header class="card-header">
        <div class="card-header-group">
          <div class="card-icon navy">📅</div>
          <div>
            <h3 class="card-title">Upcoming Appointments</h3>
            <p class="card-sub">Your scheduled visits</p>
          </div>
        </div>
        <a href="{{ route('patient.appointments') }}" class="btn btn-ghost btn-sm">View all</a>
      </header>
      <div class="card-body">
        @forelse($upcomingAppointments as $appt)
          <div class="dash-appt">
            <div class="dash-appt-cal">
              <span class="dash-appt-cal-day">{{ $appt->appointment_date->format('d') }}</span>
              <span class="dash-appt-cal-mon">{{ $appt->appointment_date->format('M') }}</span>
            </div>
            <div class="dash-appt-info">
              <p class="dash-appt-title">{{ $appt->reason ?: ucfirst($appt->type ?? 'Appointment') }}</p>
              <p class="dash-appt-meta">
                @if($appt->appointment_time){{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}@endif
                @if($appt->provider) &middot; Dr. {{ $appt->provider->name }}@endif
                @if($appt->facility) &middot; {{ $appt->facility->name }}@endif
              </p>
            </div>
            <div class="dash-appt-badge">
              <span class="pill {{ $appt->status === 'confirmed' ? 'green' : 'amber' }}">{{ ucfirst($appt->status) }}</span>
            </div>
          </div>
        @empty
          <div class="empty-state empty-state-sm">
            <p class="empty-icon">📅</p>
            <h3>No upcoming appointments</h3>
            <p>Nothing scheduled yet</p>
          </div>
        @endforelse
      </div>
    </article>

    <article class="card">
      <header class="card-header">
        <div class="card-header-group">
          <div class="card-icon navy">👁</div>
          <div>
            <h3 class="card-title">Recent Activity</h3>
            <p class="card-sub">Who accessed your record recently</p>
          </div>
        </div>
        <a href="{{ route('patient.audit-log') }}" class="btn btn-ghost btn-sm">View all</a>
      </header>
      <div class="card-body-0">
        @forelse($recentActivity as $log)
          <div class="list-row">
            <div class="list-left">
              <div class="avatar avatar-sm">{{ strtoupper(substr($log->actor?->name ?? 'S', 0, 1)) }}</div>
              <div>
                <p class="person-name">{{ $log->description }}</p>
                <p class="person-sub">{{ $log->actor?->name ?? 'System' }}</p>
              </div>
            </div>
            <span class="td-date">{{ $log->created_at->diffForHumans() }}</span>
          </div>
        @empty
          <p class="card-empty-body text-center">No recent activity.</p>
        @endforelse
      </div>
    </article>

  </div>

  {{-- Right: vitals + health summary + prescriptions --}}
  <div class="col-stack">

    <article class="card">
      <header class="card-header">
        <div class="card-header-group">
          <div class="card-icon red">❤️</div>
          <div>
            <h3 class="card-title">Latest Vitals</h3>
            <p class="card-sub">
              @if($latestVitals)
                Recorded {{ $latestVitals->logged_at->diffForHumans() }}
              @else
                No readings on record
              @endif
            </p>
          </div>
        </div>
        <a href="{{ route('patient.vitals') }}" class="btn btn-ghost btn-sm">Log vitals</a>
      </header>
      <div class="card-body">
        @if($latestVitals)
          <div class="dash-vitals-grid">
            @if($latestVitals->bp_systolic && $latestVitals->bp_diastolic)
              <div class="dash-vital dash-vital-wide">
                <p class="dash-vital-label">Blood pressure</p>
                <p class="dash-vital-value">{{ $latestVitals->bp_systolic }}/{{ $latestVitals->bp_diastolic }}<span class="dash-vital-unit">mmHg</span></p>
              </div>
            @endif
            @if($latestVitals->heart_rate)
              <div class="dash-vital">
                <p class="dash-vital-label">Heart rate</p>
                <p class="dash-vital-value">{{ $latestVitals->heart_rate }}<span class="dash-vital-unit">bpm</span></p>
              </div>
            @endif
            @if($latestVitals->temperature_c)
              <div class="dash-vital">
                <p class="dash-vital-label">Temperature</p>
                <p class="dash-vital-value">{{ $latestVitals->temperature_c }}<span class="dash-vital-unit">°C</span></p>
              </div>
            @endif
            @if($latestVitals->oxygen_saturation)
              <div class="dash-vital">
                <p class="dash-vital-label">SpO₂</p>
                <p class="dash-vital-value">{{ $latestVitals->oxygen_saturation }}<span class="dash-vital-unit">%</span></p>
              </div>
            @endif
            @if($latestVitals->weight_kg)
              <div class="dash-vital">
                <p class="dash-vital-label">Weight</p>
                <p class="dash-vital-value">{{ $latestVitals->weight_kg }}<span class="dash-vital-unit">kg</span></p>
              </div>
            @endif
          </div>
        @else
          <div class="empty-state empty-state-sm">
            <p class="empty-icon">❤️</p>
            <h3>No vitals recorded</h3>
            <p><a href="{{ route('patient.vitals') }}" class="text-navy-link">Log your first reading</a></p>
          </div>
        @endif
      </div>
    </article>

    <article class="card">
      <header class="card-header">
        <div class="card-header-group">
          <div class="card-icon amber">🩺</div>
          <div>
            <h3 class="card-title">Health Summary</h3>
            <p class="card-sub">Active conditions &amp; allergies</p>
          </div>
        </div>
        <a href="{{ route('patient.record') }}" class="btn btn-ghost btn-sm">Full record</a>
      </header>
      <div class="card-body">
        @if($patient->activeAllergies->isEmpty() && $patient->activeConditions->isEmpty())
          <div class="empty-state empty-state-sm">
            <p class="empty-icon">✅</p>
            <h3>All clear</h3>
            <p>No active conditions or allergies on record</p>
          </div>
        @else
          @if($patient->activeAllergies->count())
            <p class="dash-section-label">Allergies</p>
            @foreach($patient->activeAllergies->take(3) as $allergy)
              <div class="dash-health-item">
                <span class="dash-health-item-name">{{ $allergy->allergen }}</span>
                <span class="pill {{ $allergy->severityColor ?? 'grey' }} pill-xs">{{ ucfirst($allergy->severity ?? 'Unknown') }}</span>
              </div>
            @endforeach
          @endif
          @if($patient->activeConditions->count())
            <p class="dash-section-label{{ $patient->activeAllergies->count() ? ' mt-12' : '' }}">Conditions</p>
            @foreach($patient->activeConditions->take(4) as $cond)
              <div class="dash-health-item">
                <span class="dash-health-item-name">{{ $cond->name }}</span>
                <span class="pill {{ $cond->status === 'chronic' ? 'amber' : 'navy' }} pill-xs">{{ ucfirst($cond->status) }}</span>
              </div>
            @endforeach
          @endif
        @endif
      </div>
    </article>

    @if($patient->activePrescriptions->count())
      <article class="card">
        <header class="card-header">
          <div class="card-header-group">
            <div class="card-icon green">💊</div>
            <div>
              <h3 class="card-title">Active Prescriptions</h3>
              <p class="card-sub">Current medications</p>
            </div>
          </div>
        </header>
        <div class="card-body">
          @foreach($patient->activePrescriptions->take(4) as $rx)
            <div class="dash-health-item">
              <div class="dash-health-item-info">
                <p class="dash-health-item-name">{{ $rx->drug_name }}</p>
                @if($rx->dose || $rx->frequency)
                  <p class="person-sub">{{ collect([$rx->dose, $rx->frequency])->filter()->implode(' · ') }}</p>
                @endif
              </div>
              <span class="pill green pill-xs">Active</span>
            </div>
          @endforeach
        </div>
      </article>
    @endif

  </div>
</div>

@endsection
