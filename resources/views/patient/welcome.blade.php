@extends('layouts.app')
@section('title', 'Welcome to MedRecords')
@section('topbar-title', 'Welcome')

@section('content')

<div class="welcome-hero">
  <div class="welcome-confetti">🎉</div>
  <h1 class="welcome-title">Welcome to MedRecords, {{ explode(' ', auth()->user()->name)[0] }}!</h1>
  <p class="welcome-sub">Your secure, patient-owned health record is ready.</p>
</div>

{{-- MedID reveal banner --}}
<div class="medid-reveal">
  <div class="medid-reveal-label">Your unique Medical ID</div>
  <div class="medid-reveal-value" id="medIdVal">{{ $patient->med_id }}</div>
  <div class="medid-reveal-hint">
    Share this with any healthcare provider or pharmacist in Nigeria — they can look up your records with your consent.
  </div>
  <div class="medid-reveal-btns">
    <button class="btn btn-primary" id="copyMedIdBtn">📋 Copy MedID</button>
    <a href="{{ route('patient.access-code') }}" class="btn btn-secondary">🔑 Generate access code</a>
  </div>
  <div class="medid-copy-confirm" id="copyConfirm">✓ Copied to clipboard!</div>
</div>

{{-- Profile completeness nudge --}}
@php
  $profile = [
    'Date of birth' => $patient->dob,
    'Gender'        => $patient->gender,
    'Blood type'    => $patient->blood_type,
    'State'         => $patient->state,
  ];
  $filled = collect($profile)->filter()->count();
  $total  = count($profile);
  $pct    = (int)(($filled / $total) * 100);
@endphp

@if($pct < 100)
<div class="card profile-nudge">
  <div class="profile-nudge-head">
    <div>
      <div class="card-title">Complete your health profile</div>
      <div class="card-sub">{{ $filled }}/{{ $total }} fields filled — providers see this at a glance</div>
    </div>
    <div class="pct-badge">{{ $pct }}%</div>
  </div>
  <div class="nudge-bar"><div class="nudge-fill" style="width:{{ $pct }}%;"></div></div>
  <div class="nudge-fields">
    @foreach($profile as $label => $val)
      <div class="nudge-field {{ $val ? 'done' : 'missing' }}">
        {{ $val ? '✓' : '○' }} {{ $label }}
      </div>
    @endforeach
  </div>
</div>
@endif

{{-- Quick start steps --}}
<h2 class="welcome-heading">Get started in 3 steps</h2>
<div class="quickstart-grid">
  <div class="qs-card qs-done">
    <div class="qs-num">✓</div>
    <div class="qs-title">Create account</div>
    <div class="qs-body">Your account and MedID are active and ready to use.</div>
  </div>
  <a href="{{ route('patient.access-code') }}" class="qs-card qs-todo" style="text-decoration:none;color:inherit;">
    <div class="qs-num">2</div>
    <div class="qs-title">Generate an access code</div>
    <div class="qs-body">Give a 6-digit code to your provider so they can look up your record. Codes expire in 10 minutes.</div>
    <div class="qs-action">Generate now →</div>
  </a>
  <a href="{{ route('patient.consents') }}" class="qs-card qs-todo" style="text-decoration:none;color:inherit;">
    <div class="qs-num">3</div>
    <div class="qs-title">Manage consent</div>
    <div class="qs-body">Approve or revoke provider access to your records. You are always in control.</div>
    <div class="qs-action">View consents →</div>
  </a>
</div>

{{-- Feature overview --}}
<h2 class="welcome-heading">What you can do on MedRecords</h2>
<div class="feature-grid">
  <div class="feature-item">
    <div class="feature-icon">📁</div>
    <div>
      <div class="feature-title">Your full health history</div>
      <div class="feature-desc">Encounters, prescriptions, lab results, vaccinations, dental and eye care — all in one place.</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">🔐</div>
    <div>
      <div class="feature-title">Consent-gated access</div>
      <div class="feature-desc">Providers can only view your records when you approve. Revoke any time, instantly.</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">👨‍👩‍👧</div>
    <div>
      <div class="feature-title">Manage family dependents</div>
      <div class="feature-desc">Add children or elderly relatives and oversee their health records from your account.</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">📋</div>
    <div>
      <div class="feature-title">Full audit trail</div>
      <div class="feature-desc">Every access to your records is logged. You always know who looked at what and when.</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">💊</div>
    <div>
      <div class="feature-title">Pharmacy verification</div>
      <div class="feature-desc">Pharmacists verify your prescription via MedRecords before dispensing — preventing fraud.</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">📲</div>
    <div>
      <div class="feature-title">Works everywhere</div>
      <div class="feature-desc">Access codes work via SMS — share a one-time code with your provider at reception.</div>
    </div>
  </div>
</div>

<div class="welcome-cta">
  <a href="{{ route('patient.dashboard') }}" class="btn btn-primary" style="font-size:15px;padding:12px 32px;">
    Go to my dashboard →
  </a>
</div>

@endsection

@push('scripts')
@vite('resources/js/pages/welcome.js')
@endpush
