@extends('layouts.app')
@section('title', 'Upload Lab Result')
@section('topbar-title', 'Upload Lab Result')

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
  <a href="{{ route('provider.patient', [$patient->id, 'tab' => 'labs']) }}" class="btn btn-secondary btn-sm">← Back to {{ $patient->user->name }}</a>
</div>
<div class="page-header">
  <h1>Upload lab result — {{ $patient->user->name }}</h1>
</div>

<div class="card" style="max-width:640px;">
  <form method="POST" action="{{ route('provider.lab.store', $patient->id) }}">
    @csrf
    <div class="form-grid">
      <div class="form-field">
        <label>Test name</label>
        <input type="text" name="test_name" placeholder="e.g. HbA1c" required>
      </div>
      <div class="form-field">
        <label>LOINC code (optional)</label>
        <input type="text" name="loinc_code" placeholder="e.g. 4548-4">
      </div>
      <div class="form-field">
        <label>Result</label>
        <input type="text" name="result" placeholder="e.g. 7.4" required>
      </div>
      <div class="form-field">
        <label>Unit</label>
        <input type="text" name="unit" placeholder="e.g. %">
      </div>
      <div class="form-field">
        <label>Reference range</label>
        <input type="text" name="reference_range" placeholder="e.g. < 5.7%">
      </div>
      <div class="form-field">
        <label>Flag</label>
        <select name="flag">
          <option value="normal">Normal</option>
          <option value="high">High ↑</option>
          <option value="low">Low ↓</option>
          <option value="critical">Critical ⚠</option>
        </select>
      </div>
      <div class="form-field">
        <label>Specimen collected on</label>
        <input type="date" name="collected_at" value="{{ date('Y-m-d') }}" required>
      </div>
      <div class="form-field full">
        <label>Notes</label>
        <textarea name="notes" rows="2" placeholder="Any additional clinical context…"></textarea>
      </div>
    </div>
    <div class="form-actions">
      <a href="{{ route('provider.patient', $patient->id) }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Upload result</button>
    </div>
  </form>
</div>
@endsection
