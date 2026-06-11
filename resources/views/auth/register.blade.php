@extends('layouts.auth')
@section('title', 'Create Account')
@section('content')

@if($errors->any())
  <div class="alert-error" id="serverErrors">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
  </div>
@endif

<form method="POST" action="{{ route('register') }}" id="regForm" novalidate>
  @csrf
  <input type="hidden" name="reg_type" id="regType" value="{{ old('reg_type', '') }}">

  {{-- ══════════════════════════════════════════════════════
       TYPE SELECTOR
  ══════════════════════════════════════════════════════ --}}
  <div id="typeScreen">
    <div class="auth-box-header">
      <h2>Create your account</h2>
      <p>Choose how you'll use MedRecords to get started</p>
    </div>

    <div class="type-list">
      <button type="button" class="type-card" onclick="selectType('patient')" data-type="patient">
        <div class="type-icon-wrap c-patient">👤</div>
        <div class="type-card-body">
          <div class="type-title">Patient</div>
          <div class="type-desc">Access and manage your personal health record</div>
        </div>
        <span class="type-arrow">›</span>
      </button>
      <button type="button" class="type-card" onclick="selectType('provider')" data-type="provider">
        <div class="type-icon-wrap c-provider">🩺</div>
        <div class="type-card-body">
          <div class="type-title">Doctor / Provider</div>
          <div class="type-desc">Register as a licensed clinical practitioner</div>
        </div>
        <span class="type-arrow">›</span>
      </button>
      <button type="button" class="type-card" onclick="selectType('hospital')" data-type="hospital">
        <div class="type-icon-wrap c-hospital">🏥</div>
        <div class="type-card-body">
          <div class="type-title">Hospital / Clinic</div>
          <div class="type-desc">Register your healthcare facility on the platform</div>
        </div>
        <span class="type-arrow">›</span>
      </button>
      <button type="button" class="type-card" onclick="selectType('pharmacy')" data-type="pharmacy">
        <div class="type-icon-wrap c-pharmacy">💊</div>
        <div class="type-card-body">
          <div class="type-title">Pharmacy</div>
          <div class="type-desc">Register your pharmacy organisation</div>
        </div>
        <span class="type-arrow">›</span>
      </button>
    </div>

    <div class="form-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
  </div>

  {{-- ══════════════════════════════════════════════════════
       PATIENT WIZARD
  ══════════════════════════════════════════════════════ --}}
  <div id="patientWizard" class="wizard" style="display:none;">

    <button type="button" class="wizard-back-btn" onclick="backToTypes()">
      <span class="wbk-arrow">←</span> Change account type
    </button>

    <div class="wizard-badge c-patient"><span>👤</span> Patient Registration</div>

    {{-- Progress --}}
    <div class="progress-bar" id="p-prog">
      <div class="progress-step active" id="p-s1"><div class="step-dot">1</div><div class="step-label">Account</div></div>
      <div class="progress-line" id="p-l1"></div>
      <div class="progress-step" id="p-s2"><div class="step-dot">2</div><div class="step-label">Health info</div></div>
      <div class="progress-line" id="p-l2"></div>
      <div class="progress-step" id="p-s3"><div class="step-dot">3</div><div class="step-label">Review</div></div>
    </div>

    {{-- Step 1 --}}
    <div id="p-step1" class="wstep">
      <div class="step-header">
        <h2>Your account</h2>
        <p class="subtitle">Free, secure, and private</p>
      </div>

      <div class="form-field">
        <label>Full name <span class="req">*</span></label>
        <input type="text" name="name" id="p-name" value="{{ old('name') }}" placeholder="Adaeze Okonkwo" autocomplete="name" required>
        <div class="field-hint">As it appears on government-issued ID</div>
      </div>
      <div class="form-field">
        <label>Email address <span class="req">*</span></label>
        <input type="email" name="email" id="p-email" value="{{ old('email') }}" placeholder="adaeze@gmail.com" autocomplete="email" required>
      </div>
      <div class="form-field">
        <label>Phone number <span class="req">*</span></label>
        <x-phone-input name="phone" id="p-phone" :value="old('phone', '')" :required="true" />
        <div class="field-hint">Include country code, e.g. +2348012345678</div>
      </div>
      <div class="form-field">
        <label>Password <span class="req">*</span></label>
        <div class="input-with-action">
          <input type="password" name="password" id="p-pw" placeholder="Minimum 8 characters" autocomplete="new-password" minlength="8" required>
          <button type="button" class="input-action" onclick="togglePwd('p-pw','p-eye1')"><span id="p-eye1">👁</span></button>
        </div>
        <div class="strength-bar"><div class="strength-fill" id="p-sfill"></div></div>
        <div class="field-hint" id="p-slabel">Enter a password</div>
      </div>
      <div class="form-field">
        <label>Confirm password <span class="req">*</span></label>
        <div class="input-with-action">
          <input type="password" name="password_confirmation" id="p-cf" placeholder="Repeat your password" autocomplete="new-password" minlength="8" required>
          <button type="button" class="input-action" onclick="togglePwd('p-cf','p-eye2')"><span id="p-eye2">👁</span></button>
        </div>
        <div class="field-hint match-hint" id="p-mhint"></div>
      </div>
      <button type="button" class="btn-submit" onclick="pGoStep2()">Continue <span class="btn-arrow">→</span></button>
      <div class="form-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
    </div>

    {{-- Step 2 --}}
    <div id="p-step2" class="wstep" style="display:none;">
      <div class="step-header">
        <h2>Health profile</h2>
        <p class="subtitle">Helps providers give you better care — all fields optional</p>
      </div>

      <div class="form-row">
        <div class="form-field">
          <label>Date of birth</label>
          <input type="date" name="dob" id="p-dob" value="{{ old('dob') }}" max="{{ date('Y-m-d', strtotime('-1 day')) }}">
        </div>
        <div class="form-field">
          <label>Gender</label>
          <select name="gender" id="p-gender">
            <option value="">Select…</option>
            <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
            <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-field">
          <label>Blood type</label>
          <select name="blood_type" id="p-blood">
            <option value="">Unknown</option>
            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
              <option value="{{ $bt }}" {{ old('blood_type') === $bt ? 'selected' : '' }}>{{ $bt }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-field">
          <label>Genotype</label>
          <select name="genotype" id="p-geno">
            <option value="">Unknown</option>
            @foreach(['AA','AS','SS','AC','SC'] as $g)
              <option value="{{ $g }}" {{ old('genotype') === $g ? 'selected' : '' }}>{{ $g }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-field">
        <label>State of residence</label>
        <select name="state" id="p-state">
          <option value="">Select state…</option>
          @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $st)
            <option value="{{ $st }}" {{ old('state') === $st ? 'selected' : '' }}>{{ $st }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-field">
        <label>NIN (National Identity Number)</label>
        <input type="text" name="nin" id="p-nin" value="{{ old('nin') }}" placeholder="12345678901" maxlength="11" pattern="\d{11}" inputmode="numeric">
        <div class="field-hint">11-digit NIN — optional, used for identity verification</div>
      </div>

      <div class="btn-row">
        <button type="button" class="btn-back" onclick="pGoStep1()">← Back</button>
        <button type="button" class="btn-submit btn-flex" onclick="pGoStep3()">Review <span class="btn-arrow">→</span></button>
      </div>
    </div>

    {{-- Step 3 --}}
    <div id="p-step3" class="wstep" style="display:none;">
      <div class="step-header">
        <h2>Review & confirm</h2>
        <p class="subtitle">Check your details before creating your account</p>
      </div>

      <div class="review-card">
        <div class="review-section">Account</div>
        <div class="review-row"><span>Name</span><strong id="p-rv-name">—</strong></div>
        <div class="review-row"><span>Email</span><strong id="p-rv-email">—</strong></div>
        <div class="review-row"><span>Phone</span><strong id="p-rv-phone">—</strong></div>
        <div class="review-row"><span>Password</span><strong>••••••••</strong></div>
      </div>
      <div class="review-card">
        <div class="review-section">Health profile</div>
        <div class="review-row"><span>Date of birth</span><strong id="p-rv-dob">Not provided</strong></div>
        <div class="review-row"><span>Gender</span><strong id="p-rv-gender">Not provided</strong></div>
        <div class="review-row"><span>Blood type</span><strong id="p-rv-blood">Unknown</strong></div>
        <div class="review-row"><span>Genotype</span><strong id="p-rv-geno">Unknown</strong></div>
        <div class="review-row"><span>State</span><strong id="p-rv-state">Not provided</strong></div>
        <div class="review-row"><span>NIN</span><strong id="p-rv-nin">Not provided</strong></div>
      </div>

      <label class="checkbox-label" style="margin-bottom:18px;">
        <input type="checkbox" name="terms" id="p-terms" required style="width:auto;accent-color:var(--navy);cursor:pointer;margin-right:2px;flex-shrink:0;margin-top:2px;">
        I agree to the <a href="#" style="color:var(--navy);">Terms of Service</a> and <a href="#" style="color:var(--navy);">Privacy Policy</a>. My health data is stored securely and shared only with providers I explicitly consent to.
      </label>

      <div class="btn-row">
        <button type="button" class="btn-back" onclick="pGoStep2()">← Back</button>
        <button type="submit" class="btn-submit btn-flex btn-green" id="p-submit">Create account</button>
      </div>
      <div class="form-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
    </div>
  </div>{{-- /patientWizard --}}

  {{-- ══════════════════════════════════════════════════════
       PROVIDER WIZARD
  ══════════════════════════════════════════════════════ --}}
  <div id="providerWizard" class="wizard" style="display:none;">

    <button type="button" class="wizard-back-btn" onclick="backToTypes()">
      <span class="wbk-arrow">←</span> Change account type
    </button>

    <div class="wizard-badge c-provider"><span>🩺</span> Provider Registration</div>

    <div class="progress-bar">
      <div class="progress-step active" id="pr-s1"><div class="step-dot">1</div><div class="step-label">Account</div></div>
      <div class="progress-line" id="pr-l1"></div>
      <div class="progress-step" id="pr-s2"><div class="step-dot">2</div><div class="step-label">Professional</div></div>
      <div class="progress-line" id="pr-l2"></div>
      <div class="progress-step" id="pr-s3"><div class="step-dot">3</div><div class="step-label">Review</div></div>
    </div>

    {{-- Step 1 --}}
    <div id="pr-step1" class="wstep">
      <div class="step-header">
        <h2>Create your account</h2>
        <p class="subtitle">Your professional provider profile</p>
      </div>

      <div class="form-field">
        <label>Full name <span class="req">*</span></label>
        <input type="text" name="name" id="pr-name" value="{{ old('name') }}" placeholder="Dr. Emeka Obi" autocomplete="name" required>
        <div class="field-hint">As it appears on your MDCN registration</div>
      </div>
      <div class="form-field">
        <label>Email address <span class="req">*</span></label>
        <input type="email" name="email" id="pr-email" value="{{ old('email') }}" placeholder="emeka.obi@gmail.com" autocomplete="email" required>
      </div>
      <div class="form-field">
        <label>Phone number <span class="req">*</span></label>
        <x-phone-input name="phone" id="pr-phone" :value="old('phone', '')" :required="true" />
      </div>
      <div class="form-field">
        <label>Password <span class="req">*</span></label>
        <div class="input-with-action">
          <input type="password" name="password" id="pr-pw" placeholder="Minimum 8 characters" autocomplete="new-password" minlength="8" required>
          <button type="button" class="input-action" onclick="togglePwd('pr-pw','pr-eye1')"><span id="pr-eye1">👁</span></button>
        </div>
        <div class="strength-bar"><div class="strength-fill" id="pr-sfill"></div></div>
        <div class="field-hint" id="pr-slabel">Enter a password</div>
      </div>
      <div class="form-field">
        <label>Confirm password <span class="req">*</span></label>
        <div class="input-with-action">
          <input type="password" name="password_confirmation" id="pr-cf" placeholder="Repeat your password" autocomplete="new-password" minlength="8" required>
          <button type="button" class="input-action" onclick="togglePwd('pr-cf','pr-eye2')"><span id="pr-eye2">👁</span></button>
        </div>
        <div class="field-hint match-hint" id="pr-mhint"></div>
      </div>
      <button type="button" class="btn-submit" onclick="prGoStep2()">Continue <span class="btn-arrow">→</span></button>
      <div class="form-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
    </div>

    {{-- Step 2 --}}
    <div id="pr-step2" class="wstep" style="display:none;">
      <div class="step-header">
        <h2>Professional details</h2>
        <p class="subtitle">Your clinical credentials</p>
      </div>

      <div class="form-field">
        <label>Specialty <span class="req">*</span></label>
        <select name="specialty" id="pr-specialty" required>
          <option value="">Select specialty…</option>
          @foreach(['General Practice','Internal Medicine','Paediatrics','Obstetrics & Gynaecology','Surgery','Cardiology','Neurology','Psychiatry','Dermatology','Radiology','Anaesthesia','Ophthalmology','ENT','Orthopaedics','Urology','Nephrology','Endocrinology','Gastroenterology','Oncology','Emergency Medicine','Family Medicine','Community Medicine','Pathology','Pharmacology','Physiotherapy','Dentistry','Nursing','Pharmacy','Medical Laboratory Science','Radiography','Other'] as $sp)
            <option value="{{ $sp }}" {{ old('specialty') === $sp ? 'selected' : '' }}>{{ $sp }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-field">
        <label>MDCN Folio Number</label>
        <input type="text" name="mdcn_folio" id="pr-folio" value="{{ old('mdcn_folio') }}" placeholder="e.g. MDCN/2018/12345">
        <div class="field-hint">Medical and Dental Council of Nigeria registration number</div>
      </div>
      <div class="form-field">
        <label>Professional Licence No.</label>
        <input type="text" name="license_no" id="pr-license" value="{{ old('license_no') }}" placeholder="e.g. FMC/2019/00456">
        <div class="field-hint">Your current practising licence — you can add this later</div>
      </div>

      <div class="info-box">
        <div class="info-box-icon">ℹ</div>
        <div>You can join a facility after registration. A facility administrator will add you to their team, or you can operate independently.</div>
      </div>

      <div class="btn-row">
        <button type="button" class="btn-back" onclick="prGoStep1()">← Back</button>
        <button type="button" class="btn-submit btn-flex" onclick="prGoStep3()">Review <span class="btn-arrow">→</span></button>
      </div>
    </div>

    {{-- Step 3 --}}
    <div id="pr-step3" class="wstep" style="display:none;">
      <div class="step-header">
        <h2>Review & confirm</h2>
        <p class="subtitle">Check your details before creating your account</p>
      </div>

      <div class="review-card">
        <div class="review-section">Account</div>
        <div class="review-row"><span>Name</span><strong id="pr-rv-name">—</strong></div>
        <div class="review-row"><span>Email</span><strong id="pr-rv-email">—</strong></div>
        <div class="review-row"><span>Phone</span><strong id="pr-rv-phone">—</strong></div>
      </div>
      <div class="review-card">
        <div class="review-section">Professional</div>
        <div class="review-row"><span>Specialty</span><strong id="pr-rv-spec">—</strong></div>
        <div class="review-row"><span>MDCN Folio</span><strong id="pr-rv-folio">Not provided</strong></div>
        <div class="review-row"><span>Licence No.</span><strong id="pr-rv-lic">Not provided</strong></div>
        <div class="review-row"><span>Facility</span><strong>To be assigned</strong></div>
      </div>

      <label class="checkbox-label" style="margin-bottom:18px;">
        <input type="checkbox" name="terms" id="pr-terms" required style="width:auto;accent-color:var(--navy);cursor:pointer;margin-right:2px;flex-shrink:0;margin-top:2px;">
        I agree to the <a href="#" style="color:var(--navy);">Terms of Service</a> and <a href="#" style="color:var(--navy);">Privacy Policy</a>. I confirm that my credentials are accurate.
      </label>

      <div class="btn-row">
        <button type="button" class="btn-back" onclick="prGoStep2()">← Back</button>
        <button type="submit" class="btn-submit btn-flex btn-green" id="pr-submit">Create account</button>
      </div>
      <div class="form-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
    </div>
  </div>{{-- /providerWizard --}}

  {{-- ══════════════════════════════════════════════════════
       ORGANISATION WIZARD  (hospital / clinic / pharmacy)
  ══════════════════════════════════════════════════════ --}}
  <div id="orgWizard" class="wizard" style="display:none;">

    <button type="button" class="wizard-back-btn" onclick="backToTypes()">
      <span class="wbk-arrow">←</span> Change account type
    </button>

    <div class="wizard-badge c-hospital" id="orgBadge"><span>🏥</span> <span id="orgBadgeLabel">Organisation Registration</span></div>

    <div class="progress-bar">
      <div class="progress-step active" id="o-s1"><div class="step-dot">1</div><div class="step-label">Admin</div></div>
      <div class="progress-line" id="o-l1"></div>
      <div class="progress-step" id="o-s2"><div class="step-dot">2</div><div class="step-label">Organisation</div></div>
      <div class="progress-line" id="o-l2"></div>
      <div class="progress-step" id="o-s3"><div class="step-dot">3</div><div class="step-label">Review</div></div>
    </div>

    {{-- Step 1 --}}
    <div id="o-step1" class="wstep">
      <div class="step-header">
        <h2 id="o-title1">Register your organisation</h2>
        <p class="subtitle">Create the administrator account</p>
      </div>

      <div class="form-field">
        <label>Your full name <span class="req">*</span></label>
        <input type="text" name="name" id="o-name" value="{{ old('name') }}" placeholder="Chidi Nwosu" autocomplete="name" required>
        <div class="field-hint">Name of the account administrator</div>
      </div>
      <div class="form-field">
        <label>Your email address <span class="req">*</span></label>
        <input type="email" name="email" id="o-email" value="{{ old('email') }}" placeholder="admin@yourhospital.ng" autocomplete="email" required>
      </div>
      <div class="form-field">
        <label>Your phone number <span class="req">*</span></label>
        <x-phone-input name="phone" id="o-phone" :value="old('phone', '')" :required="true" />
      </div>
      <div class="form-field">
        <label>Password <span class="req">*</span></label>
        <div class="input-with-action">
          <input type="password" name="password" id="o-pw" placeholder="Minimum 8 characters" autocomplete="new-password" minlength="8" required>
          <button type="button" class="input-action" onclick="togglePwd('o-pw','o-eye1')"><span id="o-eye1">👁</span></button>
        </div>
        <div class="strength-bar"><div class="strength-fill" id="o-sfill"></div></div>
        <div class="field-hint" id="o-slabel">Enter a password</div>
      </div>
      <div class="form-field">
        <label>Confirm password <span class="req">*</span></label>
        <div class="input-with-action">
          <input type="password" name="password_confirmation" id="o-cf" placeholder="Repeat your password" autocomplete="new-password" minlength="8" required>
          <button type="button" class="input-action" onclick="togglePwd('o-cf','o-eye2')"><span id="o-eye2">👁</span></button>
        </div>
        <div class="field-hint match-hint" id="o-mhint"></div>
      </div>
      <button type="button" class="btn-submit" onclick="oGoStep2()">Continue <span class="btn-arrow">→</span></button>
      <div class="form-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
    </div>

    {{-- Step 2 --}}
    <div id="o-step2" class="wstep" style="display:none;">
      <div class="step-header">
        <h2 id="o-title2">Organisation details</h2>
        <p class="subtitle">Your facility's information</p>
      </div>

      <div class="form-field">
        <label id="o-name-label">Organisation name <span class="req">*</span></label>
        <input type="text" name="org_name" id="o-org-name" value="{{ old('org_name') }}" placeholder="Lagos General Hospital" required>
      </div>

      <div class="form-field" id="o-type-field">
        <label>Facility type <span class="req">*</span></label>
        <select name="org_type" id="o-org-type">
          <option value="hospital" {{ old('org_type','hospital') === 'hospital' ? 'selected' : '' }}>Hospital</option>
          <option value="clinic"   {{ old('org_type') === 'clinic'   ? 'selected' : '' }}>Clinic</option>
          <option value="lab"      {{ old('org_type') === 'lab'      ? 'selected' : '' }}>Diagnostic Lab</option>
          <option value="dental"   {{ old('org_type') === 'dental'   ? 'selected' : '' }}>Dental Practice</option>
          <option value="optical"  {{ old('org_type') === 'optical'  ? 'selected' : '' }}>Optical / Eye Care</option>
        </select>
      </div>

      <div class="form-field">
        <label id="o-lic-label">Licence / Registration No.</label>
        <input type="text" name="org_license" id="o-license" value="{{ old('org_license') }}" placeholder="e.g. MOH/2020/LAG-001">
      </div>
      <div class="form-field">
        <label>Street address</label>
        <input type="text" name="org_address" id="o-address" value="{{ old('org_address') }}" placeholder="14 Broad Street">
      </div>
      <div class="form-row">
        <div class="form-field">
          <label>City</label>
          <input type="text" name="org_city" id="o-city" value="{{ old('org_city') }}" placeholder="Lagos">
        </div>
        <div class="form-field">
          <label>State</label>
          <select name="org_state" id="o-state">
            <option value="">Select…</option>
            @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $st)
              <option value="{{ $st }}" {{ old('org_state') === $st ? 'selected' : '' }}>{{ $st }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-field">
          <label>Organisation phone</label>
          <input type="text" name="org_phone" id="o-org-phone" value="{{ old('org_phone') }}" placeholder="+2341234567890">
        </div>
        <div class="form-field">
          <label>Organisation email</label>
          <input type="email" name="org_email" id="o-org-email" value="{{ old('org_email') }}" placeholder="info@yourhospital.ng">
        </div>
      </div>

      <div class="btn-row">
        <button type="button" class="btn-back" onclick="oGoStep1()">← Back</button>
        <button type="button" class="btn-submit btn-flex" onclick="oGoStep3()">Review <span class="btn-arrow">→</span></button>
      </div>
    </div>

    {{-- Step 3 --}}
    <div id="o-step3" class="wstep" style="display:none;">
      <div class="step-header">
        <h2>Review & confirm</h2>
        <p class="subtitle">Check everything before registering</p>
      </div>

      <div class="review-card">
        <div class="review-section">Administrator account</div>
        <div class="review-row"><span>Name</span><strong id="o-rv-name">—</strong></div>
        <div class="review-row"><span>Email</span><strong id="o-rv-email">—</strong></div>
        <div class="review-row"><span>Phone</span><strong id="o-rv-phone">—</strong></div>
      </div>
      <div class="review-card">
        <div class="review-section">Organisation</div>
        <div class="review-row"><span>Name</span><strong id="o-rv-org">—</strong></div>
        <div class="review-row" id="o-rv-type-row"><span>Type</span><strong id="o-rv-type">—</strong></div>
        <div class="review-row"><span>Licence No.</span><strong id="o-rv-lic">Not provided</strong></div>
        <div class="review-row"><span>Address</span><strong id="o-rv-addr">Not provided</strong></div>
        <div class="review-row"><span>State</span><strong id="o-rv-state">Not provided</strong></div>
      </div>

      <label class="checkbox-label" style="margin-bottom:18px;">
        <input type="checkbox" name="terms" id="o-terms" required style="width:auto;accent-color:var(--navy);cursor:pointer;margin-right:2px;flex-shrink:0;margin-top:2px;">
        I agree to the <a href="#" style="color:var(--navy);">Terms of Service</a> and <a href="#" style="color:var(--navy);">Privacy Policy</a>. I confirm that the organisation details are accurate.
      </label>

      <div class="btn-row">
        <button type="button" class="btn-back" onclick="oGoStep2()">← Back</button>
        <button type="submit" class="btn-submit btn-flex btn-green" id="o-submit">Register organisation</button>
      </div>
      <div class="form-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></div>
    </div>
  </div>{{-- /orgWizard --}}

</form>

{{-- ══ STYLES ══════════════════════════════════════════════ --}}
<style nonce="{{ $cspNonce ?? '' }}">
/* ── Override box width for registration ─────────────── */
.auth-box { max-width: 500px; }

/* ── Type selector list ──────────────────────────────── */
.type-list {
  border: 1.5px solid #E2E8F0;
  border-radius: 14px;
  overflow: hidden;
  margin-bottom: 24px;
  background: #fff;
}
.type-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 15px 18px;
  background: transparent;
  border: none;
  border-bottom: 1px solid #F1F5F9;
  cursor: pointer;
  text-align: left;
  width: 100%;
  transition: background 0.12s;
  font-family: inherit;
}
.type-card:last-child { border-bottom: none; }
.type-card:hover { background: #F8FAFC; }
.type-icon-wrap {
  width: 44px; height: 44px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px;
  flex-shrink: 0;
}
.c-patient  { background: #EEF3FA; }
.c-provider { background: #EDFAF0; }
.c-hospital { background: #FFF7ED; }
.c-pharmacy { background: #F5F3FF; }
.type-card-body { flex: 1; min-width: 0; }
.type-title { font-size: 14px; font-weight: 700; color: #0F172A; margin-bottom: 2px; }
.type-desc  { font-size: 12px; color: #64748B; line-height: 1.4; }
.type-arrow {
  color: #CBD5E1;
  font-size: 22px;
  font-weight: 300;
  line-height: 1;
  flex-shrink: 0;
  transition: transform 0.15s, color 0.15s;
}
.type-card:hover .type-arrow { transform: translateX(3px); color: #94A3B8; }

/* ── Wizard back button ───────────────────────────────── */
.wizard-back-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 13px;
  color: #94A3B8;
  padding: 0;
  margin-bottom: 16px;
  font-family: inherit;
  transition: color 0.15s;
}
.wizard-back-btn:hover { color: #1E3A5F; }
.wbk-arrow { font-size: 14px; }

/* ── Wizard type badge ───────────────────────────────── */
.wizard-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 20px;
}
.wizard-badge.c-patient  { background: #EEF3FA; color: #1E3A5F; }
.wizard-badge.c-provider { background: #EDFAF0; color: #166534; }
.wizard-badge.c-hospital { background: #FFF7ED; color: #92400E; }
.wizard-badge.c-pharmacy { background: #F5F3FF; color: #5B21B6; }

/* ── Step headings ───────────────────────────────────── */
.step-header { margin-bottom: 22px; }
.step-header h2 { font-size: 22px; font-weight: 800; color: #0F172A; letter-spacing: -0.02em; margin-bottom: 4px; }

/* ── Progress bar ─────────────────────────────────────── */
.progress-bar {
  display: flex; align-items: flex-start;
  margin-bottom: 28px;
}
.progress-step { display: flex; flex-direction: column; align-items: center; gap: 5px; flex-shrink: 0; }
.step-dot {
  width: 32px; height: 32px; border-radius: 50%;
  background: #E2E8F0; color: #94A3B8;
  font-size: 12px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.25s;
  border: 2px solid transparent;
}
.step-label { font-size: 10px; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap; transition: color 0.2s; }
.progress-step.active .step-dot { background: #1E3A5F; color: #fff; border-color: #1E3A5F; }
.progress-step.active .step-label { color: #1E3A5F; }
.progress-step.done .step-dot { background: #2C5F2D; color: #fff; border-color: #2C5F2D; }
.progress-step.done .step-label { color: #2C5F2D; }
.progress-line { flex: 1; height: 2px; background: #E2E8F0; min-width: 20px; margin: 0 4px; margin-top: 15px; transition: background 0.3s; }
.progress-line.done { background: #2C5F2D; }

/* ── Wizard step animation ────────────────────────────── */
@keyframes stepFade {
  from { opacity: 0; transform: translateY(5px); }
  to   { opacity: 1; transform: translateY(0); }
}
.wstep-enter { animation: stepFade 0.2s ease both; }

/* ── Button row ───────────────────────────────────────── */
.btn-row {
  display: flex;
  gap: 10px;
  margin-top: 8px;
  align-items: stretch;
}
/* Reset global width:100% and margin-top when btn-submit is inside a flex row */
.btn-row .btn-submit {
  width: auto;
  flex: 1;
  margin-top: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}
.btn-row .btn-back {
  flex-shrink: 0;
  white-space: nowrap;
}
.btn-arrow { font-size: 15px; }
.btn-green { background: #2C5F2D; }
.btn-green:hover { background: #1E4020; }

/* ── Info box ─────────────────────────────────────────── */
.info-box {
  display: flex;
  gap: 10px;
  align-items: flex-start;
  background: #EEF3FA;
  border-left: 3px solid #1E3A5F;
  border-radius: 0 8px 8px 0;
  padding: 10px 14px;
  font-size: 12px;
  color: #1E3A5F;
  line-height: 1.55;
  margin-bottom: 18px;
  margin-top: 4px;
}
.info-box-icon {
  width: 18px; height: 18px;
  border-radius: 50%;
  background: #1E3A5F;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  margin-top: 1px;
  font-style: normal;
}

/* ── Review cards ─────────────────────────────────────── */
.review-card {
  border: 1.5px solid #E2E8F0;
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 12px;
  background: #fff;
}
.review-section {
  background: #F8FAFC;
  padding: 7px 16px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: #64748B;
  border-bottom: 1px solid #F1F5F9;
}
.review-row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  padding: 8px 16px;
  font-size: 13px;
  border-top: 1px solid #F8FAFC;
}
.review-row:first-of-type { border-top: none; }
.review-row span { color: #64748B; font-size: 12px; }
.review-row strong { text-align: right; max-width: 58%; word-break: break-all; font-weight: 600; color: #0F172A; }

/* ── Password / confirm fields ────────────────────────── */
/* Override the auth layout's padding shorthand for password fields with eye icon */
.form-field .input-with-action input { padding-right: 40px; }
.input-with-action { position: relative; display: flex; align-items: center; }
.input-action {
  position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  font-size: 15px; line-height: 1; color: #94A3B8; padding: 2px;
  transition: color 0.15s;
}
.input-action:hover { color: #475569; }

/* ── Misc overrides ───────────────────────────────────── */
.checkbox-label {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 12px;
  color: #64748B;
  line-height: 1.55;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 400;
  cursor: default;
}
.req { color: #B23A48; }
.field-hint { font-size: 11px; color: #94A3B8; margin-top: 4px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
/* Ensure form-row children with form-field > input get width:100% from auth layout,
   which they do since .form-field > input matches them */
.strength-bar { height: 3px; background: #E2E8F0; border-radius: 3px; margin-top: 6px; overflow: hidden; }
.strength-fill { height: 100%; width: 0; border-radius: 3px; transition: width .3s, background .3s; background: #B23A48; }
.match-hint { min-height: 14px; }

@media (max-width: 520px) {
  .form-row { grid-template-columns: 1fr; }
  .auth-box { max-width: 100%; }
  .btn-row { flex-wrap: wrap; }
  .btn-row .btn-back { width: 100%; text-align: center; }
  .btn-row .btn-submit { width: 100%; }
}
</style>

{{-- ══ SCRIPTS ══════════════════════════════════════════════ --}}
<script nonce="{{ $cspNonce ?? '' }}">
/* ── Utilities ──────────────────────────────────────────── */
function togglePwd(inputId, iconId) {
  var el = document.getElementById(inputId);
  var ic = document.getElementById(iconId);
  el.type = el.type === 'password' ? 'text' : 'password';
  ic.textContent = el.type === 'password' ? '👁' : '🙈';
}

function fmtDate(str) {
  var d = new Date(str + 'T00:00:00');
  return d.toLocaleDateString('en-NG', { day:'numeric', month:'long', year:'numeric' });
}

function val(id) { return (document.getElementById(id)||{}).value || ''; }
function selTxt(id) {
  var el = document.getElementById(id);
  return el && el.options[el.selectedIndex] ? el.options[el.selectedIndex].text : '';
}

/* ── Left panel content per type ────────────────────────── */
var LEFT_PANELS = {
  default: {
    headline: 'Create your<br><span>MedRecords</span> account',
    subhead: "Nigeria's most comprehensive electronic health platform. Choose your role to get started."
  },
  patient: {
    headline: 'Your health record,<br><span>always with you</span>',
    subhead: 'Secure, patient-owned health records. Share access with any provider — only when you choose.'
  },
  provider: {
    headline: "Join Nigeria's<br><span>clinical network</span>",
    subhead: 'Access patient records with consent, log clinical encounters, and coordinate care across facilities.'
  },
  hospital: {
    headline: 'Bring your facility<br><span>onto MedRecords</span>',
    subhead: 'Manage your provider team, review patient data with consent, and streamline clinical workflows.'
  },
  pharmacy: {
    headline: 'Modernise your<br><span>dispensing workflow</span>',
    subhead: 'Verify prescriptions, check allergy and drug interaction history, and log dispensing events securely.'
  }
};

function updateLeftPanel(type) {
  var hl = document.getElementById('leftHeadline');
  var sh = document.getElementById('leftSubhead');
  if (!hl) return;
  var c = LEFT_PANELS[type] || LEFT_PANELS.default;
  hl.innerHTML = c.headline;
  sh.textContent = c.subhead;
}

/* ── Type selector ──────────────────────────────────────── */
var currentType = '';

function selectType(type) {
  currentType = type;
  document.getElementById('regType').value = type;
  document.getElementById('typeScreen').style.display = 'none';
  document.getElementById('patientWizard').style.display  = 'none';
  document.getElementById('providerWizard').style.display = 'none';
  document.getElementById('orgWizard').style.display      = 'none';

  updateLeftPanel(type);

  if (type === 'patient') {
    document.getElementById('patientWizard').style.display = '';
    pSetProg(1);
  } else if (type === 'provider') {
    document.getElementById('providerWizard').style.display = '';
    prSetProg(1);
  } else {
    document.getElementById('orgWizard').style.display = '';
    oSetProg(1);
    oConfigureForType(type);
  }
  window.scrollTo(0, 0);
}

function backToTypes() {
  document.getElementById('typeScreen').style.display = '';
  document.getElementById('patientWizard').style.display  = 'none';
  document.getElementById('providerWizard').style.display = 'none';
  document.getElementById('orgWizard').style.display      = 'none';
  updateLeftPanel('default');
  window.scrollTo(0, 0);
}

/* ── Org type configuration ─────────────────────────────── */
function oConfigureForType(type) {
  var isPharmacy = (type === 'pharmacy');
  var badge = document.getElementById('orgBadge');
  if (badge) {
    badge.className = 'wizard-badge ' + (isPharmacy ? 'c-pharmacy' : 'c-hospital');
    badge.querySelector('span:first-child').textContent = isPharmacy ? '💊' : '🏥';
    document.getElementById('orgBadgeLabel').textContent = isPharmacy ? 'Pharmacy Registration' : 'Organisation Registration';
  }
  document.getElementById('o-title1').textContent = isPharmacy ? 'Register your pharmacy' : 'Register your organisation';
  document.getElementById('o-title2').textContent = isPharmacy ? 'Pharmacy details' : 'Organisation details';
  document.getElementById('o-name-label').innerHTML = (isPharmacy ? 'Pharmacy name' : 'Organisation name') + ' <span class="req">*</span>';
  document.getElementById('o-org-name').placeholder  = isPharmacy ? 'Wellness Pharmacy Ltd' : 'Lagos General Hospital';
  document.getElementById('o-lic-label').textContent  = isPharmacy ? 'PCN Licence No.' : 'Licence / Registration No.';
  document.getElementById('o-license').placeholder    = isPharmacy ? 'e.g. PCN/2020/LAG-001' : 'e.g. MOH/2020/LAG-001';

  var typeField = document.getElementById('o-type-field');
  if (isPharmacy) {
    typeField.style.display = 'none';
    var existing = document.getElementById('o-type-hidden');
    if (!existing) {
      var inp = document.createElement('input');
      inp.type = 'hidden'; inp.name = 'org_type'; inp.id = 'o-type-hidden'; inp.value = 'pharmacy';
      typeField.parentNode.insertBefore(inp, typeField);
    }
  } else {
    typeField.style.display = '';
    var h = document.getElementById('o-type-hidden');
    if (h) h.remove();
    document.getElementById('o-rv-type-row').style.display = '';
  }
}

/* ── Step entry animation helper ────────────────────────── */
function animateStep(el) {
  el.classList.remove('wstep-enter');
  void el.offsetWidth; /* reflow */
  el.classList.add('wstep-enter');
}

/* ══ PATIENT progress / steps ════════════════════════════ */
function pSetProg(step) {
  ['s1','s2','s3'].forEach(function(s, i) {
    var el = document.getElementById('p-' + s);
    el.classList.remove('active','done');
    if (i+1 < step) { el.classList.add('done'); el.querySelector('.step-dot').textContent = '✓'; }
    else { el.querySelector('.step-dot').textContent = i+1; }
    if (i+1 === step) el.classList.add('active');
  });
  ['l1','l2'].forEach(function(l, i) {
    var el = document.getElementById('p-' + l);
    if (el) el.classList.toggle('done', i+1 < step);
  });
}
function pShowStep(n) {
  ['step1','step2','step3'].forEach(function(s, i) {
    var el = document.getElementById('p-' + s);
    el.style.display = (i+1===n) ? '' : 'none';
    if (i+1===n) animateStep(el);
  });
  pSetProg(n); window.scrollTo(0,0);
}

document.addEventListener('DOMContentLoaded', function() {
  bindStrength('p-pw','p-sfill','p-slabel');
  bindStrength('pr-pw','pr-sfill','pr-slabel');
  bindStrength('o-pw','o-sfill','o-slabel');
  bindMatch('p-pw','p-cf','p-mhint');
  bindMatch('pr-pw','pr-cf','pr-mhint');
  bindMatch('o-pw','o-cf','o-mhint');
  updateLeftPanel('default');
});

function bindStrength(pwId, fillId, labelId) {
  var el = document.getElementById(pwId);
  if (!el) return;
  el.addEventListener('input', function() {
    var v = this.value;
    var s = 0;
    if (v.length>=8) s++; if (v.length>=12) s++;
    if (/[A-Z]/.test(v)) s++; if (/[0-9]/.test(v)) s++; if (/[^A-Za-z0-9]/.test(v)) s++;
    var fill  = document.getElementById(fillId);
    var label = document.getElementById(labelId);
    var pcts  = ['0%','20%','40%','65%','85%','100%'];
    var cols  = ['#ccc','#B23A48','#E8A030','#B8860B','#4CAF50','#2C5F2D'];
    var texts = ['Enter a password','Too short','Weak','Fair','Good','Strong ✓'];
    fill.style.width = pcts[s]; fill.style.background = cols[s];
    label.textContent = texts[s];
    label.style.color = s >= 4 ? '#2C5F2D' : '#94A3B8';
  });
}

function bindMatch(pwId, cfId, hintId) {
  function check() {
    var hint = document.getElementById(hintId);
    var pw = val(pwId), cf = val(cfId);
    if (!cf) { hint.textContent = ''; return; }
    var ok = pw === cf;
    hint.textContent = ok ? '✓ Passwords match' : '✗ Passwords do not match';
    hint.style.color = ok ? '#2C5F2D' : '#B23A48';
  }
  var pw = document.getElementById(pwId), cf = document.getElementById(cfId);
  if (pw) pw.addEventListener('input', check);
  if (cf) cf.addEventListener('input', check);
}

function pValidStep1() {
  if (!val('p-name'))  { alert('Please enter your full name.'); document.getElementById('p-name').focus(); return false; }
  if (!val('p-email')) { alert('Please enter your email address.'); document.getElementById('p-email').focus(); return false; }
  var phone = val('p-phone_hidden') || val('p-phone');
  if (!phone || !/^\+[1-9]\d{7,14}$/.test(phone)) { alert('Please enter a valid phone number with country code.'); return false; }
  if (val('p-pw').length < 8) { alert('Password must be at least 8 characters.'); document.getElementById('p-pw').focus(); return false; }
  if (val('p-pw') !== val('p-cf')) { alert('Passwords do not match.'); document.getElementById('p-cf').focus(); return false; }
  return true;
}
function pGoStep2() { if (pValidStep1()) pShowStep(2); }
function pGoStep1() { pShowStep(1); }
function pGoStep3() {
  var nin = val('p-nin');
  if (nin && !/^\d{11}$/.test(nin)) { alert('NIN must be exactly 11 digits or leave it blank.'); document.getElementById('p-nin').focus(); return; }
  document.getElementById('p-rv-name').textContent  = val('p-name')  || '—';
  document.getElementById('p-rv-email').textContent = val('p-email') || '—';
  document.getElementById('p-rv-phone').textContent = val('p-phone_hidden') || val('p-phone') || '—';
  var gdr   = document.getElementById('p-gender');
  var blood = document.getElementById('p-blood');
  var geno  = document.getElementById('p-geno');
  var state = document.getElementById('p-state');
  var dob   = val('p-dob');
  document.getElementById('p-rv-dob').textContent    = dob ? fmtDate(dob) : 'Not provided';
  document.getElementById('p-rv-gender').textContent = (gdr && gdr.value) ? gdr.options[gdr.selectedIndex].text : 'Not provided';
  document.getElementById('p-rv-blood').textContent  = (blood && blood.value) || 'Unknown';
  document.getElementById('p-rv-geno').textContent   = (geno && geno.value)  || 'Unknown';
  document.getElementById('p-rv-state').textContent  = (state && state.value) || 'Not provided';
  document.getElementById('p-rv-nin').textContent    = nin ? nin.replace(/(\d{3})(\d{4})(\d{4})/, '$1-$2-$3') : 'Not provided';
  pShowStep(3);
}

/* ══ PROVIDER progress / steps ════════════════════════════ */
function prSetProg(step) {
  ['s1','s2','s3'].forEach(function(s, i) {
    var el = document.getElementById('pr-' + s);
    el.classList.remove('active','done');
    if (i+1 < step) { el.classList.add('done'); el.querySelector('.step-dot').textContent = '✓'; }
    else { el.querySelector('.step-dot').textContent = i+1; }
    if (i+1 === step) el.classList.add('active');
  });
  ['l1','l2'].forEach(function(l, i) {
    var el = document.getElementById('pr-' + l);
    if (el) el.classList.toggle('done', i+1 < step);
  });
}
function prShowStep(n) {
  ['step1','step2','step3'].forEach(function(s, i) {
    var el = document.getElementById('pr-' + s);
    el.style.display = (i+1===n) ? '' : 'none';
    if (i+1===n) animateStep(el);
  });
  prSetProg(n); window.scrollTo(0,0);
}
function prValidStep1() {
  if (!val('pr-name'))  { alert('Please enter your full name.'); document.getElementById('pr-name').focus(); return false; }
  if (!val('pr-email')) { alert('Please enter your email address.'); document.getElementById('pr-email').focus(); return false; }
  var phone = val('pr-phone_hidden') || val('pr-phone');
  if (!phone || !/^\+[1-9]\d{7,14}$/.test(phone)) { alert('Please enter a valid phone number with country code.'); return false; }
  if (val('pr-pw').length < 8) { alert('Password must be at least 8 characters.'); document.getElementById('pr-pw').focus(); return false; }
  if (val('pr-pw') !== val('pr-cf')) { alert('Passwords do not match.'); document.getElementById('pr-cf').focus(); return false; }
  return true;
}
function prGoStep2() { if (prValidStep1()) prShowStep(2); }
function prGoStep1() { prShowStep(1); }
function prGoStep3() {
  var spec = val('pr-specialty');
  if (!spec) { alert('Please select your specialty.'); document.getElementById('pr-specialty').focus(); return; }
  document.getElementById('pr-rv-name').textContent  = val('pr-name')  || '—';
  document.getElementById('pr-rv-email').textContent = val('pr-email') || '—';
  document.getElementById('pr-rv-phone').textContent = val('pr-phone_hidden') || val('pr-phone') || '—';
  document.getElementById('pr-rv-spec').textContent  = spec || '—';
  document.getElementById('pr-rv-folio').textContent = val('pr-folio') || 'Not provided';
  document.getElementById('pr-rv-lic').textContent   = val('pr-license') || 'Not provided';
  prShowStep(3);
}

/* ══ ORG progress / steps ══════════════════════════════════ */
function oSetProg(step) {
  ['s1','s2','s3'].forEach(function(s, i) {
    var el = document.getElementById('o-' + s);
    el.classList.remove('active','done');
    if (i+1 < step) { el.classList.add('done'); el.querySelector('.step-dot').textContent = '✓'; }
    else { el.querySelector('.step-dot').textContent = i+1; }
    if (i+1 === step) el.classList.add('active');
  });
  ['l1','l2'].forEach(function(l, i) {
    var el = document.getElementById('o-' + l);
    if (el) el.classList.toggle('done', i+1 < step);
  });
}
function oShowStep(n) {
  ['step1','step2','step3'].forEach(function(s, i) {
    var el = document.getElementById('o-' + s);
    el.style.display = (i+1===n) ? '' : 'none';
    if (i+1===n) animateStep(el);
  });
  oSetProg(n); window.scrollTo(0,0);
}
function oValidStep1() {
  if (!val('o-name'))  { alert('Please enter your full name.'); document.getElementById('o-name').focus(); return false; }
  if (!val('o-email')) { alert('Please enter your email address.'); document.getElementById('o-email').focus(); return false; }
  var phone = val('o-phone_hidden') || val('o-phone');
  if (!phone || !/^\+[1-9]\d{7,14}$/.test(phone)) { alert('Please enter a valid phone number with country code.'); return false; }
  if (val('o-pw').length < 8) { alert('Password must be at least 8 characters.'); document.getElementById('o-pw').focus(); return false; }
  if (val('o-pw') !== val('o-cf')) { alert('Passwords do not match.'); document.getElementById('o-cf').focus(); return false; }
  return true;
}
function oGoStep1() { oShowStep(1); }
function oGoStep2() { if (oValidStep1()) oShowStep(2); }
function oGoStep3() {
  if (!val('o-org-name')) { alert('Please enter the organisation name.'); document.getElementById('o-org-name').focus(); return; }
  var isPharmacy = (currentType === 'pharmacy');
  document.getElementById('o-rv-name').textContent  = val('o-name')  || '—';
  document.getElementById('o-rv-email').textContent = val('o-email') || '—';
  document.getElementById('o-rv-phone').textContent = val('o-phone_hidden') || val('o-phone') || '—';
  document.getElementById('o-rv-org').textContent   = val('o-org-name') || '—';
  if (!isPharmacy) {
    var ot = document.getElementById('o-org-type');
    document.getElementById('o-rv-type').textContent = ot && ot.options[ot.selectedIndex] ? ot.options[ot.selectedIndex].text : '—';
  } else {
    document.getElementById('o-rv-type-row').style.display = 'none';
  }
  var addr  = val('o-address');
  var city  = val('o-city');
  var state = document.getElementById('o-state');
  document.getElementById('o-rv-lic').textContent   = val('o-license') || 'Not provided';
  document.getElementById('o-rv-addr').textContent  = [addr, city].filter(Boolean).join(', ') || 'Not provided';
  document.getElementById('o-rv-state').textContent = (state && state.value) || 'Not provided';
  oShowStep(3);
}

/* ── Submit guard ────────────────────────────────────────── */
document.getElementById('regForm').addEventListener('submit', function(e) {
  var type = document.getElementById('regType').value;
  var termsId = type === 'patient' ? 'p-terms' : (type === 'provider' ? 'pr-terms' : 'o-terms');
  var terms = document.getElementById(termsId);
  if (terms && !terms.checked) {
    e.preventDefault();
    alert('Please accept the Terms of Service to continue.');
    return;
  }
  var submitIds = { patient: 'p-submit', provider: 'pr-submit', hospital: 'o-submit', clinic: 'o-submit', pharmacy: 'o-submit' };
  var btn = document.getElementById(submitIds[type]);
  if (btn) { btn.disabled = true; btn.textContent = 'Creating account…'; }
});

/* ── Re-open correct wizard on server validation error ─────── */
@if($errors->any())
  @php $t = old('reg_type','patient'); @endphp
  (function() {
    var type = '{{ $t }}';
    if (type === 'provider') {
      selectType('provider');
      @if($errors->has('specialty') || $errors->has('license_no') || $errors->has('mdcn_folio'))
        prShowStep(2);
      @else
        prShowStep(1);
      @endif
    } else if (type === 'hospital' || type === 'clinic' || type === 'pharmacy') {
      selectType(type);
      @if($errors->has('org_name') || $errors->has('org_type') || $errors->has('org_license'))
        oShowStep(2);
      @else
        oShowStep(1);
      @endif
    } else {
      selectType('patient');
      @if($errors->has('dob') || $errors->has('gender') || $errors->has('blood_type') || $errors->has('nin'))
        pShowStep(2);
      @else
        pShowStep(1);
      @endif
    }
  })();
@endif
</script>

@endsection
