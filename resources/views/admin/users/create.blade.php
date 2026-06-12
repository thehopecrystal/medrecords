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

<div class="max-680">
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
        <div class="form-grid mb-16">
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
            <select name="role" id="roleSelect" required onchange="toggleProviderFields()">
              <option value="">— Select role —</option>
              <option value="patient"    {{ old('role')=='patient'    ? 'selected' : '' }}>Patient</option>
              <option value="provider"   {{ old('role')=='provider'   ? 'selected' : '' }}>Provider (Doctor / Nurse)</option>
              <option value="pharmacist" {{ old('role')=='pharmacist' ? 'selected' : '' }}>Pharmacist</option>
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

        {{-- Provider / Pharmacist fields --}}
        <div id="providerFields" class="mb-16 js-hidden">
          <div class="form-divider"></div>
          <div class="form-grid">
            <div class="form-field span-2">
              <label>Facility *</label>
              <select name="facility_id">
                <option value="">— Select facility —</option>
                @foreach($facilities as $f)
                  <option value="{{ $f->id }}" {{ old('facility_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                @endforeach
              </select>
              @error('facility_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
              <label>Specialty *</label>
              <input type="text" name="specialty" value="{{ old('specialty') }}" placeholder="e.g. General Practice, Pharmacy">
              @error('specialty')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
              <label>Licence / MDCN No.</label>
              <input type="text" name="license_no" value="{{ old('license_no') }}" placeholder="Optional">
            </div>
          </div>
        </div>

        <div class="alert navy">
          ℹ️ <strong>Patient</strong>: MedID auto-generated. &nbsp;|&nbsp; <strong>Provider / Pharmacist</strong>: facility and specialty required.
        </div>
        <div class="form-actions mt-20">
          <button type="submit" class="btn btn-primary">Create User</button>
          <a href="{{ route('admin.users') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script nonce="{{ $cspNonce }}">
function toggleProviderFields() {
  var role = document.getElementById('roleSelect').value;
  var show = role === 'provider' || role === 'pharmacist';
  document.getElementById('providerFields').classList.toggle('js-hidden', !show);
}
document.addEventListener('DOMContentLoaded', toggleProviderFields);
</script>
@endsection
