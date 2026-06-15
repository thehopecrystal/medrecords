@extends('layouts.app')
@section('title', 'Edit Profile')
@section('topbar-title', 'My Profile')

@section('content')
<div class="page-header-row">
  <div>
    <h1>Edit Profile</h1>
    <p>Keep your personal and medical information up to date</p>
  </div>
  <div>
    <span class="pill navy">MedID: {{ $patient->med_id }}</span>
  </div>
</div>

<div class="profile-layout">

  {{-- ── Sidebar nav ─────────────────────────────── --}}
  <div>
    <div class="card">
      <div class="profile-sidebar-body">

        {{-- Avatar --}}
        <div class="profile-avatar-wrap">
          @php
            $parts    = explode(' ', $user->name);
            $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1] ?? '', 0, 1));
          @endphp
          @if($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Profile photo" class="profile-avatar-photo">
          @else
            <div class="profile-avatar-initials">{{ $initials }}</div>
          @endif
          <div class="profile-name">{{ $user->name }}</div>
          <div class="profile-email">{{ $user->email }}</div>
          <div style="margin-top:8px;">
            @if($patient->nin_verified)
              <span class="pill green">✓ NIN Verified</span>
            @else
              <span class="pill grey">NIN unverified</span>
            @endif
          </div>
          <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
            @csrf
            <label class="profile-photo-btn">
              📷 Change photo
              <input type="file" name="avatar" accept="image/*" style="display:none;" data-auto-submit>
            </label>
          </form>
          @error('avatar')<div class="form-error mt-12">{{ $message }}</div>@enderror
        </div>

        {{-- Profile completeness --}}
        @php
          $fields = [
            'Date of birth'     => $patient->dob,
            'Gender'            => $patient->gender,
            'Blood type'        => $patient->blood_type,
            'State'             => $patient->state,
            'Emergency contact' => $patient->emergency_contact_name,
          ];
          $filled = collect($fields)->filter()->count();
          $pct    = (int)(($filled / count($fields)) * 100);
          $pctClass = $pct >= 80 ? 'green' : ($pct >= 50 ? 'amber' : 'red');
        @endphp
        <div class="profile-completeness">
          <div class="profile-completeness-header">
            <span class="profile-completeness-label">Profile completeness</span>
            <span class="profile-completeness-pct">{{ $pct }}%</span>
          </div>
          <div class="profile-progress-track">
            <div class="profile-progress-fill {{ $pctClass }}" style="width:{{ $pct }}%;"></div>
          </div>
          <div class="profile-field-list">
            @foreach($fields as $label => $val)
              <div class="profile-field-item {{ $val ? 'profile-field-done' : 'profile-field-empty' }}">
                {{ $val ? '✓' : '○' }} {{ $label }}
              </div>
            @endforeach
          </div>
        </div>

        <nav class="profile-nav">
          <a href="#personal"   class="profile-nav-item active">👤 Personal Info</a>
          <a href="#medical"    class="profile-nav-item">🩺 Medical Profile</a>
          <a href="#emergency"  class="profile-nav-item">🚨 Emergency Contact</a>
          <a href="#security"   class="profile-nav-item">🔒 Password &amp; Security</a>
          <a href="#two-factor" class="profile-nav-item">🛡 Two-Factor Auth</a>
        </nav>
      </div>
    </div>
  </div>

  {{-- ── Forms ───────────────────────────────────── --}}
  <div class="profile-forms">

    {{-- Personal Info --}}
    <div class="card" id="personal">
      <div class="card-header">
        <div>
          <div class="card-title">Personal Information</div>
          <div class="card-sub">Your account details and contact information</div>
        </div>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('patient.profile.update') }}">
          @csrf
          @method('PUT')
          <div class="form-grid">
            <div class="form-field span-2">
              <label>Full name *</label>
              <input type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="Adaeze Okonkwo">
              @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
              <label>Email address *</label>
              <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
              @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
              <label>Phone number *</label>
              <x-phone-input name="phone" id="profilePhone"
                             :value="old('phone', $user->phone)"
                             :required="true" />
              @error('phone')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
              <label>Date of birth</label>
              <input type="date" name="dob" value="{{ old('dob', $patient->dob?->format('Y-m-d')) }}" max="{{ date('Y-m-d', strtotime('-1 day')) }}">
              @error('dob')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
              <label>Gender</label>
              <select name="gender">
                <option value="">Select…</option>
                <option value="male"   {{ old('gender', $patient->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other"  {{ old('gender', $patient->gender) === 'other'  ? 'selected' : '' }}>Other / prefer not to say</option>
              </select>
            </div>
            <div class="form-field">
              <label>State of residence</label>
              <select name="state">
                <option value="">Select state…</option>
                @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $st)
                  <option value="{{ $st }}" {{ old('state', $patient->state) === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-field">
              <label>City</label>
              <input type="text" name="city" value="{{ old('city', $patient->city) }}" placeholder="Lagos Island">
            </div>
            <div class="form-field span-2">
              <label>Address</label>
              <input type="text" name="address" value="{{ old('address', $patient->address) }}" placeholder="14 Broad Street, Lagos Island">
            </div>
          </div>
          <div class="form-actions form-actions-bordered">
            <button type="submit" class="btn btn-primary">Save personal info</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Medical Profile --}}
    <div class="card" id="medical">
      <div class="card-header">
        <div>
          <div class="card-title">Medical Profile</div>
          <div class="card-sub">Clinical information shown to providers during consultations</div>
        </div>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('patient.profile.update') }}">
          @csrf
          @method('PUT')
          {{-- Re-send the user fields as hidden to avoid overwriting --}}
          <input type="hidden" name="name"  value="{{ $user->name }}">
          <input type="hidden" name="email" value="{{ $user->email }}">
          <input type="hidden" name="phone" value="{{ $user->phone }}">
          <input type="hidden" name="dob"        value="{{ $patient->dob?->format('Y-m-d') }}">
          <input type="hidden" name="gender"     value="{{ $patient->gender }}">
          <input type="hidden" name="state"      value="{{ $patient->state }}">
          <input type="hidden" name="city"       value="{{ $patient->city }}">
          <input type="hidden" name="address"    value="{{ $patient->address }}">
          <input type="hidden" name="emergency_contact_name"  value="{{ $patient->emergency_contact_name }}">
          <input type="hidden" name="emergency_contact_phone" value="{{ $patient->emergency_contact_phone }}">

          <div class="form-grid">
            <div class="form-field">
              <label>Blood type</label>
              <select name="blood_type">
                <option value="">Unknown</option>
                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                  <option value="{{ $bt }}" {{ old('blood_type', $patient->blood_type) === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-field">
              <label>Genotype</label>
              <select name="genotype">
                <option value="">Unknown</option>
                @foreach(['AA','AS','SS','AC','SC'] as $g)
                  <option value="{{ $g }}" {{ old('genotype', $patient->genotype) === $g ? 'selected' : '' }}>{{ $g }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="alert navy mt-16">
            <span>ℹ</span>
            <span>Blood type and genotype are displayed to providers and pharmacists when they access your record with your consent.</span>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save medical profile</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Emergency Contact --}}
    <div class="card" id="emergency">
      <div class="card-header">
        <div>
          <div class="card-title">Emergency Contact</div>
          <div class="card-sub">Person to notify in a medical emergency</div>
        </div>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('patient.profile.update') }}">
          @csrf
          @method('PUT')
          <input type="hidden" name="name"       value="{{ $user->name }}">
          <input type="hidden" name="email"      value="{{ $user->email }}">
          <input type="hidden" name="phone"      value="{{ $user->phone }}">
          <input type="hidden" name="dob"        value="{{ $patient->dob?->format('Y-m-d') }}">
          <input type="hidden" name="gender"     value="{{ $patient->gender }}">
          <input type="hidden" name="blood_type" value="{{ $patient->blood_type }}">
          <input type="hidden" name="genotype"   value="{{ $patient->genotype }}">
          <input type="hidden" name="state"      value="{{ $patient->state }}">
          <input type="hidden" name="city"       value="{{ $patient->city }}">
          <input type="hidden" name="address"    value="{{ $patient->address }}">

          <div class="form-grid">
            <div class="form-field">
              <label>Contact name</label>
              <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" placeholder="Chidi Okonkwo">
            </div>
            <div class="form-field">
              <label>Contact phone</label>
              <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" placeholder="08012345678">
            </div>
          </div>
          <div class="form-actions mt-16">
            <button type="submit" class="btn btn-primary">Save emergency contact</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Password --}}
    <div class="card" id="security">
      <div class="card-header">
        <div>
          <div class="card-title">Password &amp; Security</div>
          <div class="card-sub">Change your account password</div>
        </div>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('patient.profile.password') }}">
          @csrf
          @method('PUT')
          <div class="form-grid">
            <div class="form-field span-2">
              <label>Current password *</label>
              <input type="password" name="current_password" required autocomplete="current-password">
              @error('current_password')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
              <label>New password *</label>
              <input type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Min 8 characters">
              @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-field">
              <label>Confirm new password *</label>
              <input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat new password">
            </div>
          </div>
          <div class="alert amber mt-16">
            <span>⚠</span>
            <span>Use at least 8 characters. Choose a mix of letters, numbers, and symbols for a strong password.</span>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Change password</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Two-Factor Authentication --}}
    <div class="card" id="two-factor">
      <div class="card-header">
        <div>
          <div class="card-title">Two-Factor Authentication</div>
          <div class="card-sub">Add an extra layer of security with SMS OTP</div>
        </div>
        <span class="pill {{ $user->two_factor_enabled ? 'green' : 'grey' }}">
          {{ $user->two_factor_enabled ? '✓ Enabled' : 'Disabled' }}
        </span>
      </div>
      <div class="card-body">
        @if($user->two_factor_enabled)
          <div class="alert green mb-16">
            <span>✓</span>
            <span>Two-factor authentication is active. You will receive a 6-digit OTP via SMS each time you log in.</span>
          </div>
          <form method="POST" action="{{ route('patient.profile.2fa') }}"
                data-confirm="Disable two-factor authentication?"
                data-confirm-ok="Disable 2FA">
            @csrf
            <input type="hidden" name="action" value="disable">
            <button type="submit" class="btn btn-danger">Disable 2FA</button>
          </form>
        @else
          <div class="alert amber mb-16">
            <span>⚠</span>
            <span>2FA is currently disabled. Enable it to receive an OTP on your phone number each time you sign in.</span>
          </div>
          @if($user->phone)
            <p class="td-sm td-muted mb-16">
              OTPs will be sent to <strong>{{ $user->phone }}</strong>.
              <a href="#personal" class="text-navy-link">Change phone number</a>
            </p>
            <form method="POST" action="{{ route('patient.profile.2fa') }}">
              @csrf
              <input type="hidden" name="action" value="enable">
              <button type="submit" class="btn btn-primary">Enable 2FA</button>
            </form>
          @else
            <div class="alert red">
              <span>✗</span>
              <span>You need to add a phone number before enabling 2FA. Update your personal info above.</span>
            </div>
          @endif
        @endif
      </div>
    </div>

  </div>
</div>
@endsection
