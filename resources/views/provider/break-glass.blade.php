@extends('layouts.app')
@section('title', 'Break-Glass Access')
@section('topbar-title', 'Emergency Access')


@section('content')
<div class="alert red" style="margin-bottom:20px;">
  <span>⚡</span>
  <span><strong>Break-glass access.</strong> Patient consent is not on file. Invoking emergency access will be reviewed by compliance. The patient will be notified immediately.</span>
</div>

<div style="margin-bottom:16px;">
  <a href="{{ route('provider.patient', $patient->id) }}" class="btn btn-secondary btn-sm">← Cancel</a>
</div>

<div class="card" style="max-width:640px;">
  <div class="card-header">
    <div class="card-title">Justification required — {{ $patient->user->name }}</div>
    <div class="card-sub">All break-glass events are logged, reviewed, and visible to the patient.</div>
  </div>

  <form method="POST" action="{{ route('provider.break-glass.store', $patient->id) }}">
    @csrf
    <div class="form-grid">
      <div class="form-field full">
        <label>Reason code</label>
        <select name="reason_code" required>
          <option value="">— select —</option>
          <option value="life_threatening_emergency">Life-threatening emergency — patient unconscious</option>
          <option value="suspected_overdose">Suspected overdose or poisoning</option>
          <option value="trauma">Major trauma requiring urgent history</option>
          <option value="other_emergency">Other clinical emergency</option>
        </select>
      </div>
      <div class="form-field full">
        <label>Justification (minimum 20 characters)</label>
        <textarea name="justification" rows="4" placeholder="Describe the clinical emergency in detail…" required minlength="20"></textarea>
      </div>
    </div>
    <div class="form-actions">
      <a href="{{ route('provider.patient', $patient->id) }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn" style="background:var(--red);color:#fff;">⚡ Proceed with break-glass</button>
    </div>
  </form>
</div>
@endsection
