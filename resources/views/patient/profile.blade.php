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

<div style="display:grid; grid-template-columns: 260px 1fr; gap: 20px; align-items: start;">

  {{-- ── Sidebar nav ─────────────────────────────── --}}
  <div>
    <div class="card">
      <div style="padding: 20px;">
        {{-- Avatar --}}
        <div style="text-align: center; margin-bottom: 18px;">
          @php
            $parts    = explode(' ', $user->name);
            $initials = strtoupper(substr($parts[0],0,1) . substr($parts[1] ?? '',0,1));
          @endphp
          @if($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Profile photo"
                 style="width:72px;height:72px;border-radius:50%;object-fit:cover;margin:0 auto 10px;display:block;border:3px solid var(--border);">
          @else
            <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#2C5F2D,#1E3A5F);color:#fff;font-size:26px;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;">
              {{ $initials }}
            </div>
          @endif
          <div style="font-size:15px;font-weight:700;color:var(--navy);">{{ $user->name }}</div>
          <div style="font-size:12px;color:var(--text-2);margin-top:2px;">{{ $user->email }}</div>
          <div style="margin-top:8px;">
            @if($patient->nin_verified)
              <span class="pill green">✓ NIN Verified</span>
            @else
              <span class="pill grey">NIN unverified</span>
            @endif
          </div>
          <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" style="margin-top:12px;">
            @csrf
            <label style="cursor:pointer;display:inline-block;font-size:12px;color:var(--navy);font-weight:600;background:var(--navy-lt);padding:6px 14px;border-radius:20px;border:1.5px solid var(--navy);">
              📷 Change photo
              <input type="file" name="avatar" accept="image/*" style="display:none;" onchange="this.form.submit()">
            </label>
          </form>
          @error('avatar')<div class="form-error" style="font-size:11px;margin-top:6px;">{{ $message }}</div>@enderror
        </div>

        {{-- Profile completeness --}}
        @php
          $fields = [
            'Date of birth' => $patient->dob,
            'Gender'        => $patient->gender,
            'Blood type'    => $patient->blood_type,
            'State'         => $patient->state,
            'Emergency contact' => $patient->emergency_contact_name,
          ];
          $filled = collect($fields)->filter()->count();
          $pct    = (int)(($filled / count($fields)) * 100);
        @endphp
        <div style="background: #F8FAFC; border-radius: 10px; padding: 14px; margin-bottom: 14px;">
          <div style="display:flex;justify-content:space-between;font-size:12px;font-weight:600;margin-bottom:7px;">
            <span style="color:var(--text-2);">Profile completeness</span>
            <span style="color:var(--navy);">{{ $pct }}%</span>
          </div>
          <div style="height:6px;background:var(--border);border-radius:6px;overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 80 ? 'var(--green)' : ($pct >= 50 ? 'var(--amber)' : 'var(--red)') }};border-radius:6px;transition:width 0.5s;"></div>
          </div>
          <div style="margin-top:10px;display:flex;flex-direction:column;gap:4px;">
            @foreach($fields as $label => $val)
              <div style="font-size:11px;color:{{ $val ? 'var(--green)' : 'var(--text-3)' }};">
                {{ $val ? '✓' : '○' }} {{ $label }}
              </div>
            @endforeach
          </div>
        </div>

        <nav style="display:flex;flex-direction:column;gap:2px;">
          <a href="#personal" class="profile-nav-item active">👤 Personal Info</a>
          <a href="#medical"  class="profile-nav-item">🩺 Medical Profile</a>
          <a href="#emergency" class="profile-nav-item">🚨 Emergency Contact</a>
          <a href="#security" class="profile-nav-item">🔒 Password & Security</a>
          <a href="#two-factor" class="profile-nav-item">🛡 Two-Factor Auth</a>
        </nav>
      </div>
    </div>
  </div>

  {{-- ── Forms ───────────────────────────────────── --}}
  <div style="display:flex;flex-direction:column;gap:20px;">

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
          <div class="form-actions" style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border);">
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
          <div class="alert navy" style="margin-top: 16px;">
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
          <div class="form-actions" style="margin-top: 16px;">
            <button type="submit" class="btn btn-primary">Save emergency contact</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Password --}}
    <div class="card" id="security">
      <div class="card-header">
        <div>
          <div class="card-title">Password & Security</div>
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
          <div class="alert amber" style="margin-top: 16px;">
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
          <div class="alert green" style="margin-bottom:16px;">
            <span>✓</span>
            <span>Two-factor authentication is active. You will receive a 6-digit OTP via SMS each time you log in.</span>
          </div>
          <form method="POST" action="{{ route('patient.profile.2fa') }}">
            @csrf
            <input type="hidden" name="action" value="disable">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Disable two-factor authentication?')">
              Disable 2FA
            </button>
          </form>
        @else
          <div class="alert amber" style="margin-bottom:16px;">
            <span>⚠</span>
            <span>2FA is currently disabled. Enable it to receive an OTP on your phone number each time you sign in.</span>
          </div>
          @if($user->phone)
            <p style="font-size:13px;color:var(--text-2);margin-bottom:16px;">
              OTPs will be sent to <strong>{{ $user->phone }}</strong>.
              <a href="#personal" style="color:var(--navy);">Change phone number</a>
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

<style>
.profile-nav-item {
  display: flex; align-items: center; gap: 8px;
  padding: 9px 12px; border-radius: 8px;
  font-size: 13px; font-weight: 500; color: var(--text-2);
  transition: all 0.12s;
}
.profile-nav-item:hover { background: var(--navy-lt); color: var(--navy); }
.profile-nav-item.active { background: var(--navy-lt); color: var(--navy); font-weight: 700; }

@media (max-width: 768px) {
  div[style*="grid-template-columns: 260px"] {
    display: block !important;
  }
  div[style*="grid-template-columns: 260px"] > div:first-child {
    margin-bottom: 20px;
  }
}
</style>
@endsection
