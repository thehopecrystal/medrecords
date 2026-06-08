@extends('layouts.app')
@section('title','Book Appointment')
@section('topbar-title','Book Appointment')

@section('content')
<div class="page-header-row">
  <div><h1>Book an Appointment</h1><p>Request a consultation with a registered provider.</p></div>
  <a href="{{ route('patient.appointments') }}" class="btn btn-secondary">← Back</a>
</div>

<div style="max-width:640px;">
  <div class="card">
    <div class="card-header"><div class="card-title">Appointment Details</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('patient.appointments.store') }}">
        @csrf
        <div class="form-grid" style="margin-bottom:16px;">
          <div class="form-field span-2">
            <label>Provider *</label>
            <select name="provider_id" required>
              <option value="">— Select a provider —</option>
              @foreach($providers as $prov)
              <option value="{{ $prov->id }}" {{ old('provider_id') == $prov->id ? 'selected':'' }}>
                {{ $prov->name }}
                @if($prov->provider) — {{ $prov->provider->specialty ?? '' }}
                  @if($prov->provider->facility) · {{ $prov->provider->facility->name }} @endif
                @endif
              </option>
              @endforeach
            </select>
            @error('provider_id')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Date *</label>
            <input type="date" name="appointment_date" value="{{ old('appointment_date') }}"
              min="{{ now()->addDay()->toDateString() }}" required>
            @error('appointment_date')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Time *</label>
            <input type="time" name="appointment_time" value="{{ old('appointment_time','09:00') }}" required>
            @error('appointment_time')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Appointment Type *</label>
            <select name="type" required>
              @foreach(['consultation'=>'Consultation','followup'=>'Follow-up','lab'=>'Lab / Test','dental'=>'Dental','eye'=>'Eye Care','procedure'=>'Procedure','other'=>'Other'] as $v=>$l)
              <option value="{{ $v }}" {{ old('type')==$v?'selected':'' }}>{{ $l }}</option>
              @endforeach
            </select>
            @error('type')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Reason for Visit *</label>
            <input type="text" name="reason" value="{{ old('reason') }}" maxlength="300" required placeholder="Brief description…">
            @error('reason')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field span-2">
            <label>Additional Notes</label>
            <textarea name="notes" rows="3" maxlength="500" placeholder="Symptoms, questions, anything else the provider should know…">{{ old('notes') }}</textarea>
          </div>
        </div>
        <div class="alert navy" style="font-size:12px;margin-bottom:16px;">ℹ️ Appointments are requests — the provider will confirm or suggest a different time. You will be notified in-app.</div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Request Appointment</button>
          <a href="{{ route('patient.appointments') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
