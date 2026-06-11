@extends('layouts.auth')
@section('title', 'Create Account')
@section('content')

{{-- Server-side errors --}}
@if($errors->any())
<div class="re-alert">
  @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('register') }}" id="regForm" novalidate>
  @csrf
  <input type="hidden" name="reg_type" id="regType" value="{{ old('reg_type','') }}">

{{-- ══════════════════════════════════════════
     SCREEN 0 — TYPE SELECTION
══════════════════════════════════════════ --}}
<div id="s-type">
  <div class="re-header">
    <h2>Create your account</h2>
    <p class="re-sub">What type of account do you need?</p>
  </div>

  <div class="re-type-list">

    <button type="button" class="re-type-btn" onclick="pickType('patient')">
      <span class="re-type-icon" style="background:#EEF3FA;">👤</span>
      <span class="re-type-text">
        <span class="re-type-name">Patient</span>
        <span class="re-type-desc">Manage your personal health record</span>
      </span>
      <span class="re-type-arrow">›</span>
    </button>

    <button type="button" class="re-type-btn" onclick="pickType('provider')">
      <span class="re-type-icon" style="background:#EDFAF0;">🩺</span>
      <span class="re-type-text">
        <span class="re-type-name">Doctor / Provider</span>
        <span class="re-type-desc">Register as a licensed practitioner</span>
      </span>
      <span class="re-type-arrow">›</span>
    </button>

    <button type="button" class="re-type-btn" onclick="pickType('hospital')">
      <span class="re-type-icon" style="background:#FFF7ED;">🏥</span>
      <span class="re-type-text">
        <span class="re-type-name">Hospital / Clinic</span>
        <span class="re-type-desc">Register your healthcare facility</span>
      </span>
      <span class="re-type-arrow">›</span>
    </button>

    <button type="button" class="re-type-btn" onclick="pickType('pharmacy')">
      <span class="re-type-icon" style="background:#F5F3FF;">💊</span>
      <span class="re-type-text">
        <span class="re-type-name">Pharmacy</span>
        <span class="re-type-desc">Register your pharmacy organisation</span>
      </span>
      <span class="re-type-arrow">›</span>
    </button>

  </div>

  <p class="re-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
</div>{{-- /s-type --}}


{{-- ══════════════════════════════════════════
     SCREEN 1 — PATIENT WIZARD
══════════════════════════════════════════ --}}
<div id="s-patient" style="display:none;">

  <button type="button" class="re-back" onclick="backToTypes()">← Change type</button>
  <div class="re-badge" style="background:#EEF3FA;color:#1E3A5F;">👤 Patient Registration</div>

  {{-- Progress indicator --}}
  <div class="re-progress">
    <div class="re-prog-item" id="pp-1"><div class="re-prog-dot active">1</div><div class="re-prog-lbl">Account</div></div>
    <div class="re-prog-line" id="pp-l1"></div>
    <div class="re-prog-item" id="pp-2"><div class="re-prog-dot">2</div><div class="re-prog-lbl">Health info</div></div>
    <div class="re-prog-line" id="pp-l2"></div>
    <div class="re-prog-item" id="pp-3"><div class="re-prog-dot">3</div><div class="re-prog-lbl">Review</div></div>
  </div>

  {{-- Step 1: Account --}}
  <div id="p1" class="re-step">
    <div class="re-step-hd"><h2>Your account</h2><p class="re-sub">Free, secure, and private</p></div>

    <div class="re-field">
      <label class="re-label">Full name <span class="re-req">*</span></label>
      <input class="re-input" type="text" name="name" id="p-name" value="{{ old('name') }}" placeholder="Adaeze Okonkwo" autocomplete="name">
      <span class="re-hint">As it appears on your government-issued ID</span>
    </div>
    <div class="re-field">
      <label class="re-label">Email address <span class="re-req">*</span></label>
      <input class="re-input" type="email" name="email" id="p-email" value="{{ old('email') }}" placeholder="adaeze@gmail.com" autocomplete="email">
    </div>
    <div class="re-field">
      <label class="re-label">Phone number <span class="re-req">*</span></label>
      <x-phone-input name="phone" id="p-phone" :value="old('phone','')" :required="true" />
    </div>
    <div class="re-field">
      <label class="re-label">Password <span class="re-req">*</span></label>
      <div class="re-pw-row">
        <input class="re-input re-pw-input" type="password" name="password" id="p-pw" placeholder="Minimum 8 characters" autocomplete="new-password">
        <button type="button" class="re-pw-eye" onclick="rePwToggle('p-pw','p-pw-ic')"><span id="p-pw-ic">👁</span></button>
      </div>
      <div class="re-strength"><div class="re-strength-bar" id="p-sw"></div></div>
      <span class="re-hint" id="p-swlbl">Enter a password</span>
    </div>
    <div class="re-field">
      <label class="re-label">Confirm password <span class="re-req">*</span></label>
      <div class="re-pw-row">
        <input class="re-input re-pw-input" type="password" name="password_confirmation" id="p-cf" placeholder="Repeat your password" autocomplete="new-password">
        <button type="button" class="re-pw-eye" onclick="rePwToggle('p-cf','p-cf-ic')"><span id="p-cf-ic">👁</span></button>
      </div>
      <span class="re-hint" id="p-match"></span>
    </div>

    <button type="button" class="re-btn-primary" onclick="pNext1()">Continue →</button>
    <p class="re-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>{{-- /p1 --}}

  {{-- Step 2: Health info --}}
  <div id="p2" class="re-step" style="display:none;">
    <div class="re-step-hd"><h2>Health profile</h2><p class="re-sub">Helps providers give better care — all optional</p></div>

    <div class="re-row2">
      <div class="re-field">
        <label class="re-label">Date of birth</label>
        <input class="re-input" type="date" name="dob" id="p-dob" value="{{ old('dob') }}" max="{{ date('Y-m-d', strtotime('-1 day')) }}">
      </div>
      <div class="re-field">
        <label class="re-label">Gender</label>
        <select class="re-input" name="gender" id="p-gender">
          <option value="">Select…</option>
          <option value="male"   {{ old('gender')==='male'   ? 'selected':'' }}>Male</option>
          <option value="female" {{ old('gender')==='female' ? 'selected':'' }}>Female</option>
          <option value="other"  {{ old('gender')==='other'  ? 'selected':'' }}>Other</option>
        </select>
      </div>
    </div>
    <div class="re-row2">
      <div class="re-field">
        <label class="re-label">Blood type</label>
        <select class="re-input" name="blood_type" id="p-blood">
          <option value="">Unknown</option>
          @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
            <option value="{{ $bt }}" {{ old('blood_type')===$bt ? 'selected':'' }}>{{ $bt }}</option>
          @endforeach
        </select>
      </div>
      <div class="re-field">
        <label class="re-label">Genotype</label>
        <select class="re-input" name="genotype" id="p-geno">
          <option value="">Unknown</option>
          @foreach(['AA','AS','SS','AC','SC'] as $g)
            <option value="{{ $g }}" {{ old('genotype')===$g ? 'selected':'' }}>{{ $g }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="re-field">
      <label class="re-label">State of residence</label>
      <select class="re-input" name="state" id="p-state">
        <option value="">Select state…</option>
        @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $st)
          <option value="{{ $st }}" {{ old('state')===$st ? 'selected':'' }}>{{ $st }}</option>
        @endforeach
      </select>
    </div>
    <div class="re-field">
      <label class="re-label">NIN (National Identity Number)</label>
      <input class="re-input" type="text" name="nin" id="p-nin" value="{{ old('nin') }}" placeholder="12345678901" maxlength="11" inputmode="numeric">
      <span class="re-hint">11-digit NIN — optional, used for identity verification</span>
    </div>

    <div class="re-btn-row">
      <button type="button" class="re-btn-back" onclick="pBack2()">← Back</button>
      <button type="button" class="re-btn-primary" onclick="pNext2()">Review →</button>
    </div>
  </div>{{-- /p2 --}}

  {{-- Step 3: Review --}}
  <div id="p3" class="re-step" style="display:none;">
    <div class="re-step-hd"><h2>Review & confirm</h2><p class="re-sub">Check your details before creating your account</p></div>

    <div class="re-review">
      <div class="re-review-head">Account</div>
      <div class="re-review-row"><span>Name</span><strong id="p-rv-name">—</strong></div>
      <div class="re-review-row"><span>Email</span><strong id="p-rv-email">—</strong></div>
      <div class="re-review-row"><span>Phone</span><strong id="p-rv-phone">—</strong></div>
      <div class="re-review-row"><span>Password</span><strong>••••••••</strong></div>
    </div>
    <div class="re-review">
      <div class="re-review-head">Health profile</div>
      <div class="re-review-row"><span>Date of birth</span><strong id="p-rv-dob">Not provided</strong></div>
      <div class="re-review-row"><span>Gender</span><strong id="p-rv-gender">Not provided</strong></div>
      <div class="re-review-row"><span>Blood type</span><strong id="p-rv-blood">Unknown</strong></div>
      <div class="re-review-row"><span>Genotype</span><strong id="p-rv-geno">Unknown</strong></div>
      <div class="re-review-row"><span>State</span><strong id="p-rv-state">Not provided</strong></div>
      <div class="re-review-row"><span>NIN</span><strong id="p-rv-nin">Not provided</strong></div>
    </div>

    <label class="re-terms">
      <input type="checkbox" name="terms" id="p-terms" style="width:16px;height:16px;flex-shrink:0;accent-color:#1E3A5F;cursor:pointer;">
      I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. My health data is stored securely and shared only with providers I consent to.
    </label>

    <div class="re-btn-row">
      <button type="button" class="re-btn-back" onclick="pBack3()">← Back</button>
      <button type="submit" class="re-btn-primary re-btn-green" id="p-submit">Create account</button>
    </div>
    <p class="re-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>{{-- /p3 --}}

</div>{{-- /s-patient --}}


{{-- ══════════════════════════════════════════
     SCREEN 2 — PROVIDER WIZARD
══════════════════════════════════════════ --}}
<div id="s-provider" style="display:none;">

  <button type="button" class="re-back" onclick="backToTypes()">← Change type</button>
  <div class="re-badge" style="background:#EDFAF0;color:#166534;">🩺 Provider Registration</div>

  <div class="re-progress">
    <div class="re-prog-item" id="prp-1"><div class="re-prog-dot active">1</div><div class="re-prog-lbl">Account</div></div>
    <div class="re-prog-line" id="prp-l1"></div>
    <div class="re-prog-item" id="prp-2"><div class="re-prog-dot">2</div><div class="re-prog-lbl">Professional</div></div>
    <div class="re-prog-line" id="prp-l2"></div>
    <div class="re-prog-item" id="prp-3"><div class="re-prog-dot">3</div><div class="re-prog-lbl">Review</div></div>
  </div>

  {{-- Step 1 --}}
  <div id="pr1" class="re-step">
    <div class="re-step-hd"><h2>Your account</h2><p class="re-sub">Your professional provider profile</p></div>

    <div class="re-field">
      <label class="re-label">Full name <span class="re-req">*</span></label>
      <input class="re-input" type="text" name="name" id="pr-name" value="{{ old('name') }}" placeholder="Dr. Emeka Obi" autocomplete="name">
      <span class="re-hint">As it appears on your MDCN registration</span>
    </div>
    <div class="re-field">
      <label class="re-label">Email address <span class="re-req">*</span></label>
      <input class="re-input" type="email" name="email" id="pr-email" value="{{ old('email') }}" placeholder="emeka.obi@gmail.com" autocomplete="email">
    </div>
    <div class="re-field">
      <label class="re-label">Phone number <span class="re-req">*</span></label>
      <x-phone-input name="phone" id="pr-phone" :value="old('phone','')" :required="true" />
    </div>
    <div class="re-field">
      <label class="re-label">Password <span class="re-req">*</span></label>
      <div class="re-pw-row">
        <input class="re-input re-pw-input" type="password" name="password" id="pr-pw" placeholder="Minimum 8 characters" autocomplete="new-password">
        <button type="button" class="re-pw-eye" onclick="rePwToggle('pr-pw','pr-pw-ic')"><span id="pr-pw-ic">👁</span></button>
      </div>
      <div class="re-strength"><div class="re-strength-bar" id="pr-sw"></div></div>
      <span class="re-hint" id="pr-swlbl">Enter a password</span>
    </div>
    <div class="re-field">
      <label class="re-label">Confirm password <span class="re-req">*</span></label>
      <div class="re-pw-row">
        <input class="re-input re-pw-input" type="password" name="password_confirmation" id="pr-cf" placeholder="Repeat your password" autocomplete="new-password">
        <button type="button" class="re-pw-eye" onclick="rePwToggle('pr-cf','pr-cf-ic')"><span id="pr-cf-ic">👁</span></button>
      </div>
      <span class="re-hint" id="pr-match"></span>
    </div>

    <button type="button" class="re-btn-primary" onclick="prNext1()">Continue →</button>
    <p class="re-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>

  {{-- Step 2 --}}
  <div id="pr2" class="re-step" style="display:none;">
    <div class="re-step-hd"><h2>Professional details</h2><p class="re-sub">Your clinical credentials</p></div>

    <div class="re-field">
      <label class="re-label">Specialty <span class="re-req">*</span></label>
      <select class="re-input" name="specialty" id="pr-specialty">
        <option value="">Select specialty…</option>
        @foreach(['General Practice','Internal Medicine','Paediatrics','Obstetrics & Gynaecology','Surgery','Cardiology','Neurology','Psychiatry','Dermatology','Radiology','Anaesthesia','Ophthalmology','ENT','Orthopaedics','Urology','Nephrology','Endocrinology','Gastroenterology','Oncology','Emergency Medicine','Family Medicine','Community Medicine','Pathology','Pharmacology','Physiotherapy','Dentistry','Nursing','Pharmacy','Medical Laboratory Science','Radiography','Other'] as $sp)
          <option value="{{ $sp }}" {{ old('specialty')===$sp ? 'selected':'' }}>{{ $sp }}</option>
        @endforeach
      </select>
    </div>
    <div class="re-field">
      <label class="re-label">MDCN Folio Number</label>
      <input class="re-input" type="text" name="mdcn_folio" id="pr-folio" value="{{ old('mdcn_folio') }}" placeholder="e.g. MDCN/2018/12345">
      <span class="re-hint">Medical and Dental Council of Nigeria registration number</span>
    </div>
    <div class="re-field">
      <label class="re-label">Professional Licence No.</label>
      <input class="re-input" type="text" name="license_no" id="pr-lic" value="{{ old('license_no') }}" placeholder="e.g. FMC/2019/00456">
      <span class="re-hint">You can add this later</span>
    </div>

    <div class="re-info">
      <span>ℹ</span>
      You can join a facility after registration. A facility administrator will add you to their team, or you can operate independently.
    </div>

    <div class="re-btn-row">
      <button type="button" class="re-btn-back" onclick="prBack2()">← Back</button>
      <button type="button" class="re-btn-primary" onclick="prNext2()">Review →</button>
    </div>
  </div>

  {{-- Step 3 --}}
  <div id="pr3" class="re-step" style="display:none;">
    <div class="re-step-hd"><h2>Review & confirm</h2><p class="re-sub">Check your details before creating your account</p></div>

    <div class="re-review">
      <div class="re-review-head">Account</div>
      <div class="re-review-row"><span>Name</span><strong id="pr-rv-name">—</strong></div>
      <div class="re-review-row"><span>Email</span><strong id="pr-rv-email">—</strong></div>
      <div class="re-review-row"><span>Phone</span><strong id="pr-rv-phone">—</strong></div>
    </div>
    <div class="re-review">
      <div class="re-review-head">Professional</div>
      <div class="re-review-row"><span>Specialty</span><strong id="pr-rv-spec">—</strong></div>
      <div class="re-review-row"><span>MDCN Folio</span><strong id="pr-rv-folio">Not provided</strong></div>
      <div class="re-review-row"><span>Licence No.</span><strong id="pr-rv-lic">Not provided</strong></div>
      <div class="re-review-row"><span>Facility</span><strong>To be assigned</strong></div>
    </div>

    <label class="re-terms">
      <input type="checkbox" name="terms" id="pr-terms" style="width:16px;height:16px;flex-shrink:0;accent-color:#1E3A5F;cursor:pointer;">
      I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. I confirm my credentials are accurate.
    </label>

    <div class="re-btn-row">
      <button type="button" class="re-btn-back" onclick="prBack3()">← Back</button>
      <button type="submit" class="re-btn-primary re-btn-green" id="pr-submit">Create account</button>
    </div>
    <p class="re-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>

</div>{{-- /s-provider --}}


{{-- ══════════════════════════════════════════
     SCREEN 3 — ORG WIZARD (hospital / clinic / pharmacy)
══════════════════════════════════════════ --}}
<div id="s-org" style="display:none;">

  <button type="button" class="re-back" onclick="backToTypes()">← Change type</button>
  <div class="re-badge" id="org-badge" style="background:#FFF7ED;color:#92400E;">🏥 Organisation Registration</div>

  <div class="re-progress">
    <div class="re-prog-item" id="op-1"><div class="re-prog-dot active">1</div><div class="re-prog-lbl">Admin</div></div>
    <div class="re-prog-line" id="op-l1"></div>
    <div class="re-prog-item" id="op-2"><div class="re-prog-dot">2</div><div class="re-prog-lbl">Organisation</div></div>
    <div class="re-prog-line" id="op-l2"></div>
    <div class="re-prog-item" id="op-3"><div class="re-prog-dot">3</div><div class="re-prog-lbl">Review</div></div>
  </div>

  {{-- Step 1 --}}
  <div id="o1" class="re-step">
    <div class="re-step-hd">
      <h2 id="o-title1">Register your organisation</h2>
      <p class="re-sub">Create the administrator account</p>
    </div>

    <div class="re-field">
      <label class="re-label">Your full name <span class="re-req">*</span></label>
      <input class="re-input" type="text" name="name" id="o-name" value="{{ old('name') }}" placeholder="Chidi Nwosu" autocomplete="name">
      <span class="re-hint">Name of the account administrator</span>
    </div>
    <div class="re-field">
      <label class="re-label">Your email address <span class="re-req">*</span></label>
      <input class="re-input" type="email" name="email" id="o-email" value="{{ old('email') }}" placeholder="admin@yourhospital.ng" autocomplete="email">
    </div>
    <div class="re-field">
      <label class="re-label">Your phone number <span class="re-req">*</span></label>
      <x-phone-input name="phone" id="o-phone" :value="old('phone','')" :required="true" />
    </div>
    <div class="re-field">
      <label class="re-label">Password <span class="re-req">*</span></label>
      <div class="re-pw-row">
        <input class="re-input re-pw-input" type="password" name="password" id="o-pw" placeholder="Minimum 8 characters" autocomplete="new-password">
        <button type="button" class="re-pw-eye" onclick="rePwToggle('o-pw','o-pw-ic')"><span id="o-pw-ic">👁</span></button>
      </div>
      <div class="re-strength"><div class="re-strength-bar" id="o-sw"></div></div>
      <span class="re-hint" id="o-swlbl">Enter a password</span>
    </div>
    <div class="re-field">
      <label class="re-label">Confirm password <span class="re-req">*</span></label>
      <div class="re-pw-row">
        <input class="re-input re-pw-input" type="password" name="password_confirmation" id="o-cf" placeholder="Repeat your password" autocomplete="new-password">
        <button type="button" class="re-pw-eye" onclick="rePwToggle('o-cf','o-cf-ic')"><span id="o-cf-ic">👁</span></button>
      </div>
      <span class="re-hint" id="o-match"></span>
    </div>

    <button type="button" class="re-btn-primary" onclick="oNext1()">Continue →</button>
    <p class="re-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>

  {{-- Step 2 --}}
  <div id="o2" class="re-step" style="display:none;">
    <div class="re-step-hd">
      <h2 id="o-title2">Organisation details</h2>
      <p class="re-sub">Your facility's information</p>
    </div>

    <div class="re-field">
      <label class="re-label" id="o-name-lbl">Organisation name <span class="re-req">*</span></label>
      <input class="re-input" type="text" name="org_name" id="o-org-name" value="{{ old('org_name') }}" placeholder="Lagos General Hospital">
    </div>

    <div class="re-field" id="o-type-wrap">
      <label class="re-label">Facility type <span class="re-req">*</span></label>
      <select class="re-input" name="org_type" id="o-org-type">
        <option value="hospital" {{ old('org_type','hospital')==='hospital' ? 'selected':'' }}>Hospital</option>
        <option value="clinic"   {{ old('org_type')==='clinic'   ? 'selected':'' }}>Clinic</option>
        <option value="lab"      {{ old('org_type')==='lab'      ? 'selected':'' }}>Diagnostic Lab</option>
        <option value="dental"   {{ old('org_type')==='dental'   ? 'selected':'' }}>Dental Practice</option>
        <option value="optical"  {{ old('org_type')==='optical'  ? 'selected':'' }}>Optical / Eye Care</option>
      </select>
    </div>

    <div class="re-field">
      <label class="re-label" id="o-lic-lbl">Licence / Registration No.</label>
      <input class="re-input" type="text" name="org_license" id="o-lic" value="{{ old('org_license') }}" placeholder="e.g. MOH/2020/LAG-001">
    </div>
    <div class="re-field">
      <label class="re-label">Street address</label>
      <input class="re-input" type="text" name="org_address" id="o-addr" value="{{ old('org_address') }}" placeholder="14 Broad Street">
    </div>
    <div class="re-row2">
      <div class="re-field">
        <label class="re-label">City</label>
        <input class="re-input" type="text" name="org_city" id="o-city" value="{{ old('org_city') }}" placeholder="Lagos">
      </div>
      <div class="re-field">
        <label class="re-label">State</label>
        <select class="re-input" name="org_state" id="o-state">
          <option value="">Select…</option>
          @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $st)
            <option value="{{ $st }}" {{ old('org_state')===$st ? 'selected':'' }}>{{ $st }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="re-row2">
      <div class="re-field">
        <label class="re-label">Organisation phone</label>
        <input class="re-input" type="text" name="org_phone" id="o-org-phone" value="{{ old('org_phone') }}" placeholder="+2341234567890">
      </div>
      <div class="re-field">
        <label class="re-label">Organisation email</label>
        <input class="re-input" type="email" name="org_email" id="o-org-email" value="{{ old('org_email') }}" placeholder="info@yourhospital.ng">
      </div>
    </div>

    <div class="re-btn-row">
      <button type="button" class="re-btn-back" onclick="oBack2()">← Back</button>
      <button type="button" class="re-btn-primary" onclick="oNext2()">Review →</button>
    </div>
  </div>

  {{-- Step 3 --}}
  <div id="o3" class="re-step" style="display:none;">
    <div class="re-step-hd"><h2>Review & confirm</h2><p class="re-sub">Check everything before registering</p></div>

    <div class="re-review">
      <div class="re-review-head">Administrator account</div>
      <div class="re-review-row"><span>Name</span><strong id="o-rv-name">—</strong></div>
      <div class="re-review-row"><span>Email</span><strong id="o-rv-email">—</strong></div>
      <div class="re-review-row"><span>Phone</span><strong id="o-rv-phone">—</strong></div>
    </div>
    <div class="re-review">
      <div class="re-review-head">Organisation</div>
      <div class="re-review-row"><span>Name</span><strong id="o-rv-org">—</strong></div>
      <div class="re-review-row" id="o-rv-type-row"><span>Type</span><strong id="o-rv-type">—</strong></div>
      <div class="re-review-row"><span>Licence No.</span><strong id="o-rv-lic">Not provided</strong></div>
      <div class="re-review-row"><span>Address</span><strong id="o-rv-addr">Not provided</strong></div>
      <div class="re-review-row"><span>State</span><strong id="o-rv-state">Not provided</strong></div>
    </div>

    <label class="re-terms">
      <input type="checkbox" name="terms" id="o-terms" style="width:16px;height:16px;flex-shrink:0;accent-color:#1E3A5F;cursor:pointer;">
      I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. I confirm the organisation details are accurate.
    </label>

    <div class="re-btn-row">
      <button type="button" class="re-btn-back" onclick="oBack3()">← Back</button>
      <button type="submit" class="re-btn-primary re-btn-green" id="o-submit">Register organisation</button>
    </div>
    <p class="re-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>

</div>{{-- /s-org --}}

</form>

{{-- ══════════════════════════════════════════
     STYLES — fully scoped, no auth-layout dependency
══════════════════════════════════════════ --}}
<style nonce="{{ $cspNonce ?? '' }}">

/* Box sizing */
#regForm *, #regForm *::before, #regForm *::after { box-sizing: border-box; }

/* Widen auth box for registration */
.auth-box { max-width: 500px; }

/* Alert */
.re-alert {
  background: #FEF2F2; border-left: 3px solid #DC2626;
  border-radius: 8px; padding: 12px 14px;
  font-size: 13px; color: #7F1D1D; margin-bottom: 18px; line-height: 1.6;
}

/* Page header */
.re-header { margin-bottom: 24px; }
.re-header h2 { font-size: 22px; font-weight: 800; color: #0F172A; letter-spacing: -.02em; margin: 0 0 4px; }
.re-sub { font-size: 14px; color: #64748B; margin: 0; line-height: 1.5; }

/* ── Type list ───────────────────────────────────────── */
.re-type-list {
  border: 1.5px solid #E2E8F0; border-radius: 14px;
  overflow: hidden; background: #fff; margin-bottom: 22px;
}
.re-type-btn {
  display: flex; align-items: center; gap: 14px;
  width: 100%; padding: 15px 18px;
  background: none; border: none; border-bottom: 1px solid #F1F5F9;
  cursor: pointer; text-align: left; font-family: inherit;
  transition: background .12s;
}
.re-type-btn:last-child { border-bottom: none; }
.re-type-btn:hover { background: #F8FAFC; }
.re-type-icon {
  width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 22px;
}
.re-type-text { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.re-type-name { font-size: 14px; font-weight: 700; color: #0F172A; }
.re-type-desc { font-size: 12px; color: #64748B; }
.re-type-arrow { color: #CBD5E1; font-size: 22px; line-height: 1; flex-shrink: 0; transition: transform .15s, color .15s; }
.re-type-btn:hover .re-type-arrow { transform: translateX(3px); color: #94A3B8; }

/* ── Back + badge ────────────────────────────────────── */
.re-back {
  display: inline-flex; align-items: center; gap: 5px;
  background: none; border: none; cursor: pointer;
  font-size: 13px; color: #94A3B8; padding: 0;
  margin-bottom: 14px; font-family: inherit; transition: color .15s;
}
.re-back:hover { color: #1E3A5F; }
.re-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 4px 12px; border-radius: 20px;
  font-size: 12px; font-weight: 600; margin-bottom: 20px;
}

/* ── Progress indicator ──────────────────────────────── */
.re-progress {
  display: flex; align-items: flex-start;
  margin-bottom: 28px;
}
.re-prog-item { display: flex; flex-direction: column; align-items: center; gap: 5px; flex-shrink: 0; }
.re-prog-dot {
  width: 32px; height: 32px; border-radius: 50%;
  background: #E2E8F0; color: #94A3B8;
  font-size: 12px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  transition: background .25s, color .25s;
}
.re-prog-dot.active { background: #1E3A5F; color: #fff; }
.re-prog-dot.done   { background: #2C5F2D; color: #fff; }
.re-prog-lbl { font-size: 10px; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; transition: color .2s; }
.re-prog-item.active .re-prog-lbl { color: #1E3A5F; }
.re-prog-item.done   .re-prog-lbl { color: #2C5F2D; }
.re-prog-line { flex: 1; height: 2px; background: #E2E8F0; min-width: 20px; margin: 15px 4px 0; transition: background .3s; }
.re-prog-line.done { background: #2C5F2D; }

/* ── Step content ────────────────────────────────────── */
.re-step-hd { margin-bottom: 22px; }
.re-step-hd h2 { font-size: 20px; font-weight: 800; color: #0F172A; letter-spacing: -.02em; margin: 0 0 4px; }

/* ── Form fields ─────────────────────────────────────── */
.re-field { margin-bottom: 16px; }
.re-label {
  display: block; font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .06em;
  color: #475569; margin-bottom: 6px;
}
.re-req { color: #DC2626; }
.re-hint { display: block; font-size: 11px; color: #94A3B8; margin-top: 4px; }
.re-input {
  display: block; width: 100%;
  background: #fff; border: 1.5px solid #E2E8F0;
  border-radius: 9px; padding: 10px 14px;
  font-size: 14px; color: #0F172A; font-family: inherit;
  transition: border-color .15s, box-shadow .15s;
  appearance: auto;
}
.re-input:focus {
  outline: none; border-color: #1E3A5F;
  box-shadow: 0 0 0 3px rgba(30,58,95,.08);
}
.re-input::placeholder { color: #CBD5E1; }

/* Two-column row */
.re-row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

/* ── Password field ──────────────────────────────────── */
.re-pw-row {
  position: relative; display: flex; align-items: stretch;
}
.re-pw-input {
  flex: 1; padding-right: 44px !important; border-radius: 9px !important;
}
.re-pw-eye {
  position: absolute; right: 0; top: 0; bottom: 0;
  width: 40px; display: flex; align-items: center; justify-content: center;
  background: none; border: none; cursor: pointer;
  font-size: 15px; color: #94A3B8; transition: color .15s;
}
.re-pw-eye:hover { color: #475569; }

/* Password strength */
.re-strength {
  height: 3px; background: #E2E8F0; border-radius: 3px;
  margin-top: 6px; overflow: hidden;
}
.re-strength-bar {
  height: 100%; width: 0; border-radius: 3px;
  transition: width .3s, background .3s; background: #DC2626;
}

/* ── Info note ───────────────────────────────────────── */
.re-info {
  display: flex; gap: 10px; align-items: flex-start;
  background: #EEF3FA; border-left: 3px solid #1E3A5F;
  border-radius: 0 8px 8px 0; padding: 10px 14px;
  font-size: 12px; color: #1E3A5F; line-height: 1.55;
  margin-bottom: 18px; margin-top: 4px;
}
.re-info span { font-weight: 800; font-size: 13px; flex-shrink: 0; }

/* ── Buttons ─────────────────────────────────────────── */
.re-btn-primary {
  display: block; width: 100%;
  background: #1E3A5F; color: #fff;
  border: none; border-radius: 10px;
  padding: 12px 20px; font-size: 15px; font-weight: 700;
  cursor: pointer; font-family: inherit; margin-top: 6px;
  transition: background .15s, transform .1s; text-align: center;
}
.re-btn-primary:hover { background: #163050; }
.re-btn-primary:active { transform: scale(.99); }
.re-btn-primary:disabled { opacity: .6; cursor: not-allowed; }
.re-btn-green { background: #2C5F2D; }
.re-btn-green:hover { background: #1E4020; }

.re-btn-back {
  background: #F1F5F9; color: #475569;
  border: 1.5px solid #E2E8F0; border-radius: 10px;
  padding: 11px 18px; font-size: 14px; font-weight: 600;
  cursor: pointer; font-family: inherit; transition: background .15s;
  white-space: nowrap;
}
.re-btn-back:hover { background: #E2E8F0; }

.re-btn-row {
  display: flex; gap: 10px; margin-top: 6px; align-items: stretch;
}
.re-btn-row .re-btn-primary { flex: 1; margin-top: 0; width: auto; }

/* ── Review cards ────────────────────────────────────── */
.re-review {
  border: 1.5px solid #E2E8F0; border-radius: 12px;
  overflow: hidden; margin-bottom: 12px; background: #fff;
}
.re-review-head {
  background: #F8FAFC; padding: 7px 16px;
  font-size: 10px; font-weight: 700; text-transform: uppercase;
  letter-spacing: .07em; color: #64748B;
}
.re-review-row {
  display: flex; justify-content: space-between; align-items: baseline;
  padding: 8px 16px; font-size: 13px; border-top: 1px solid #F8FAFC;
}
.re-review-row span { color: #64748B; font-size: 12px; }
.re-review-row strong { text-align: right; max-width: 58%; word-break: break-all; font-weight: 600; color: #0F172A; }

/* ── Terms checkbox ──────────────────────────────────── */
.re-terms {
  display: flex; align-items: flex-start; gap: 10px;
  font-size: 12px; color: #64748B; line-height: 1.55;
  margin-bottom: 16px; cursor: default;
}
.re-terms a { color: #1E3A5F; font-weight: 600; text-decoration: none; }
.re-terms a:hover { text-decoration: underline; }

/* ── Footer link ─────────────────────────────────────── */
.re-footer { text-align: center; margin-top: 20px; font-size: 13px; color: #64748B; }
.re-footer a { color: #1E3A5F; font-weight: 700; text-decoration: none; }
.re-footer a:hover { text-decoration: underline; }

/* ── Responsive ──────────────────────────────────────── */
@media (max-width: 500px) {
  .re-row2 { grid-template-columns: 1fr; }
  .re-btn-row { flex-direction: column; }
  .re-btn-row .re-btn-back,
  .re-btn-row .re-btn-primary { width: 100%; text-align: center; }
}
</style>

{{-- ══════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════ --}}
<script nonce="{{ $cspNonce ?? '' }}">
/* ── Screens / navigation ───────────────────────────── */
var currentType = '';

var SCREENS = ['s-type','s-patient','s-provider','s-org'];

function showOnly(id) {
  SCREENS.forEach(function(s) {
    document.getElementById(s).style.display = s === id ? '' : 'none';
  });
  window.scrollTo(0, 0);
}

function pickType(type) {
  currentType = type;
  document.getElementById('regType').value = type;
  if (type === 'patient')  {
    showOnly('s-patient');
    pSetProg(1); pShowStep(1);
  } else if (type === 'provider') {
    showOnly('s-provider');
    prSetProg(1); prShowStep(1);
  } else {
    // hospital, clinic, pharmacy
    configOrg(type);
    showOnly('s-org');
    oSetProg(1); oShowStep(1);
  }
}

function backToTypes() {
  currentType = '';
  document.getElementById('regType').value = '';
  showOnly('s-type');
}

/* ── Org type configuration ─────────────────────────── */
function configOrg(type) {
  var isPharm = (type === 'pharmacy');
  var badge = document.getElementById('org-badge');
  if (isPharm) {
    badge.style.background = '#F5F3FF'; badge.style.color = '#5B21B6';
    badge.textContent = '💊 Pharmacy Registration';
  } else {
    badge.style.background = '#FFF7ED'; badge.style.color = '#92400E';
    badge.textContent = '🏥 Organisation Registration';
  }
  document.getElementById('o-title1').textContent = isPharm ? 'Register your pharmacy' : 'Register your organisation';
  document.getElementById('o-title2').textContent = isPharm ? 'Pharmacy details' : 'Organisation details';
  document.getElementById('o-name-lbl').innerHTML = (isPharm ? 'Pharmacy name' : 'Organisation name') + ' <span class="re-req">*</span>';
  document.getElementById('o-org-name').placeholder = isPharm ? 'Wellness Pharmacy Ltd' : 'Lagos General Hospital';
  document.getElementById('o-lic-lbl').textContent  = isPharm ? 'PCN Licence No.' : 'Licence / Registration No.';
  document.getElementById('o-lic').placeholder      = isPharm ? 'PCN/2020/LAG-001' : 'MOH/2020/LAG-001';

  var typeWrap = document.getElementById('o-type-wrap');
  var hidden   = document.getElementById('o-type-hidden');
  if (isPharm) {
    typeWrap.style.display = 'none';
    if (!hidden) {
      var inp = document.createElement('input');
      inp.type = 'hidden'; inp.name = 'org_type'; inp.id = 'o-type-hidden'; inp.value = 'pharmacy';
      typeWrap.insertAdjacentElement('afterend', inp);
    }
  } else {
    typeWrap.style.display = '';
    if (hidden) hidden.remove();
    document.getElementById('o-rv-type-row').style.display = '';
  }
}

/* ── Utilities ──────────────────────────────────────── */
function rePwToggle(inputId, iconId) {
  var el = document.getElementById(inputId);
  var ic = document.getElementById(iconId);
  el.type = el.type === 'password' ? 'text' : 'password';
  ic.textContent = el.type === 'password' ? '👁' : '🙈';
}

function gv(id) { return (document.getElementById(id) || {}).value || ''; }
function selText(id) {
  var el = document.getElementById(id);
  return el && el.selectedIndex >= 0 ? el.options[el.selectedIndex].text : '';
}
function fmtDate(s) {
  if (!s) return 'Not provided';
  var d = new Date(s + 'T00:00:00');
  return d.toLocaleDateString('en-NG', {day:'numeric', month:'long', year:'numeric'});
}

function bindStrength(pwId, barId, lblId) {
  var el = document.getElementById(pwId);
  if (!el) return;
  el.addEventListener('input', function() {
    var v = this.value, s = 0;
    if (v.length >= 8) s++; if (v.length >= 12) s++;
    if (/[A-Z]/.test(v)) s++; if (/[0-9]/.test(v)) s++; if (/[^A-Za-z0-9]/.test(v)) s++;
    var bar = document.getElementById(barId);
    var lbl = document.getElementById(lblId);
    var pcts  = ['0%','20%','40%','65%','85%','100%'];
    var cols  = ['#ddd','#DC2626','#D97706','#CA8A04','#16A34A','#166534'];
    var texts = ['Enter a password','Too short','Weak','Fair','Good','Strong ✓'];
    bar.style.width = pcts[s]; bar.style.background = cols[s];
    lbl.textContent = texts[s];
    lbl.style.color = s >= 4 ? '#16A34A' : '#94A3B8';
  });
}

function bindMatch(pwId, cfId, hintId) {
  function check() {
    var hint = document.getElementById(hintId);
    var pw = gv(pwId), cf = gv(cfId);
    if (!cf) { hint.textContent = ''; return; }
    var ok = (pw === cf);
    hint.textContent = ok ? '✓ Passwords match' : '✗ Passwords do not match';
    hint.style.color = ok ? '#16A34A' : '#DC2626';
  }
  var pw = document.getElementById(pwId), cf = document.getElementById(cfId);
  if (pw) pw.addEventListener('input', check);
  if (cf) cf.addEventListener('input', check);
}

document.addEventListener('DOMContentLoaded', function() {
  bindStrength('p-pw','p-sw','p-swlbl');   bindMatch('p-pw','p-cf','p-match');
  bindStrength('pr-pw','pr-sw','pr-swlbl'); bindMatch('pr-pw','pr-cf','pr-match');
  bindStrength('o-pw','o-sw','o-swlbl');    bindMatch('o-pw','o-cf','o-match');
});

function validAccount(nameId, emailId, phoneId, pwId, cfId) {
  if (!gv(nameId))  { alert('Please enter your full name.'); document.getElementById(nameId).focus(); return false; }
  if (!gv(emailId)) { alert('Please enter your email address.'); document.getElementById(emailId).focus(); return false; }
  var ph = gv(phoneId + '_hidden') || gv(phoneId);
  if (!ph || !/^\+[1-9]\d{7,14}$/.test(ph)) { alert('Please enter a valid phone number with country code.'); return false; }
  if (gv(pwId).length < 8) { alert('Password must be at least 8 characters.'); document.getElementById(pwId).focus(); return false; }
  if (gv(pwId) !== gv(cfId)) { alert('Passwords do not match.'); document.getElementById(cfId).focus(); return false; }
  return true;
}

/* ══ PATIENT ════════════════════════════════════════════ */
function pSetProg(step) {
  [1,2,3].forEach(function(n) {
    var item = document.getElementById('pp-' + n);
    var dot  = item.querySelector('.re-prog-dot');
    item.className = 're-prog-item' + (n === step ? ' active' : n < step ? ' done' : '');
    dot.className  = 're-prog-dot'  + (n === step ? ' active' : n < step ? ' done'  : '');
    dot.textContent = n < step ? '✓' : String(n);
  });
  [1,2].forEach(function(n) {
    var l = document.getElementById('pp-l' + n);
    if (l) l.className = 're-prog-line' + (n < step ? ' done' : '');
  });
}
function pShowStep(n) {
  [1,2,3].forEach(function(i) {
    document.getElementById('p' + i).style.display = i === n ? '' : 'none';
  });
}
function pNext1() {
  if (!validAccount('p-name','p-email','p-phone','p-pw','p-cf')) return;
  pSetProg(2); pShowStep(2); window.scrollTo(0,0);
}
function pBack2() { pSetProg(1); pShowStep(1); window.scrollTo(0,0); }
function pNext2() {
  var nin = gv('p-nin');
  if (nin && !/^\d{11}$/.test(nin)) { alert('NIN must be exactly 11 digits or leave it blank.'); document.getElementById('p-nin').focus(); return; }
  // Populate review
  document.getElementById('p-rv-name').textContent  = gv('p-name')  || '—';
  document.getElementById('p-rv-email').textContent = gv('p-email') || '—';
  document.getElementById('p-rv-phone').textContent = gv('p-phone_hidden') || gv('p-phone') || '—';
  document.getElementById('p-rv-dob').textContent    = fmtDate(gv('p-dob'));
  var gdr = document.getElementById('p-gender');
  document.getElementById('p-rv-gender').textContent = (gdr && gdr.value) ? selText('p-gender') : 'Not provided';
  var bl = document.getElementById('p-blood'); document.getElementById('p-rv-blood').textContent = (bl && bl.value) || 'Unknown';
  var gn = document.getElementById('p-geno');  document.getElementById('p-rv-geno').textContent  = (gn && gn.value)  || 'Unknown';
  var st = document.getElementById('p-state'); document.getElementById('p-rv-state').textContent = (st && st.value)  || 'Not provided';
  document.getElementById('p-rv-nin').textContent = nin ? nin.replace(/(\d{3})(\d{4})(\d{4})/, '$1-$2-$3') : 'Not provided';
  pSetProg(3); pShowStep(3); window.scrollTo(0,0);
}
function pBack3() { pSetProg(2); pShowStep(2); window.scrollTo(0,0); }

/* ══ PROVIDER ══════════════════════════════════════════ */
function prSetProg(step) {
  [1,2,3].forEach(function(n) {
    var item = document.getElementById('prp-' + n);
    var dot  = item.querySelector('.re-prog-dot');
    item.className = 're-prog-item' + (n === step ? ' active' : n < step ? ' done' : '');
    dot.className  = 're-prog-dot'  + (n === step ? ' active' : n < step ? ' done'  : '');
    dot.textContent = n < step ? '✓' : String(n);
  });
  [1,2].forEach(function(n) {
    var l = document.getElementById('prp-l' + n);
    if (l) l.className = 're-prog-line' + (n < step ? ' done' : '');
  });
}
function prShowStep(n) {
  [1,2,3].forEach(function(i) {
    document.getElementById('pr' + i).style.display = i === n ? '' : 'none';
  });
}
function prNext1() {
  if (!validAccount('pr-name','pr-email','pr-phone','pr-pw','pr-cf')) return;
  prSetProg(2); prShowStep(2); window.scrollTo(0,0);
}
function prBack2() { prSetProg(1); prShowStep(1); window.scrollTo(0,0); }
function prNext2() {
  if (!gv('pr-specialty')) { alert('Please select your specialty.'); document.getElementById('pr-specialty').focus(); return; }
  document.getElementById('pr-rv-name').textContent  = gv('pr-name')  || '—';
  document.getElementById('pr-rv-email').textContent = gv('pr-email') || '—';
  document.getElementById('pr-rv-phone').textContent = gv('pr-phone_hidden') || gv('pr-phone') || '—';
  document.getElementById('pr-rv-spec').textContent  = gv('pr-specialty') || '—';
  document.getElementById('pr-rv-folio').textContent = gv('pr-folio') || 'Not provided';
  document.getElementById('pr-rv-lic').textContent   = gv('pr-lic')   || 'Not provided';
  prSetProg(3); prShowStep(3); window.scrollTo(0,0);
}
function prBack3() { prSetProg(2); prShowStep(2); window.scrollTo(0,0); }

/* ══ ORG ═══════════════════════════════════════════════ */
function oSetProg(step) {
  [1,2,3].forEach(function(n) {
    var item = document.getElementById('op-' + n);
    var dot  = item.querySelector('.re-prog-dot');
    item.className = 're-prog-item' + (n === step ? ' active' : n < step ? ' done' : '');
    dot.className  = 're-prog-dot'  + (n === step ? ' active' : n < step ? ' done'  : '');
    dot.textContent = n < step ? '✓' : String(n);
  });
  [1,2].forEach(function(n) {
    var l = document.getElementById('op-l' + n);
    if (l) l.className = 're-prog-line' + (n < step ? ' done' : '');
  });
}
function oShowStep(n) {
  [1,2,3].forEach(function(i) {
    document.getElementById('o' + i).style.display = i === n ? '' : 'none';
  });
}
function oNext1() {
  if (!validAccount('o-name','o-email','o-phone','o-pw','o-cf')) return;
  oSetProg(2); oShowStep(2); window.scrollTo(0,0);
}
function oBack2() { oSetProg(1); oShowStep(1); window.scrollTo(0,0); }
function oNext2() {
  if (!gv('o-org-name')) { alert('Please enter the organisation name.'); document.getElementById('o-org-name').focus(); return; }
  var isPharm = (currentType === 'pharmacy');
  document.getElementById('o-rv-name').textContent  = gv('o-name')  || '—';
  document.getElementById('o-rv-email').textContent = gv('o-email') || '—';
  document.getElementById('o-rv-phone').textContent = gv('o-phone_hidden') || gv('o-phone') || '—';
  document.getElementById('o-rv-org').textContent   = gv('o-org-name') || '—';
  if (!isPharm) {
    document.getElementById('o-rv-type').textContent = selText('o-org-type') || '—';
  } else {
    document.getElementById('o-rv-type-row').style.display = 'none';
  }
  var addr = gv('o-addr'), city = gv('o-city');
  var st   = document.getElementById('o-state');
  document.getElementById('o-rv-lic').textContent   = gv('o-lic')   || 'Not provided';
  document.getElementById('o-rv-addr').textContent  = [addr,city].filter(Boolean).join(', ') || 'Not provided';
  document.getElementById('o-rv-state').textContent = (st && st.value) || 'Not provided';
  oSetProg(3); oShowStep(3); window.scrollTo(0,0);
}
function oBack3() { oSetProg(2); oShowStep(2); window.scrollTo(0,0); }

/* ── Submit guard ──────────────────────────────────────── */
document.getElementById('regForm').addEventListener('submit', function(e) {
  var type = document.getElementById('regType').value;
  var termId = {patient:'p-terms', provider:'pr-terms'}[type] || 'o-terms';
  var terms  = document.getElementById(termId);
  if (terms && !terms.checked) {
    e.preventDefault();
    alert('Please accept the Terms of Service to continue.');
    return;
  }
  var btnIds = {patient:'p-submit', provider:'pr-submit', hospital:'o-submit', clinic:'o-submit', pharmacy:'o-submit'};
  var btn = document.getElementById(btnIds[type]);
  if (btn) { btn.disabled = true; btn.textContent = 'Creating account…'; }
});

/* ── Re-open on server validation error ────────────────── */
@if($errors->any())
  @php $t = old('reg_type','patient'); @endphp
  (function() {
    var t = '{{ $t }}';
    if (t === 'provider') {
      pickType('provider');
      @if($errors->has('specialty') || $errors->has('license_no') || $errors->has('mdcn_folio'))
        prSetProg(2); prShowStep(2);
      @else
        prSetProg(1); prShowStep(1);
      @endif
    } else if (t === 'hospital' || t === 'clinic' || t === 'pharmacy') {
      pickType(t);
      @if($errors->has('org_name') || $errors->has('org_type') || $errors->has('org_license'))
        oSetProg(2); oShowStep(2);
      @else
        oSetProg(1); oShowStep(1);
      @endif
    } else {
      pickType('patient');
      @if($errors->has('dob') || $errors->has('gender') || $errors->has('blood_type') || $errors->has('nin'))
        pSetProg(2); pShowStep(2);
      @else
        pSetProg(1); pShowStep(1);
      @endif
    }
  })();
@endif
</script>

@endsection
