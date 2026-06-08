@extends('layouts.app')
@section('title', 'Welcome to MedRecords')
@section('topbar-title', 'Welcome')

@section('sidebar')
<aside class="sidebar">
  <div class="sidebar-brand">MedRecords<div class="sub">Patient Portal</div></div>
  <nav class="sidebar-nav">
    <a href="{{ route('patient.dashboard') }}" class="nav-item">🏠 Dashboard</a>
    <a href="{{ route('patient.record') }}"    class="nav-item">📁 My Records</a>
    <a href="{{ route('patient.consents') }}"  class="nav-item">🔐 Consent</a>
    <a href="{{ route('patient.access-code') }}" class="nav-item">🔑 Access Code</a>
    <a href="{{ route('patient.family') }}"    class="nav-item">👨‍👩‍👧 Family</a>
    <a href="{{ route('patient.audit-log') }}" class="nav-item">📋 Audit log</a>
  </nav>
  <div class="sidebar-footer">
    <div class="name">{{ auth()->user()->name }}</div>
    {{ $patient->med_id }}
  </div>
</aside>
@endsection

@section('content')

<div class="welcome-hero">
  <div class="confetti-emoji">🎉</div>
  <h1 class="welcome-title">Welcome to MedRecords, {{ explode(' ', auth()->user()->name)[0] }}!</h1>
  <p class="welcome-sub">Your secure, patient-owned health record is ready.</p>
</div>

{{-- MedID card --}}
<div class="medid-reveal">
  <div class="medid-label">Your unique Medical ID</div>
  <div class="medid-value" id="medIdVal">{{ $patient->med_id }}</div>
  <div class="medid-hint">
    Share this with any healthcare provider or pharmacist in Nigeria — they can look up your records with your consent.
  </div>
  <div style="display:flex;gap:10px;justify-content:center;margin-top:16px;flex-wrap:wrap;">
    <button class="btn btn-primary" onclick="copyMedId()">📋 Copy MedID</button>
    <a href="{{ route('patient.access-code') }}" class="btn btn-secondary" style="text-decoration:none;">🔑 Generate access code</a>
  </div>
  <div id="copyConfirm" style="display:none;margin-top:10px;font-size:13px;color:var(--green);font-weight:600;">✓ Copied to clipboard!</div>
</div>

{{-- Profile completeness --}}
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
  <div class="nudge-bar"><div class="nudge-fill" style="width:{{ $pct }}%"></div></div>
  <div class="nudge-fields">
    @foreach($profile as $label => $val)
      <div class="nudge-field {{ $val ? 'done' : 'missing' }}">
        {{ $val ? '✓' : '○' }} {{ $label }}
      </div>
    @endforeach
  </div>
</div>
@endif

{{-- Quick start --}}
<h2 class="section-heading">Get started in 3 steps</h2>
<div class="quickstart-grid">
  <div class="qs-card done">
    <div class="qs-num">✓</div>
    <div class="qs-title">Create account</div>
    <div class="qs-body">Your account and MedID are active and ready to use.</div>
  </div>
  <a href="{{ route('patient.access-code') }}" class="qs-card todo" style="text-decoration:none;color:inherit;">
    <div class="qs-num">2</div>
    <div class="qs-title">Generate an access code</div>
    <div class="qs-body">Give a 6-digit code to your provider so they can look up your record. Codes expire in 10 minutes.</div>
    <div class="qs-action">Generate now →</div>
  </a>
  <a href="{{ route('patient.consents') }}" class="qs-card todo" style="text-decoration:none;color:inherit;">
    <div class="qs-num">3</div>
    <div class="qs-title">Manage consent</div>
    <div class="qs-body">Approve or revoke provider access to your records. You are always in control.</div>
    <div class="qs-action">View consents →</div>
  </a>
</div>

{{-- Feature overview --}}
<h2 class="section-heading">What you can do on MedRecords</h2>
<div class="feature-grid">
  <div class="feature-item">
    <div class="feature-icon">📁</div>
    <div class="feature-body">
      <div class="feature-title">Your full health history</div>
      <div class="feature-desc">Encounters, prescriptions, lab results, vaccinations, dental and eye care — all in one place.</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">🔐</div>
    <div class="feature-body">
      <div class="feature-title">Consent-gated access</div>
      <div class="feature-desc">Providers can only view your records when you approve. Revoke any time, instantly.</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">👨‍👩‍👧</div>
    <div class="feature-body">
      <div class="feature-title">Manage family dependents</div>
      <div class="feature-desc">Add children or elderly relatives and oversee their health records from your account.</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">📋</div>
    <div class="feature-body">
      <div class="feature-title">Full audit trail</div>
      <div class="feature-desc">Every access to your records is logged. You always know who looked at what and when.</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">💊</div>
    <div class="feature-body">
      <div class="feature-title">Pharmacy verification</div>
      <div class="feature-desc">Pharmacists verify your prescription via MedRecords before dispensing — preventing fraud.</div>
    </div>
  </div>
  <div class="feature-item">
    <div class="feature-icon">📲</div>
    <div class="feature-body">
      <div class="feature-title">Works everywhere</div>
      <div class="feature-desc">Access codes work via SMS and USSD (*384*CODE#) — even without internet at the clinic.</div>
    </div>
  </div>
</div>

<div style="text-align:center;margin:32px 0 8px;">
  <a href="{{ route('patient.dashboard') }}" class="btn btn-primary" style="font-size:15px;padding:12px 32px;text-decoration:none;">
    Go to my dashboard →
  </a>
</div>

<style>
/* ─── Hero ─────────────────────────────────── */
.welcome-hero {
  text-align: center;
  padding: 32px 0 20px;
}
.confetti-emoji { font-size: 40px; margin-bottom: 10px; }
.welcome-title {
  font-size: 26px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 6px;
}
.welcome-sub { font-size: 15px; color: var(--grey-500); }

/* ─── MedID reveal ─────────────────────────── */
.medid-reveal {
  background: linear-gradient(135deg, #1E3A5F 0%, #2C5178 100%);
  color: #fff;
  border-radius: 16px;
  padding: 28px 32px;
  text-align: center;
  margin: 20px 0 28px;
}
.medid-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  opacity: 0.7;
  margin-bottom: 10px;
}
.medid-value {
  font-size: 32px;
  font-weight: 800;
  font-family: monospace;
  letter-spacing: 0.12em;
  color: #fff;
  text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.medid-hint {
  font-size: 13px;
  opacity: 0.8;
  margin-top: 12px;
  max-width: 420px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.5;
}

/* ─── Profile nudge ────────────────────────── */
.profile-nudge { margin-bottom: 28px; }
.profile-nudge-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}
.pct-badge {
  font-size: 22px;
  font-weight: 800;
  color: var(--navy);
}
.nudge-bar {
  height: 6px;
  background: var(--grey-200);
  border-radius: 6px;
  overflow: hidden;
  margin-bottom: 14px;
}
.nudge-fill {
  height: 100%;
  background: var(--navy);
  border-radius: 6px;
  transition: width 0.5s ease;
}
.nudge-fields { display: flex; flex-wrap: wrap; gap: 8px; }
.nudge-field {
  font-size: 12px;
  padding: 4px 10px;
  border-radius: 20px;
  font-weight: 500;
}
.nudge-field.done { background: #EDF7ED; color: #2C5F2D; }
.nudge-field.missing { background: var(--grey-100); color: var(--grey-500); }

/* ─── Section heading ──────────────────────── */
.section-heading {
  font-size: 15px;
  font-weight: 700;
  color: var(--grey-900, #1A1F26);
  margin: 0 0 14px;
}

/* ─── Quick start ──────────────────────────── */
.quickstart-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-bottom: 32px;
}
.qs-card {
  border: 2px solid var(--grey-200);
  border-radius: 12px;
  padding: 18px;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.qs-card.todo:hover { border-color: var(--navy); box-shadow: 0 4px 12px rgba(30,58,95,0.08); }
.qs-card.done { background: #EDF7ED; border-color: #2C5F2D44; }
.qs-num {
  font-size: 18px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 8px;
}
.qs-card.done .qs-num { color: var(--green); }
.qs-title { font-size: 14px; font-weight: 700; color: var(--grey-900, #1A1F26); margin-bottom: 6px; }
.qs-body { font-size: 12px; color: var(--grey-500); line-height: 1.5; }
.qs-action { font-size: 12px; font-weight: 600; color: var(--navy); margin-top: 10px; }

/* ─── Feature grid ─────────────────────────── */
.feature-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin-bottom: 8px;
}
.feature-item {
  display: flex;
  gap: 14px;
  padding: 16px;
  border: 1px solid var(--grey-200);
  border-radius: 10px;
}
.feature-icon { font-size: 22px; flex-shrink: 0; }
.feature-title { font-size: 13px; font-weight: 700; margin-bottom: 4px; }
.feature-desc { font-size: 12px; color: var(--grey-500); line-height: 1.5; }

/* ─── Button overrides ─────────────────────── */
.btn-secondary {
  background: rgba(255,255,255,0.15);
  color: #fff;
  border: 1px solid rgba(255,255,255,0.4);
  border-radius: 8px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  font-family: inherit;
  display: inline-flex;
  align-items: center;
  transition: background 0.15s;
}
.btn-secondary:hover { background: rgba(255,255,255,0.25); }

@media (max-width: 640px) {
  .quickstart-grid { grid-template-columns: 1fr; }
  .feature-grid { grid-template-columns: 1fr; }
  .medid-value { font-size: 22px; }
}
</style>

<script>
function copyMedId() {
  const val = document.getElementById('medIdVal').textContent.trim();
  navigator.clipboard.writeText(val).then(function() {
    const confirm = document.getElementById('copyConfirm');
    confirm.style.display = 'block';
    setTimeout(function() { confirm.style.display = 'none'; }, 3000);
  });
}
</script>

@endsection
