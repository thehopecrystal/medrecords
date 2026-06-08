@extends('layouts.app')
@section('title', 'New Referral')
@section('topbar-title', 'New Referral')

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
  <h1>Referral — {{ $patient->user->name }}</h1>
  <p>Create a structured referral to another specialist or facility</p>
</div>

<div class="card" style="max-width:640px;">
  <form method="POST" action="{{ route('provider.referral.store', $patient->id) }}">
    @csrf
    <div class="form-grid">
      <div class="form-field">
        <label>Refer to (facility / specialist)</label>
        <input type="text" name="referred_to" placeholder="e.g. Lagos University Teaching Hospital" required>
      </div>
      <div class="form-field">
        <label>Specialty</label>
        <input type="text" name="specialty" placeholder="e.g. Endocrinology" required>
      </div>
      <div class="form-field full">
        <label>Reason for referral</label>
        <textarea name="reason" rows="3" placeholder="Clinical reason for this referral…" required></textarea>
      </div>
      <div class="form-field full">
        <label>Additional notes (optional)</label>
        <textarea name="notes" rows="2" placeholder="Any further notes for the receiving specialist…"></textarea>
      </div>
    </div>
    <div class="form-actions">
      <a href="{{ route('provider.patient', $patient->id) }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Create referral</button>
    </div>
  </form>
</div>
@endsection
