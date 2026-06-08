@extends('layouts.app')
@section('title', 'Request Consent')
@section('topbar-title', 'Request Consent')

@section('sidebar')
<aside class="sidebar">
  <div class="sidebar-brand">MedRecords<div class="sub">Provider</div></div>
  <nav class="sidebar-nav">
    <a href="{{ route('provider.dashboard') }}" class="nav-item">🏥 Dashboard</a>
    <a href="{{ route('provider.search') }}" class="nav-item active">🔍 Patient lookup</a>
  </nav>
  <div class="sidebar-footer"><div class="name">{{ auth()->user()->name }}</div>Provider</div>
</aside>
@endsection

@section('content')
<div style="margin-bottom:16px;">
  <a href="{{ route('provider.patient', $patient->id) }}" class="btn btn-secondary btn-sm">← Back to {{ $patient->user->name }}</a>
</div>

<div class="page-header">
  <h1>Request consent — {{ $patient->user->name }}</h1>
  <p>The patient will receive an in-app notification to approve or decline.</p>
</div>

<div class="card" style="max-width:560px;">
  <form method="POST" action="{{ route('provider.consent-request.store', $patient->id) }}">
    @csrf
    <div class="form-field" style="margin-bottom:16px;">
      <label>Access scope</label>
      <select name="scope" required>
        <option value="full">Full access — all categories</option>
        <option value="encounter">Encounters & visits only</option>
        <option value="prescription">Prescriptions only</option>
        <option value="lab">Lab results only</option>
        <option value="dental">Dental records only</option>
        <option value="eye_care">Eye care records only</option>
        <option value="vaccination">Vaccinations only</option>
      </select>
    </div>
    <div class="form-field" style="margin-bottom:16px;">
      <label>Duration (days)</label>
      <select name="expires_in" required>
        <option value="7">7 days — single visit</option>
        <option value="30">30 days — short-term care</option>
        <option value="90">90 days — episode of care</option>
        <option value="180">180 days — 6 months</option>
        <option value="365" selected>365 days — one year</option>
      </select>
    </div>
    <div class="alert navy" style="margin-bottom:16px;">
      <span>ℹ</span>
      <span>The patient must approve this request before you can access their record. They can revoke at any time.</span>
    </div>
    <div class="form-actions">
      <a href="{{ route('provider.patient', $patient->id) }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Send request to patient</button>
    </div>
  </form>
</div>
@endsection
