@extends('layouts.app')
@section('title', 'Create User')
@section('topbar-title', 'Create User')

@section('content')
<div class="page-header-row">
  <div>
    <h1>Create New User</h1>
    <p>Add a new account to the platform.</p>
  </div>
  <a href="{{ route('admin.users') }}" class="btn btn-secondary">← Back to Users</a>
</div>

<div style="max-width:680px;">
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title">Account Details</div>
        <div class="card-sub">Fill in the information below to create the account.</div>
      </div>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="form-grid" style="margin-bottom:16px;">
          <div class="form-field span-2">
            <label>Full Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Amaka Okonkwo">
            @error('name')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Email Address *</label>
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="user@example.com">
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Phone Number *</label>
            <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="08012345678">
            @error('phone')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Role *</label>
            <select name="role" required>
              <option value="">— Select role —</option>
              <option value="patient"        {{ old('role')=='patient'        ? 'selected' : '' }}>Patient</option>
              <option value="provider"       {{ old('role')=='provider'       ? 'selected' : '' }}>Provider (Doctor/Nurse)</option>
              <option value="pharmacist"     {{ old('role')=='pharmacist'     ? 'selected' : '' }}>Pharmacist</option>
              <option value="facility_admin" {{ old('role')=='facility_admin' ? 'selected' : '' }}>Facility Admin</option>
              <option value="super_admin"    {{ old('role')=='super_admin'    ? 'selected' : '' }}>Super Admin</option>
            </select>
            @error('role')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field">
            <label>Password *</label>
            <input type="password" name="password" required placeholder="Min. 8 characters">
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
          </div>
          <div class="form-field span-2">
            <label>Confirm Password *</label>
            <input type="password" name="password_confirmation" required placeholder="Repeat password">
          </div>
        </div>
        <div class="alert navy" style="font-size:12px;">
          ℹ️ If role is <strong>Patient</strong>, a health record (MedID) will be automatically generated. Provider / Pharmacist profiles can be set up from the user detail page.
        </div>
        <div class="form-actions" style="margin-top:20px;">
          <button type="submit" class="btn btn-primary">Create User</button>
          <a href="{{ route('admin.users') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
