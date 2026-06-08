@extends('layouts.app')
@section('title', 'Add Dependent')
@section('topbar-title', 'Add Dependent')


@section('content')
<div style="margin-bottom:16px;">
  <a href="{{ route('patient.family') }}" class="btn btn-secondary btn-sm">← Back to My Family</a>
</div>
<div class="page-header"><h1>Add dependent</h1><p>Create a MedRecords account for a family member under your care.</p></div>

@if($errors->any())
  <div class="alert red"><span>✗</span><span>{{ $errors->first() }}</span></div>
@endif

<div class="card" style="max-width:540px;">
  <form method="POST" action="{{ route('patient.family.store') }}">
    @csrf
    <div class="form-grid">
      <div class="form-field full">
        <label>Full name</label>
        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Chioma Okonkwo" required>
      </div>
      <div class="form-field">
        <label>Date of birth</label>
        <input type="date" name="dob" value="{{ old('dob') }}" required>
      </div>
      <div class="form-field">
        <label>Gender</label>
        <select name="gender" required>
          <option value="">— select —</option>
          <option value="female">Female</option>
          <option value="male">Male</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-field full">
        <label>Relationship</label>
        <select name="relationship" required>
          <option value="">— select —</option>
          <option value="son">Son</option>
          <option value="daughter">Daughter</option>
          <option value="mother">Mother</option>
          <option value="father">Father</option>
          <option value="spouse">Spouse</option>
          <option value="ward">Ward / guardianship</option>
          <option value="other">Other</option>
        </select>
      </div>
    </div>
    <div class="alert navy" style="margin-top:16px;margin-bottom:0;">
      <span>ℹ</span>
      <span>A MedRecords account will be created for this dependent. You will manage their record until they turn 16, at which point they can take ownership.</span>
    </div>
    <div class="form-actions">
      <a href="{{ route('patient.family') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Add dependent</button>
    </div>
  </form>
</div>
@endsection
