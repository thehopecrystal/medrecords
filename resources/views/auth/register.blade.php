@extends('layouts.auth')
@section('title', 'Create Account')
@section('content')

@php
$phoneCountries = [
  ['code'=>'+234','flag'=>'🇳🇬','name'=>'Nigeria',             'ph'=>'08012345678'],
  ['code'=>'+233','flag'=>'🇬🇭','name'=>'Ghana',               'ph'=>'0241234567'],
  ['code'=>'+254','flag'=>'🇰🇪','name'=>'Kenya',               'ph'=>'0712345678'],
  ['code'=>'+27', 'flag'=>'🇿🇦','name'=>'South Africa',        'ph'=>'0812345678'],
  ['code'=>'+251','flag'=>'🇪🇹','name'=>'Ethiopia',            'ph'=>'0912345678'],
  ['code'=>'+255','flag'=>'🇹🇿','name'=>'Tanzania',            'ph'=>'0712345678'],
  ['code'=>'+256','flag'=>'🇺🇬','name'=>'Uganda',              'ph'=>'0712345678'],
  ['code'=>'+237','flag'=>'🇨🇲','name'=>'Cameroon',            'ph'=>'671234567'],
  ['code'=>'+221','flag'=>'🇸🇳','name'=>'Senegal',             'ph'=>'701234567'],
  ['code'=>'+225','flag'=>'🇨🇮','name'=>"Côte d'Ivoire",       'ph'=>'0712345678'],
  ['code'=>'+20', 'flag'=>'🇪🇬','name'=>'Egypt',               'ph'=>'01012345678'],
  ['code'=>'+212','flag'=>'🇲🇦','name'=>'Morocco',             'ph'=>'0612345678'],
  ['code'=>'+44', 'flag'=>'🇬🇧','name'=>'United Kingdom',      'ph'=>'07911123456'],
  ['code'=>'+1',  'flag'=>'🇺🇸','name'=>'United States',       'ph'=>'2025551234'],
  ['code'=>'+1',  'flag'=>'🇨🇦','name'=>'Canada',              'ph'=>'4165551234'],
  ['code'=>'+33', 'flag'=>'🇫🇷','name'=>'France',              'ph'=>'0612345678'],
  ['code'=>'+49', 'flag'=>'🇩🇪','name'=>'Germany',             'ph'=>'01512345678'],
  ['code'=>'+39', 'flag'=>'🇮🇹','name'=>'Italy',               'ph'=>'3121234567'],
  ['code'=>'+31', 'flag'=>'🇳🇱','name'=>'Netherlands',         'ph'=>'0612345678'],
  ['code'=>'+353','flag'=>'🇮🇪','name'=>'Ireland',             'ph'=>'0871234567'],
  ['code'=>'+971','flag'=>'🇦🇪','name'=>'United Arab Emirates','ph'=>'501234567'],
  ['code'=>'+966','flag'=>'🇸🇦','name'=>'Saudi Arabia',        'ph'=>'501234567'],
  ['code'=>'+91', 'flag'=>'🇮🇳','name'=>'India',               'ph'=>'9876543210'],
  ['code'=>'+61', 'flag'=>'🇦🇺','name'=>'Australia',           'ph'=>'0412345678'],
];
@endphp

<style nonce="{{ $cspNonce ?? '' }}">
.auth-box { max-width: 480px; }

/* ── Visibility control (belt-and-suspenders) ── */
.rg-hide { display: none !important; }

/* ── Error banner ── */
.rg-error {
  background: #FEF2F2; border: 1px solid #FECACA;
  border-radius: 10px; padding: 12px 16px;
  font-size: 13px; color: #991B1B;
  margin-bottom: 20px; line-height: 1.6;
}
.rg-error div + div { margin-top: 3px; }

/* ── Type-selection screen ── */
.rg-title { font-size: 24px; font-weight: 800; color: #0F172A; letter-spacing: -0.03em; margin: 0 0 6px; }
.rg-desc  { font-size: 14px; color: #64748B; margin: 0 0 28px; line-height: 1.5; }

.rg-cards { display: flex; flex-direction: column; gap: 10px; margin-bottom: 28px; }
.rg-card {
  display: flex; align-items: center; gap: 16px;
  padding: 16px 20px;
  background: #fff; border: 2px solid #E2E8F0; border-radius: 14px;
  cursor: pointer; text-align: left; width: 100%; font-family: inherit;
  transition: border-color .15s, box-shadow .15s, background .15s;
}
.rg-card:hover { border-color: #1E3A5F; background: #FAFBFD; box-shadow: 0 2px 12px rgba(30,58,95,.08); }
.rg-card-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
.rg-card-body { flex: 1; }
.rg-card-name { font-size: 15px; font-weight: 700; color: #0F172A; display: block; margin-bottom: 2px; }
.rg-card-hint { font-size: 12px; color: #64748B; line-height: 1.4; display: block; }
.rg-card-arrow { font-size: 22px; color: #CBD5E1; flex-shrink: 0; transition: transform .15s, color .15s; }
.rg-card:hover .rg-card-arrow { transform: translateX(4px); color: #1E3A5F; }

/* ── Wizard wrapper ── */
.rg-back {
  display: inline-flex; align-items: center; gap: 6px;
  background: none; border: none; cursor: pointer;
  font-size: 13px; color: #94A3B8; padding: 0; margin-bottom: 20px;
  font-family: inherit; transition: color .15s;
}
.rg-back:hover { color: #1E3A5F; }

.rg-type-pill {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 4px 12px; border-radius: 99px;
  font-size: 12px; font-weight: 700; margin-bottom: 24px;
}

/* ── Progress ── */
.rg-progress { display: flex; align-items: center; margin-bottom: 32px; }
.rg-step-node { display: flex; flex-direction: column; align-items: center; gap: 6px; flex-shrink: 0; }
.rg-step-circle {
  width: 34px; height: 34px; border-radius: 50%;
  border: 2px solid #E2E8F0; background: #fff; color: #94A3B8;
  font-size: 13px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  transition: all .25s;
}
.rg-step-circle.is-active { border-color: #1E3A5F; background: #1E3A5F; color: #fff; }
.rg-step-circle.is-done   { border-color: #16A34A; background: #16A34A; color: #fff; }
.rg-step-name { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #94A3B8; white-space: nowrap; transition: color .2s; }
.rg-step-node.is-active .rg-step-name { color: #1E3A5F; }
.rg-step-node.is-done   .rg-step-name { color: #16A34A; }
.rg-step-line { flex: 1; height: 2px; background: #E2E8F0; margin: 0 6px; margin-bottom: 22px; transition: background .3s; }
.rg-step-line.is-done { background: #16A34A; }

/* ── Step heading ── */
.rg-step-title { font-size: 20px; font-weight: 800; color: #0F172A; letter-spacing: -.02em; margin: 0 0 4px; }
.rg-step-sub   { font-size: 13px; color: #64748B; margin: 0 0 24px; line-height: 1.5; }

/* ── Form fields ── */
.rg-field { margin-bottom: 16px; }
.rg-label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #475569; margin-bottom: 7px; }
.rg-req   { color: #DC2626; }
.rg-input {
  display: block; width: 100%; background: #fff;
  border: 1.5px solid #E2E8F0; border-radius: 9px;
  padding: 11px 14px; font-size: 14px; color: #0F172A;
  font-family: inherit; line-height: 1.4;
  transition: border-color .15s, box-shadow .15s;
}
.rg-input:focus { outline: none; border-color: #1E3A5F; box-shadow: 0 0 0 3px rgba(30,58,95,.08); }
.rg-input::placeholder { color: #CBD5E1; }
.rg-hint { display: block; font-size: 11px; color: #94A3B8; margin-top: 5px; }

/* ── Inline phone picker ── */
.rg-ph { position: relative; }
.rg-ph-row { display: flex; }
.rg-ph-btn {
  flex-shrink: 0; display: flex; align-items: center; gap: 5px;
  height: 44px; padding: 0 11px;
  background: #F8FAFC; border: 1.5px solid #E2E8F0;
  border-right: none; border-radius: 9px 0 0 9px;
  cursor: pointer; font-size: 13px; font-weight: 600;
  color: #374151; white-space: nowrap; font-family: inherit;
  line-height: 1; transition: background .15s;
}
.rg-ph-btn:hover { background: #EFF3F8; border-color: #CBD5E1; }
.rg-ph-flag { font-size: 19px; line-height: 1; }
.rg-ph-code { font-size: 12px; font-weight: 700; }
.rg-ph-local {
  flex: 1; min-width: 0; height: 44px;
  border: 1.5px solid #E2E8F0; border-left: none;
  border-radius: 0 9px 9px 0; padding: 0 14px;
  font-size: 14px; color: #0F172A; background: #fff;
  font-family: inherit; transition: border-color .15s, box-shadow .15s;
}
.rg-ph-local:focus { outline: none; border-color: #1E3A5F; box-shadow: 0 0 0 3px rgba(30,58,95,.08); z-index: 1; }
.rg-ph-local::placeholder { color: #CBD5E1; }
.rg-ph-drop {
  display: none; position: absolute; top: calc(100% + 4px); left: 0;
  width: 300px; max-width: calc(100vw - 24px);
  background: #fff; border: 1.5px solid #D1D5DB; border-radius: 10px;
  box-shadow: 0 8px 28px rgba(0,0,0,.14); z-index: 9999; overflow: hidden;
}
.rg-ph-drop.is-open { display: block; }
.rg-ph-swrap { padding: 8px; }
.rg-ph-search {
  display: block; width: 100%; padding: 7px 10px;
  border: 1.5px solid #E5E7EB; border-radius: 7px;
  font-size: 13px; font-family: inherit;
  background: #F9FAFB; box-sizing: border-box; outline: none;
}
.rg-ph-search:focus { border-color: #1E3A5F; background: #fff; }
.rg-ph-list { max-height: 230px; overflow-y: auto; }
.rg-ph-list::-webkit-scrollbar { width: 5px; }
.rg-ph-list::-webkit-scrollbar-thumb { background: #D1D5DB; border-radius: 5px; }
.rg-ph-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 13px; cursor: pointer; font-size: 13px;
  transition: background .1s; user-select: none;
}
.rg-ph-item:hover { background: #F0F4FF; }
.rg-ph-item.is-sel { background: #EFF6FF; }
.rg-ph-if { font-size: 19px; line-height: 1; flex-shrink: 0; }
.rg-ph-in { flex: 1; color: #1F2937; }
.rg-ph-ic { color: #6B7280; font-size: 12px; font-weight: 600; flex-shrink: 0; }

/* ── Password wrap ── */
.rg-pw-wrap { position: relative; }
.rg-pw-wrap .rg-input { padding-right: 46px; }
.rg-pw-eye {
  position: absolute; right: 0; top: 0; bottom: 0; width: 44px;
  display: flex; align-items: center; justify-content: center;
  background: none; border: none; cursor: pointer;
  font-size: 16px; color: #94A3B8; transition: color .15s;
}
.rg-pw-eye:hover { color: #475569; }
.rg-strength { height: 3px; background: #E2E8F0; border-radius: 3px; margin-top: 8px; overflow: hidden; }
.rg-strength-fill { height: 100%; width: 0; border-radius: 3px; transition: width .3s, background .3s; background: #DC2626; }

/* ── Two-column grid ── */
.rg-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

/* ── Info note ── */
.rg-note {
  display: flex; align-items: flex-start; gap: 10px;
  background: #EFF6FF; border-left: 3px solid #3B82F6;
  border-radius: 0 8px 8px 0; padding: 11px 14px;
  font-size: 12px; color: #1E40AF; line-height: 1.6;
  margin-bottom: 20px;
}
.rg-note-icon { font-size: 14px; flex-shrink: 0; margin-top: 1px; }

/* ── Buttons ── */
.rg-btn {
  display: block; width: 100%; padding: 13px 20px;
  border-radius: 10px; font-size: 15px; font-weight: 700;
  font-family: inherit; cursor: pointer; text-align: center;
  transition: background .15s, transform .1s; border: none; margin-top: 8px;
}
.rg-btn:active  { transform: scale(.99); }
.rg-btn:disabled { opacity: .6; cursor: not-allowed; }
.rg-btn-primary { background: #1E3A5F; color: #fff; }
.rg-btn-primary:hover { background: #163050; }
.rg-btn-success { background: #16A34A; color: #fff; }
.rg-btn-success:hover { background: #15803D; }
.rg-btn-ghost   { background: #F8FAFC; color: #475569; border: 1.5px solid #E2E8F0; }
.rg-btn-ghost:hover { background: #F1F5F9; }
.rg-btn-row { display: grid; grid-template-columns: auto 1fr; gap: 10px; margin-top: 8px; }
.rg-btn-row .rg-btn { margin-top: 0; }
.rg-btn-row .rg-btn-ghost { padding-left: 20px; padding-right: 20px; width: auto; }

/* ── Review card ── */
.rg-review { border: 1.5px solid #E2E8F0; border-radius: 12px; overflow: hidden; margin-bottom: 14px; }
.rg-review-head { background: #F8FAFC; padding: 8px 16px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #64748B; border-bottom: 1px solid #E2E8F0; }
.rg-review-row { display: flex; justify-content: space-between; align-items: baseline; padding: 9px 16px; font-size: 13px; border-bottom: 1px solid #F8FAFC; }
.rg-review-row:last-child { border-bottom: none; }
.rg-review-row span   { color: #64748B; font-size: 12px; flex-shrink: 0; margin-right: 12px; }
.rg-review-row strong { color: #0F172A; font-weight: 600; text-align: right; word-break: break-all; }

/* ── Terms ── */
.rg-terms { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: #475569; line-height: 1.6; margin: 16px 0 20px; cursor: default; }
.rg-terms-check { width: 16px; height: 16px; margin-top: 3px; flex-shrink: 0; accent-color: #1E3A5F; cursor: pointer; }
.rg-terms a { color: #1E3A5F; font-weight: 600; text-decoration: none; }
.rg-terms a:hover { text-decoration: underline; }

/* ── Footer ── */
.rg-footer { text-align: center; font-size: 13px; color: #64748B; margin-top: 22px; }
.rg-footer a { color: #1E3A5F; font-weight: 700; text-decoration: none; }
.rg-footer a:hover { text-decoration: underline; }

@media (max-width: 480px) {
  .rg-2col { grid-template-columns: 1fr; }
  .rg-btn-row { grid-template-columns: 1fr; }
  .rg-btn-row .rg-btn-ghost { width: 100%; }
}
</style>

<form method="POST" action="{{ route('register') }}" id="regForm" novalidate>
@csrf
<input type="hidden" name="phone" id="rg-phone-out">
<input type="hidden" name="reg_type" id="regType" value="{{ old('reg_type','') }}">

@if($errors->any())
  <div class="rg-error">
    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
  </div>
@endif

{{-- ═══════════════════════════════════════
     TYPE SELECTION
═══════════════════════════════════════ --}}
<div id="scr-type">
  <p class="rg-title">Create your account</p>
  <p class="rg-desc">Select how you'll use MedRecords to get started.</p>
  <div class="rg-cards">
    <button type="button" class="rg-card" id="btn-type-patient" data-type="patient">
      <span class="rg-card-icon" style="background:#EEF3FA;">👤</span>
      <span class="rg-card-body">
        <span class="rg-card-name">Patient</span>
        <span class="rg-card-hint">Manage your personal health record</span>
      </span>
      <span class="rg-card-arrow">›</span>
    </button>
    <button type="button" class="rg-card" id="btn-type-provider" data-type="provider">
      <span class="rg-card-icon" style="background:#ECFDF5;">🩺</span>
      <span class="rg-card-body">
        <span class="rg-card-name">Doctor / Provider</span>
        <span class="rg-card-hint">Register as a licensed clinical practitioner</span>
      </span>
      <span class="rg-card-arrow">›</span>
    </button>
    <button type="button" class="rg-card" id="btn-type-hospital" data-type="hospital">
      <span class="rg-card-icon" style="background:#FFF7ED;">🏥</span>
      <span class="rg-card-body">
        <span class="rg-card-name">Hospital / Clinic</span>
        <span class="rg-card-hint">Register your healthcare facility</span>
      </span>
      <span class="rg-card-arrow">›</span>
    </button>
    <button type="button" class="rg-card" id="btn-type-pharmacy" data-type="pharmacy">
      <span class="rg-card-icon" style="background:#F5F3FF;">💊</span>
      <span class="rg-card-body">
        <span class="rg-card-name">Pharmacy</span>
        <span class="rg-card-hint">Register your pharmacy organisation</span>
      </span>
      <span class="rg-card-arrow">›</span>
    </button>
  </div>
  <p class="rg-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
</div>

{{-- ═══════════════════════════════════════
     PATIENT WIZARD
═══════════════════════════════════════ --}}
<div id="scr-patient" class="rg-hide">

  <button type="button" class="rg-back" id="btn-back-pat">← Change account type</button>
  <div class="rg-type-pill" style="background:#EEF3FA;color:#1E3A5F;">👤 Patient</div>

  <div class="rg-progress" id="pat-prog">
    <div class="rg-step-node is-active"><div class="rg-step-circle is-active">1</div><div class="rg-step-name">Account</div></div>
    <div class="rg-step-line" id="pat-line-1"></div>
    <div class="rg-step-node"><div class="rg-step-circle">2</div><div class="rg-step-name">Health info</div></div>
    <div class="rg-step-line" id="pat-line-2"></div>
    <div class="rg-step-node"><div class="rg-step-circle">3</div><div class="rg-step-name">Review</div></div>
  </div>

  {{-- Step 1 --}}
  <div id="pat-s1">
    <p class="rg-step-title">Your account</p>
    <p class="rg-step-sub">Start with your basic details</p>

    <div class="rg-field">
      <label class="rg-label">Full name <span class="rg-req">*</span></label>
      <input class="rg-input" type="text" name="name" id="pat-name" value="{{ old('name') }}" placeholder="Adaeze Okonkwo" autocomplete="name">
    </div>
    <div class="rg-field">
      <label class="rg-label">Email address <span class="rg-req">*</span></label>
      <input class="rg-input" type="email" name="email" id="pat-email" value="{{ old('email') }}" placeholder="adaeze@gmail.com" autocomplete="email">
    </div>
    <div class="rg-field">
      <label class="rg-label">Phone number <span class="rg-req">*</span></label>
      <input type="hidden" name="_pat_phone" id="pat-ph-val" value="{{ old('phone','') }}">
      <div class="rg-ph" id="pat-ph">
        <div class="rg-ph-row">
          <button type="button" class="rg-ph-btn" data-code="+234">
            <span class="rg-ph-flag">🇳🇬</span>
            <span class="rg-ph-code">+234</span>
            <svg viewBox="0 0 10 6" width="9" height="6" aria-hidden="true"><path d="M0 0l5 6 5-6z" fill="currentColor" opacity=".45"/></svg>
          </button>
          <input type="tel" class="rg-ph-local" placeholder="08012345678" autocomplete="tel" inputmode="tel">
        </div>
        <div class="rg-ph-drop" id="pat-ph-drop">
          <div class="rg-ph-swrap"><input type="text" class="rg-ph-search" placeholder="Search country…" autocomplete="off" spellcheck="false"></div>
          <div class="rg-ph-list">
            @foreach($phoneCountries as $c)
            <div class="rg-ph-item{{ $c['code']==='+234'&&$c['name']==='Nigeria' ? ' is-sel' : '' }}"
                 data-code="{{ $c['code'] }}" data-flag="{{ $c['flag'] }}" data-name="{{ $c['name'] }}" data-ph="{{ $c['ph'] }}">
              <span class="rg-ph-if">{{ $c['flag'] }}</span><span class="rg-ph-in">{{ $c['name'] }}</span><span class="rg-ph-ic">{{ $c['code'] }}</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    <div class="rg-field">
      <label class="rg-label">Password <span class="rg-req">*</span></label>
      <div class="rg-pw-wrap">
        <input class="rg-input" type="password" name="password" id="pat-pw" placeholder="At least 8 characters" autocomplete="new-password">
        <button type="button" class="rg-pw-eye" id="btn-eye-pat-pw" data-target="pat-pw" data-icon="pat-pw-ic"><span id="pat-pw-ic">👁</span></button>
      </div>
      <div class="rg-strength"><div class="rg-strength-fill" id="pat-str"></div></div>
      <span class="rg-hint" id="pat-str-lbl">Enter a password</span>
    </div>
    <div class="rg-field">
      <label class="rg-label">Confirm password <span class="rg-req">*</span></label>
      <div class="rg-pw-wrap">
        <input class="rg-input" type="password" name="password_confirmation" id="pat-cf" placeholder="Repeat password" autocomplete="new-password">
        <button type="button" class="rg-pw-eye" id="btn-eye-pat-cf" data-target="pat-cf" data-icon="pat-cf-ic"><span id="pat-cf-ic">👁</span></button>
      </div>
      <span class="rg-hint" id="pat-match">&nbsp;</span>
    </div>
    <button type="button" class="rg-btn rg-btn-primary" id="btn-pat-s1-next">Continue →</button>
    <p class="rg-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>

  {{-- Step 2 --}}
  <div id="pat-s2" class="rg-hide">
    <p class="rg-step-title">Health profile</p>
    <p class="rg-step-sub">Optional — helps providers give you better care</p>

    <div class="rg-2col">
      <div class="rg-field">
        <label class="rg-label">Date of birth</label>
        <input class="rg-input" type="date" name="dob" id="pat-dob" value="{{ old('dob') }}" max="{{ date('Y-m-d', strtotime('-1 day')) }}">
      </div>
      <div class="rg-field">
        <label class="rg-label">Gender</label>
        <select class="rg-input" name="gender" id="pat-gender">
          <option value="">Select…</option>
          <option value="male"   {{ old('gender')==='male'  ?'selected':'' }}>Male</option>
          <option value="female" {{ old('gender')==='female'?'selected':'' }}>Female</option>
          <option value="other"  {{ old('gender')==='other' ?'selected':'' }}>Other</option>
        </select>
      </div>
    </div>
    <div class="rg-2col">
      <div class="rg-field">
        <label class="rg-label">Blood type</label>
        <select class="rg-input" name="blood_type" id="pat-blood">
          <option value="">Unknown</option>
          @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
            <option value="{{ $bt }}" {{ old('blood_type')===$bt?'selected':'' }}>{{ $bt }}</option>
          @endforeach
        </select>
      </div>
      <div class="rg-field">
        <label class="rg-label">Genotype</label>
        <select class="rg-input" name="genotype" id="pat-geno">
          <option value="">Unknown</option>
          @foreach(['AA','AS','SS','AC','SC'] as $g)
            <option value="{{ $g }}" {{ old('genotype')===$g?'selected':'' }}>{{ $g }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="rg-field">
      <label class="rg-label">State of residence</label>
      <select class="rg-input" name="state" id="pat-state">
        <option value="">Select state…</option>
        @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $st)
          <option value="{{ $st }}" {{ old('state')===$st?'selected':'' }}>{{ $st }}</option>
        @endforeach
      </select>
    </div>
    <div class="rg-field">
      <label class="rg-label">NIN — National Identity Number</label>
      <input class="rg-input" type="text" name="nin" id="pat-nin" value="{{ old('nin') }}" placeholder="12345678901" maxlength="11" inputmode="numeric">
      <span class="rg-hint">11 digits · optional</span>
    </div>
    <div class="rg-btn-row">
      <button type="button" class="rg-btn rg-btn-ghost" id="btn-pat-s2-back">← Back</button>
      <button type="button" class="rg-btn rg-btn-primary" id="btn-pat-s2-next">Review →</button>
    </div>
  </div>

  {{-- Step 3 --}}
  <div id="pat-s3" class="rg-hide">
    <p class="rg-step-title">Review & confirm</p>
    <p class="rg-step-sub">Check your details before creating your account</p>

    <div class="rg-review">
      <div class="rg-review-head">Account</div>
      <div class="rg-review-row"><span>Name</span><strong id="pat-rv-name">—</strong></div>
      <div class="rg-review-row"><span>Email</span><strong id="pat-rv-email">—</strong></div>
      <div class="rg-review-row"><span>Phone</span><strong id="pat-rv-phone">—</strong></div>
      <div class="rg-review-row"><span>Password</span><strong>••••••••</strong></div>
    </div>
    <div class="rg-review">
      <div class="rg-review-head">Health profile</div>
      <div class="rg-review-row"><span>Date of birth</span><strong id="pat-rv-dob">—</strong></div>
      <div class="rg-review-row"><span>Gender</span><strong id="pat-rv-gender">—</strong></div>
      <div class="rg-review-row"><span>Blood type</span><strong id="pat-rv-blood">—</strong></div>
      <div class="rg-review-row"><span>Genotype</span><strong id="pat-rv-geno">—</strong></div>
      <div class="rg-review-row"><span>State</span><strong id="pat-rv-state">—</strong></div>
      <div class="rg-review-row"><span>NIN</span><strong id="pat-rv-nin">—</strong></div>
    </div>
    <label class="rg-terms">
      <input type="checkbox" class="rg-terms-check" name="terms" id="pat-terms">
      <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. My health data will be stored securely and shared only with providers I explicitly authorise.</span>
    </label>
    <div class="rg-btn-row">
      <button type="button" class="rg-btn rg-btn-ghost" id="btn-pat-s3-back">← Back</button>
      <button type="submit" class="rg-btn rg-btn-success" id="pat-submit">Create account</button>
    </div>
    <p class="rg-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>

</div>{{-- /scr-patient --}}


{{-- ═══════════════════════════════════════
     PROVIDER WIZARD
═══════════════════════════════════════ --}}
<div id="scr-provider" class="rg-hide">

  <button type="button" class="rg-back" id="btn-back-prv">← Change account type</button>
  <div class="rg-type-pill" style="background:#ECFDF5;color:#166534;">🩺 Provider</div>

  <div class="rg-progress" id="prv-prog">
    <div class="rg-step-node is-active"><div class="rg-step-circle is-active">1</div><div class="rg-step-name">Account</div></div>
    <div class="rg-step-line" id="prv-line-1"></div>
    <div class="rg-step-node"><div class="rg-step-circle">2</div><div class="rg-step-name">Credentials</div></div>
    <div class="rg-step-line" id="prv-line-2"></div>
    <div class="rg-step-node"><div class="rg-step-circle">3</div><div class="rg-step-name">Review</div></div>
  </div>

  {{-- Step 1 --}}
  <div id="prv-s1">
    <p class="rg-step-title">Your account</p>
    <p class="rg-step-sub">Your professional provider profile</p>

    <div class="rg-field">
      <label class="rg-label">Full name <span class="rg-req">*</span></label>
      <input class="rg-input" type="text" name="name" id="prv-name" value="{{ old('name') }}" placeholder="Dr. Emeka Obi">
    </div>
    <div class="rg-field">
      <label class="rg-label">Email address <span class="rg-req">*</span></label>
      <input class="rg-input" type="email" name="email" id="prv-email" value="{{ old('email') }}" placeholder="emeka.obi@gmail.com">
    </div>
    <div class="rg-field">
      <label class="rg-label">Phone number <span class="rg-req">*</span></label>
      <input type="hidden" name="_prv_phone" id="prv-ph-val" value="{{ old('phone','') }}">
      <div class="rg-ph" id="prv-ph">
        <div class="rg-ph-row">
          <button type="button" class="rg-ph-btn" data-code="+234">
            <span class="rg-ph-flag">🇳🇬</span>
            <span class="rg-ph-code">+234</span>
            <svg viewBox="0 0 10 6" width="9" height="6" aria-hidden="true"><path d="M0 0l5 6 5-6z" fill="currentColor" opacity=".45"/></svg>
          </button>
          <input type="tel" class="rg-ph-local" placeholder="08012345678" autocomplete="tel" inputmode="tel">
        </div>
        <div class="rg-ph-drop" id="prv-ph-drop">
          <div class="rg-ph-swrap"><input type="text" class="rg-ph-search" placeholder="Search country…" autocomplete="off" spellcheck="false"></div>
          <div class="rg-ph-list">
            @foreach($phoneCountries as $c)
            <div class="rg-ph-item{{ $c['code']==='+234'&&$c['name']==='Nigeria' ? ' is-sel' : '' }}"
                 data-code="{{ $c['code'] }}" data-flag="{{ $c['flag'] }}" data-name="{{ $c['name'] }}" data-ph="{{ $c['ph'] }}">
              <span class="rg-ph-if">{{ $c['flag'] }}</span><span class="rg-ph-in">{{ $c['name'] }}</span><span class="rg-ph-ic">{{ $c['code'] }}</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    <div class="rg-field">
      <label class="rg-label">Password <span class="rg-req">*</span></label>
      <div class="rg-pw-wrap">
        <input class="rg-input" type="password" name="password" id="prv-pw" placeholder="At least 8 characters" autocomplete="new-password">
        <button type="button" class="rg-pw-eye" data-target="prv-pw" data-icon="prv-pw-ic"><span id="prv-pw-ic">👁</span></button>
      </div>
      <div class="rg-strength"><div class="rg-strength-fill" id="prv-str"></div></div>
      <span class="rg-hint" id="prv-str-lbl">Enter a password</span>
    </div>
    <div class="rg-field">
      <label class="rg-label">Confirm password <span class="rg-req">*</span></label>
      <div class="rg-pw-wrap">
        <input class="rg-input" type="password" name="password_confirmation" id="prv-cf" placeholder="Repeat password" autocomplete="new-password">
        <button type="button" class="rg-pw-eye" data-target="prv-cf" data-icon="prv-cf-ic"><span id="prv-cf-ic">👁</span></button>
      </div>
      <span class="rg-hint" id="prv-match">&nbsp;</span>
    </div>
    <button type="button" class="rg-btn rg-btn-primary" id="btn-prv-s1-next">Continue →</button>
    <p class="rg-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>

  {{-- Step 2 --}}
  <div id="prv-s2" class="rg-hide">
    <p class="rg-step-title">Clinical credentials</p>
    <p class="rg-step-sub">Your professional details</p>

    <div class="rg-field">
      <label class="rg-label">Specialty <span class="rg-req">*</span></label>
      <select class="rg-input" name="specialty" id="prv-specialty">
        <option value="">Select specialty…</option>
        @foreach(['General Practice','Internal Medicine','Paediatrics','Obstetrics & Gynaecology','Surgery','Cardiology','Neurology','Psychiatry','Dermatology','Radiology','Anaesthesia','Ophthalmology','ENT','Orthopaedics','Urology','Nephrology','Endocrinology','Gastroenterology','Oncology','Emergency Medicine','Family Medicine','Community Medicine','Pathology','Physiotherapy','Dentistry','Nursing','Pharmacy','Medical Laboratory Science','Radiography','Other'] as $sp)
          <option value="{{ $sp }}" {{ old('specialty')===$sp?'selected':'' }}>{{ $sp }}</option>
        @endforeach
      </select>
    </div>
    <div class="rg-field">
      <label class="rg-label">MDCN Folio Number</label>
      <input class="rg-input" type="text" name="mdcn_folio" id="prv-folio" value="{{ old('mdcn_folio') }}" placeholder="MDCN/2018/12345">
      <span class="rg-hint">Medical and Dental Council of Nigeria registration number</span>
    </div>
    <div class="rg-field">
      <label class="rg-label">Professional Licence No.</label>
      <input class="rg-input" type="text" name="license_no" id="prv-lic" value="{{ old('license_no') }}" placeholder="FMC/2019/00456">
    </div>
    <div class="rg-note">
      <span class="rg-note-icon">ℹ️</span>
      <span>A facility administrator will add you to their team after registration, or you can operate independently.</span>
    </div>
    <div class="rg-btn-row">
      <button type="button" class="rg-btn rg-btn-ghost" id="btn-prv-s2-back">← Back</button>
      <button type="button" class="rg-btn rg-btn-primary" id="btn-prv-s2-next">Review →</button>
    </div>
  </div>

  {{-- Step 3 --}}
  <div id="prv-s3" class="rg-hide">
    <p class="rg-step-title">Review & confirm</p>
    <p class="rg-step-sub">Check your details before creating your account</p>

    <div class="rg-review">
      <div class="rg-review-head">Account</div>
      <div class="rg-review-row"><span>Name</span><strong id="prv-rv-name">—</strong></div>
      <div class="rg-review-row"><span>Email</span><strong id="prv-rv-email">—</strong></div>
      <div class="rg-review-row"><span>Phone</span><strong id="prv-rv-phone">—</strong></div>
    </div>
    <div class="rg-review">
      <div class="rg-review-head">Credentials</div>
      <div class="rg-review-row"><span>Specialty</span><strong id="prv-rv-spec">—</strong></div>
      <div class="rg-review-row"><span>MDCN Folio</span><strong id="prv-rv-folio">—</strong></div>
      <div class="rg-review-row"><span>Licence No.</span><strong id="prv-rv-lic">—</strong></div>
    </div>
    <label class="rg-terms">
      <input type="checkbox" class="rg-terms-check" name="terms" id="prv-terms">
      <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. I confirm my credentials are accurate.</span>
    </label>
    <div class="rg-btn-row">
      <button type="button" class="rg-btn rg-btn-ghost" id="btn-prv-s3-back">← Back</button>
      <button type="submit" class="rg-btn rg-btn-success" id="prv-submit">Create account</button>
    </div>
    <p class="rg-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>

</div>{{-- /scr-provider --}}


{{-- ═══════════════════════════════════════
     ORG WIZARD  (hospital / clinic / pharmacy)
═══════════════════════════════════════ --}}
<div id="scr-org" class="rg-hide">

  <button type="button" class="rg-back" id="btn-back-org">← Change account type</button>
  <div class="rg-type-pill" id="org-pill" style="background:#FFF7ED;color:#92400E;">🏥 Organisation</div>

  <div class="rg-progress" id="org-prog">
    <div class="rg-step-node is-active"><div class="rg-step-circle is-active">1</div><div class="rg-step-name">Admin</div></div>
    <div class="rg-step-line" id="org-line-1"></div>
    <div class="rg-step-node"><div class="rg-step-circle">2</div><div class="rg-step-name" id="org-step2-lbl">Organisation</div></div>
    <div class="rg-step-line" id="org-line-2"></div>
    <div class="rg-step-node"><div class="rg-step-circle">3</div><div class="rg-step-name">Review</div></div>
  </div>

  {{-- Step 1 --}}
  <div id="org-s1">
    <p class="rg-step-title" id="org-title1">Register your organisation</p>
    <p class="rg-step-sub">First, create your administrator account</p>

    <div class="rg-field">
      <label class="rg-label">Your full name <span class="rg-req">*</span></label>
      <input class="rg-input" type="text" name="name" id="org-name" value="{{ old('name') }}" placeholder="Chidi Nwosu">
    </div>
    <div class="rg-field">
      <label class="rg-label">Your email address <span class="rg-req">*</span></label>
      <input class="rg-input" type="email" name="email" id="org-email" value="{{ old('email') }}" placeholder="admin@yourhospital.ng">
    </div>
    <div class="rg-field">
      <label class="rg-label">Your phone number <span class="rg-req">*</span></label>
      <input type="hidden" name="_org_phone" id="org-ph-val" value="{{ old('phone','') }}">
      <div class="rg-ph" id="org-ph">
        <div class="rg-ph-row">
          <button type="button" class="rg-ph-btn" data-code="+234">
            <span class="rg-ph-flag">🇳🇬</span>
            <span class="rg-ph-code">+234</span>
            <svg viewBox="0 0 10 6" width="9" height="6" aria-hidden="true"><path d="M0 0l5 6 5-6z" fill="currentColor" opacity=".45"/></svg>
          </button>
          <input type="tel" class="rg-ph-local" placeholder="08012345678" autocomplete="tel" inputmode="tel">
        </div>
        <div class="rg-ph-drop" id="org-ph-drop">
          <div class="rg-ph-swrap"><input type="text" class="rg-ph-search" placeholder="Search country…" autocomplete="off" spellcheck="false"></div>
          <div class="rg-ph-list">
            @foreach($phoneCountries as $c)
            <div class="rg-ph-item{{ $c['code']==='+234'&&$c['name']==='Nigeria' ? ' is-sel' : '' }}"
                 data-code="{{ $c['code'] }}" data-flag="{{ $c['flag'] }}" data-name="{{ $c['name'] }}" data-ph="{{ $c['ph'] }}">
              <span class="rg-ph-if">{{ $c['flag'] }}</span><span class="rg-ph-in">{{ $c['name'] }}</span><span class="rg-ph-ic">{{ $c['code'] }}</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    <div class="rg-field">
      <label class="rg-label">Password <span class="rg-req">*</span></label>
      <div class="rg-pw-wrap">
        <input class="rg-input" type="password" name="password" id="org-pw" placeholder="At least 8 characters" autocomplete="new-password">
        <button type="button" class="rg-pw-eye" data-target="org-pw" data-icon="org-pw-ic"><span id="org-pw-ic">👁</span></button>
      </div>
      <div class="rg-strength"><div class="rg-strength-fill" id="org-str"></div></div>
      <span class="rg-hint" id="org-str-lbl">Enter a password</span>
    </div>
    <div class="rg-field">
      <label class="rg-label">Confirm password <span class="rg-req">*</span></label>
      <div class="rg-pw-wrap">
        <input class="rg-input" type="password" name="password_confirmation" id="org-cf" placeholder="Repeat password" autocomplete="new-password">
        <button type="button" class="rg-pw-eye" data-target="org-cf" data-icon="org-cf-ic"><span id="org-cf-ic">👁</span></button>
      </div>
      <span class="rg-hint" id="org-match">&nbsp;</span>
    </div>
    <button type="button" class="rg-btn rg-btn-primary" id="btn-org-s1-next">Continue →</button>
    <p class="rg-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>

  {{-- Step 2 --}}
  <div id="org-s2" class="rg-hide">
    <p class="rg-step-title" id="org-title2">Organisation details</p>
    <p class="rg-step-sub">Tell us about your facility</p>

    <div class="rg-field">
      <label class="rg-label" id="org-name-lbl">Organisation name <span class="rg-req">*</span></label>
      <input class="rg-input" type="text" name="org_name" id="org-org-name" value="{{ old('org_name') }}" placeholder="Lagos General Hospital">
    </div>
    <div class="rg-field" id="org-type-wrap">
      <label class="rg-label">Facility type <span class="rg-req">*</span></label>
      <select class="rg-input" name="org_type" id="org-org-type">
        <option value="hospital" {{ old('org_type','hospital')==='hospital'?'selected':'' }}>Hospital</option>
        <option value="clinic"   {{ old('org_type')==='clinic' ?'selected':'' }}>Clinic</option>
        <option value="lab"      {{ old('org_type')==='lab'    ?'selected':'' }}>Diagnostic Lab</option>
        <option value="dental"   {{ old('org_type')==='dental' ?'selected':'' }}>Dental Practice</option>
        <option value="optical"  {{ old('org_type')==='optical'?'selected':'' }}>Optical / Eye Care</option>
      </select>
    </div>
    <div class="rg-field">
      <label class="rg-label" id="org-lic-lbl">Licence / Registration No.</label>
      <input class="rg-input" type="text" name="org_license" id="org-lic" value="{{ old('org_license') }}" placeholder="MOH/2020/LAG-001">
    </div>
    <div class="rg-field">
      <label class="rg-label">Street address</label>
      <input class="rg-input" type="text" name="org_address" id="org-addr" value="{{ old('org_address') }}" placeholder="14 Broad Street">
    </div>
    <div class="rg-2col">
      <div class="rg-field">
        <label class="rg-label">City</label>
        <input class="rg-input" type="text" name="org_city" id="org-city" value="{{ old('org_city') }}" placeholder="Lagos">
      </div>
      <div class="rg-field">
        <label class="rg-label">State</label>
        <select class="rg-input" name="org_state" id="org-state">
          <option value="">Select…</option>
          @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $st)
            <option value="{{ $st }}" {{ old('org_state')===$st?'selected':'' }}>{{ $st }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="rg-2col">
      <div class="rg-field">
        <label class="rg-label">Organisation phone</label>
        <input class="rg-input" type="tel" name="org_phone" id="org-org-phone" value="{{ old('org_phone') }}" placeholder="+2341234567890">
      </div>
      <div class="rg-field">
        <label class="rg-label">Organisation email</label>
        <input class="rg-input" type="email" name="org_email" id="org-org-email" value="{{ old('org_email') }}" placeholder="info@yourhospital.ng">
      </div>
    </div>
    <div class="rg-btn-row">
      <button type="button" class="rg-btn rg-btn-ghost" id="btn-org-s2-back">← Back</button>
      <button type="button" class="rg-btn rg-btn-primary" id="btn-org-s2-next">Review →</button>
    </div>
  </div>

  {{-- Step 3 --}}
  <div id="org-s3" class="rg-hide">
    <p class="rg-step-title">Review & confirm</p>
    <p class="rg-step-sub">Check everything before submitting</p>

    <div class="rg-review">
      <div class="rg-review-head">Administrator account</div>
      <div class="rg-review-row"><span>Name</span><strong id="org-rv-name">—</strong></div>
      <div class="rg-review-row"><span>Email</span><strong id="org-rv-email">—</strong></div>
      <div class="rg-review-row"><span>Phone</span><strong id="org-rv-phone">—</strong></div>
    </div>
    <div class="rg-review">
      <div class="rg-review-head" id="org-rv-head">Organisation</div>
      <div class="rg-review-row"><span>Name</span><strong id="org-rv-org">—</strong></div>
      <div class="rg-review-row" id="org-rv-type-row"><span>Type</span><strong id="org-rv-type">—</strong></div>
      <div class="rg-review-row"><span>Licence No.</span><strong id="org-rv-lic">—</strong></div>
      <div class="rg-review-row"><span>Address</span><strong id="org-rv-addr">—</strong></div>
      <div class="rg-review-row"><span>State</span><strong id="org-rv-state">—</strong></div>
    </div>
    <label class="rg-terms">
      <input type="checkbox" class="rg-terms-check" name="terms" id="org-terms">
      <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. I confirm the organisation details are accurate.</span>
    </label>
    <div class="rg-btn-row">
      <button type="button" class="rg-btn rg-btn-ghost" id="btn-org-s3-back">← Back</button>
      <button type="submit" class="rg-btn rg-btn-success" id="org-submit">Register organisation</button>
    </div>
    <p class="rg-footer">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
  </div>

</div>{{-- /scr-org --}}
</form>

<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function () {

/* ── Helpers ── */
function H(id) { return document.getElementById(id); }
function show(el) { if (el) el.classList.remove('rg-hide'); }
function hide(el) { if (el) el.classList.add('rg-hide'); }
function gv(id)   { var e = H(id); return e ? e.value.trim() : ''; }
function selText(id) { var e = H(id); return (e && e.selectedIndex >= 0) ? e.options[e.selectedIndex].text : ''; }
function fmtDate(s) {
  if (!s) return 'Not provided';
  var d = new Date(s + 'T12:00:00');
  return isNaN(d) ? s : d.toLocaleDateString('en-NG', { day:'numeric', month:'long', year:'numeric' });
}

/* ── Screen management ── */
var SCREENS     = ['scr-type','scr-patient','scr-provider','scr-org'];
var currentType = '';

function showScreen(id) {
  SCREENS.forEach(function(s) {
    var el = H(s);
    if (s === id) show(el); else hide(el);
  });
  window.scrollTo(0, 0);
}

function chooseType(type) {
  currentType = type;
  H('regType').value = type;
  if (type === 'patient') {
    showScreen('scr-patient');
    progSet('pat', 1); stepShow('pat', 1);
  } else if (type === 'provider') {
    showScreen('scr-provider');
    progSet('prv', 1); stepShow('prv', 1);
  } else {
    configOrg(type);
    showScreen('scr-org');
    progSet('org', 1); stepShow('org', 1);
  }
};

function goBack() {
  currentType = '';
  H('regType').value = '';
  showScreen('scr-type');
}

/* ── Progress bar ── */
function progSet(prefix, active) {
  var prog = H(prefix + '-prog');
  if (!prog) return;
  var nodes  = prog.querySelectorAll('.rg-step-node');
  var lines  = prog.querySelectorAll('.rg-step-line');
  nodes.forEach(function(node, i) {
    var step   = i + 1;
    var circle = node.querySelector('.rg-step-circle');
    var name   = node.querySelector('.rg-step-name');
    node.className   = 'rg-step-node'   + (step < active ? ' is-done' : step === active ? ' is-active' : '');
    circle.className = 'rg-step-circle' + (step < active ? ' is-done' : step === active ? ' is-active' : '');
    circle.textContent = step < active ? '✓' : String(step);
  });
  lines.forEach(function(line, i) {
    line.className = 'rg-step-line' + (i + 1 < active ? ' is-done' : '');
  });
}

/* ── Step show/hide using rg-hide class ── */
function stepShow(prefix, n) {
  [1, 2, 3].forEach(function(i) {
    var el = H(prefix + '-s' + i);
    if (!el) return;
    if (i === n) show(el); else hide(el);
  });
  window.scrollTo(0, 0);
}

/* ── Org config ── */
function configOrg(type) {
  var isPharm = (type === 'pharmacy');
  var pill = H('org-pill');
  if (isPharm) {
    pill.style.background = '#F5F3FF'; pill.style.color = '#6D28D9';
    pill.textContent = '💊 Pharmacy';
  } else {
    pill.style.background = '#FFF7ED'; pill.style.color = '#92400E';
    pill.textContent = '🏥 Organisation';
  }
  H('org-title1').textContent    = isPharm ? 'Register your pharmacy'  : 'Register your organisation';
  H('org-title2').textContent    = isPharm ? 'Pharmacy details'         : 'Organisation details';
  H('org-step2-lbl').textContent = isPharm ? 'Pharmacy'                 : 'Organisation';
  H('org-name-lbl').innerHTML    = (isPharm ? 'Pharmacy name' : 'Organisation name') + ' <span class="rg-req">*</span>';
  H('org-org-name').placeholder  = isPharm ? 'Wellness Pharmacy Ltd'   : 'Lagos General Hospital';
  H('org-lic-lbl').textContent   = isPharm ? 'PCN Licence No.'         : 'Licence / Registration No.';
  H('org-lic').placeholder       = isPharm ? 'PCN/2020/LAG-001'        : 'MOH/2020/LAG-001';
  H('org-rv-head').textContent   = isPharm ? 'Pharmacy'                 : 'Organisation';

  var typeWrap = H('org-type-wrap');
  var hi       = H('org-hidden-type');
  if (isPharm) {
    hide(typeWrap);
    H('org-rv-type-row').style.display = 'none';
    if (!hi) {
      hi = document.createElement('input');
      hi.type = 'hidden'; hi.name = 'org_type'; hi.id = 'org-hidden-type'; hi.value = 'pharmacy';
      typeWrap.parentNode.insertBefore(hi, typeWrap);
    }
  } else {
    show(typeWrap);
    H('org-rv-type-row').style.display = '';
    if (hi) hi.remove();
  }
}

/* ── Utils ── */
function togglePw(inputId, iconId) {
  var el = H(inputId), ic = H(iconId);
  if (!el || !ic) return;
  el.type = (el.type === 'password') ? 'text' : 'password';
  ic.textContent = (el.type === 'password') ? '👁' : '🙈';
}

function validateAccount(nameId, emailId, phoneHiddenId, pwId, cfId) {
  if (!gv(nameId)) { alert('Please enter your full name.'); H(nameId).focus(); return false; }
  if (!gv(emailId) || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(gv(emailId))) {
    alert('Please enter a valid email address.'); H(emailId).focus(); return false;
  }
  var ph = gv(phoneHiddenId);
  if (!ph || !/^\+[1-9]\d{6,14}$/.test(ph)) {
    alert('Please enter a valid phone number.'); return false;
  }
  if (gv(pwId).length < 8) { alert('Password must be at least 8 characters.'); H(pwId).focus(); return false; }
  if (gv(pwId) !== gv(cfId)) { alert('Passwords do not match.'); H(cfId).focus(); return false; }
  return true;
}

function bindStrength(pwId, fillId, lblId) {
  var el = H(pwId); if (!el) return;
  el.addEventListener('input', function() {
    var v = this.value, s = 0;
    if (v.length >= 8)  s++;
    if (v.length >= 12) s++;
    if (/[A-Z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    var fill = H(fillId), lbl = H(lblId);
    var w = ['0%','20%','40%','60%','80%','100%'];
    var c = ['#ddd','#DC2626','#D97706','#CA8A04','#16A34A','#15803D'];
    var l = ['Enter a password','Too short','Weak','Fair','Good','Strong ✓'];
    fill.style.width = w[s]; fill.style.background = c[s];
    lbl.textContent = l[s]; lbl.style.color = s >= 4 ? '#16A34A' : '#94A3B8';
  });
}

function bindMatch(pwId, cfId, hintId) {
  function check() {
    var hint = H(hintId); if (!hint) return;
    var cf = gv(cfId);
    if (!cf) { hint.innerHTML = '&nbsp;'; hint.style.color = ''; return; }
    var ok = (gv(pwId) === cf);
    hint.textContent = ok ? '✓ Passwords match' : '✗ Passwords do not match';
    hint.style.color = ok ? '#16A34A' : '#DC2626';
  }
  var pw = H(pwId), cf = H(cfId);
  if (pw) pw.addEventListener('input', check);
  if (cf) cf.addEventListener('input', check);
}

/* ── Inline phone pickers ── */
function initPh(wrapId, valId) {
  var wrap = H(wrapId); if (!wrap) return;
  var val    = H(valId);
  var btn    = wrap.querySelector('.rg-ph-btn');
  var local  = wrap.querySelector('.rg-ph-local');
  var drop   = wrap.querySelector('.rg-ph-drop');
  var search = wrap.querySelector('.rg-ph-search');
  var list   = wrap.querySelector('.rg-ph-list');
  var code   = btn ? btn.dataset.code : '+234';

  // Build code-map sorted longest-first so +1876 matches before +1
  var codeMap = (function() {
    var entries = [];
    list.querySelectorAll('.rg-ph-item').forEach(function(item) {
      entries.push({ code: item.dataset.code, item: item });
    });
    entries.sort(function(a, b) { return b.code.length - a.code.length; });
    return entries;
  })();

  // Find the first entry whose code is a prefix of raw (raw must start with '+')
  function detectCode(raw) {
    for (var i = 0; i < codeMap.length; i++) {
      if (raw.indexOf(codeMap[i].code) === 0) return codeMap[i];
    }
    return null;
  }

  // Update button + list to reflect a matched country
  function applyCountry(entry) {
    code = entry.code;
    btn.querySelector('.rg-ph-flag').textContent = entry.item.dataset.flag;
    btn.querySelector('.rg-ph-code').textContent = entry.code;
    local.placeholder = entry.item.dataset.ph;
    list.querySelectorAll('.rg-ph-item').forEach(function(i) { i.classList.remove('is-sel'); });
    entry.item.classList.add('is-sel');
  }

  function syncVal() {
    var raw = local.value;
    // Auto-detect country when user types or pastes a +code prefix
    if (raw && raw[0] === '+') {
      var match = detectCode(raw);
      if (match) {
        applyCountry(match);
        // Strip the code from the visible input, keep only the local digits
        // Setting .value programmatically does NOT re-fire the input event
        var rest = raw.slice(match.code.length).replace(/^\s+/, '');
        local.value = rest;
        raw = rest;
      }
    }
    var n = raw.replace(/\D/g, '');
    if (n && n[0] === '0') n = n.slice(1);
    if (val) val.value = n ? code + n : '';
  }

  function closeDrop() { drop.classList.remove('is-open'); }
  function openDrop() {
    document.querySelectorAll('.rg-ph-drop.is-open').forEach(function(d) { d.classList.remove('is-open'); });
    drop.classList.add('is-open');
    search.value = ''; filterItems('');
    search.focus();
  }
  function filterItems(q) {
    var lq = q.toLowerCase().trim();
    list.querySelectorAll('.rg-ph-item').forEach(function(item) {
      item.style.display = (!lq
        || item.dataset.name.toLowerCase().includes(lq)
        || item.dataset.code.includes(lq)) ? '' : 'none';
    });
  }
  btn.addEventListener('click', function(e) {
    e.preventDefault(); e.stopPropagation();
    if (drop.classList.contains('is-open')) closeDrop(); else openDrop();
  });
  list.addEventListener('click', function(e) {
    var item = e.target.closest('.rg-ph-item');
    if (!item) return;
    code = item.dataset.code;
    btn.querySelector('.rg-ph-flag').textContent = item.dataset.flag;
    btn.querySelector('.rg-ph-code').textContent = item.dataset.code;
    local.placeholder = item.dataset.ph;
    list.querySelectorAll('.rg-ph-item').forEach(function(i) { i.classList.remove('is-sel'); });
    item.classList.add('is-sel');
    closeDrop(); syncVal(); local.focus();
  });
  search.addEventListener('input', function() { filterItems(this.value); });
  local.addEventListener('input', syncVal);
  syncVal();
}
initPh('pat-ph','pat-ph-val');
initPh('prv-ph','prv-ph-val');
initPh('org-ph','org-ph-val');
document.addEventListener('click', function(e) {
  if (!e.target.closest('.rg-ph')) {
    document.querySelectorAll('.rg-ph-drop.is-open').forEach(function(d) { d.classList.remove('is-open'); });
  }
});

/* ── Strength + match ── */
bindStrength('pat-pw','pat-str','pat-str-lbl'); bindMatch('pat-pw','pat-cf','pat-match');
bindStrength('prv-pw','prv-str','prv-str-lbl'); bindMatch('prv-pw','prv-cf','prv-match');
bindStrength('org-pw','org-str','org-str-lbl'); bindMatch('org-pw','org-cf','org-match');

/* ── Type-selection cards ── */
['patient','provider','hospital','pharmacy'].forEach(function(t) {
  var btn = H('btn-type-' + t);
  if (btn) btn.addEventListener('click', function() { chooseType(t); });
});

/* ── Back-to-types buttons ── */
['pat','prv','org'].forEach(function(p) {
  var btn = H('btn-back-' + p);
  if (btn) btn.addEventListener('click', goBack);
});

/* ── Password eye-toggle buttons ── */
document.querySelectorAll('.rg-pw-eye[data-target]').forEach(function(btn) {
  btn.addEventListener('click', function() {
    togglePw(btn.getAttribute('data-target'), btn.getAttribute('data-icon'));
  });
});

/* ── Patient step navigation ── */
function patStep1Next() {
  if (!validateAccount('pat-name','pat-email','pat-ph-val','pat-pw','pat-cf')) return;
  progSet('pat', 2); stepShow('pat', 2);
}
function patStep2Back() { progSet('pat', 1); stepShow('pat', 1); }
function patStep2Next() {
  var nin = gv('pat-nin');
  if (nin && !/^\d{11}$/.test(nin)) { alert('NIN must be exactly 11 digits, or leave it blank.'); H('pat-nin').focus(); return; }
  H('pat-rv-name').textContent   = gv('pat-name')  || '—';
  H('pat-rv-email').textContent  = gv('pat-email') || '—';
  H('pat-rv-phone').textContent  = gv('pat-ph-val') || '—';
  H('pat-rv-dob').textContent    = fmtDate(gv('pat-dob'));
  var gender = H('pat-gender'), blood = H('pat-blood'), geno = H('pat-geno'), state = H('pat-state');
  H('pat-rv-gender').textContent = (gender && gender.value) ? selText('pat-gender') : 'Not provided';
  H('pat-rv-blood').textContent  = (blood  && blood.value)  ? blood.value  : 'Unknown';
  H('pat-rv-geno').textContent   = (geno   && geno.value)   ? geno.value   : 'Unknown';
  H('pat-rv-state').textContent  = (state  && state.value)  ? state.value  : 'Not provided';
  H('pat-rv-nin').textContent    = nin ? nin.replace(/(\d{3})(\d{4})(\d{4})/, '$1-$2-$3') : 'Not provided';
  progSet('pat', 3); stepShow('pat', 3);
}
function patStep3Back() { progSet('pat', 2); stepShow('pat', 2); }

H('btn-pat-s1-next').addEventListener('click', patStep1Next);
H('btn-pat-s2-back').addEventListener('click', patStep2Back);
H('btn-pat-s2-next').addEventListener('click', patStep2Next);
H('btn-pat-s3-back').addEventListener('click', patStep3Back);

/* ── Provider step navigation ── */
function prvStep1Next() {
  if (!validateAccount('prv-name','prv-email','prv-ph-val','prv-pw','prv-cf')) return;
  progSet('prv', 2); stepShow('prv', 2);
}
function prvStep2Back() { progSet('prv', 1); stepShow('prv', 1); }
function prvStep2Next() {
  if (!gv('prv-specialty')) { alert('Please select your specialty.'); H('prv-specialty').focus(); return; }
  H('prv-rv-name').textContent  = gv('prv-name')  || '—';
  H('prv-rv-email').textContent = gv('prv-email') || '—';
  H('prv-rv-phone').textContent = gv('prv-ph-val') || '—';
  H('prv-rv-spec').textContent  = gv('prv-specialty') || '—';
  H('prv-rv-folio').textContent = gv('prv-folio') || 'Not provided';
  H('prv-rv-lic').textContent   = gv('prv-lic')   || 'Not provided';
  progSet('prv', 3); stepShow('prv', 3);
}
function prvStep3Back() { progSet('prv', 2); stepShow('prv', 2); }

H('btn-prv-s1-next').addEventListener('click', prvStep1Next);
H('btn-prv-s2-back').addEventListener('click', prvStep2Back);
H('btn-prv-s2-next').addEventListener('click', prvStep2Next);
H('btn-prv-s3-back').addEventListener('click', prvStep3Back);

/* ── Org step navigation ── */
function orgStep1Next() {
  if (!validateAccount('org-name','org-email','org-ph-val','org-pw','org-cf')) return;
  progSet('org', 2); stepShow('org', 2);
}
function orgStep2Back() { progSet('org', 1); stepShow('org', 1); }
function orgStep2Next() {
  if (!gv('org-org-name')) { alert('Please enter the organisation name.'); H('org-org-name').focus(); return; }
  var isPharm = (currentType === 'pharmacy');
  var addr = gv('org-addr'), city = gv('org-city'), state = H('org-state');
  H('org-rv-name').textContent  = gv('org-name')  || '—';
  H('org-rv-email').textContent = gv('org-email') || '—';
  H('org-rv-phone').textContent = gv('org-ph-val') || '—';
  H('org-rv-org').textContent   = gv('org-org-name') || '—';
  if (!isPharm) H('org-rv-type').textContent = selText('org-org-type') || '—';
  H('org-rv-lic').textContent   = gv('org-lic')  || 'Not provided';
  H('org-rv-addr').textContent  = [addr, city].filter(Boolean).join(', ') || 'Not provided';
  H('org-rv-state').textContent = (state && state.value) || 'Not provided';
  progSet('org', 3); stepShow('org', 3);
}
function orgStep3Back() { progSet('org', 2); stepShow('org', 2); }

H('btn-org-s1-next').addEventListener('click', orgStep1Next);
H('btn-org-s2-back').addEventListener('click', orgStep2Back);
H('btn-org-s2-next').addEventListener('click', orgStep2Next);
H('btn-org-s3-back').addEventListener('click', orgStep3Back);

/* ── Phone sync + submit guard ── */
function syncPhone() {
  var map = { patient:'pat-ph-val', provider:'prv-ph-val', hospital:'org-ph-val', clinic:'org-ph-val', pharmacy:'org-ph-val' };
  var src = H(map[currentType] || '');
  H('rg-phone-out').value = src ? src.value : '';
}

H('regForm').addEventListener('submit', function(e) {
  var termId = { patient:'pat-terms', provider:'prv-terms' }[currentType] || 'org-terms';
  if (!H(termId) || !H(termId).checked) {
    e.preventDefault();
    alert('Please accept the Terms of Service to continue.');
    return;
  }
  syncPhone();
  var btnId = { patient:'pat-submit', provider:'prv-submit' }[currentType] || 'org-submit';
  var btn = H(btnId);
  if (btn) { btn.disabled = true; btn.textContent = 'Creating account…'; }
});

/* ── Re-open correct screen+step on server validation error ── */
@if($errors->any())
(function() {
  var t = '{{ old("reg_type", "patient") }}';
  if (t === 'provider') {
    chooseType('provider');
    @if($errors->hasAny(['specialty','license_no','mdcn_folio']))
      progSet('prv', 2); stepShow('prv', 2);
    @else
      progSet('prv', 1); stepShow('prv', 1);
    @endif
  } else if (t === 'hospital' || t === 'clinic' || t === 'pharmacy') {
    chooseType(t);
    @if($errors->hasAny(['org_name','org_license','org_type']))
      progSet('org', 2); stepShow('org', 2);
    @else
      progSet('org', 1); stepShow('org', 1);
    @endif
  } else {
    chooseType('patient');
    @if($errors->hasAny(['dob','gender','blood_type','genotype','nin']))
      progSet('pat', 2); stepShow('pat', 2);
    @else
      progSet('pat', 1); stepShow('pat', 1);
    @endif
  }
}());
@endif

}); /* end DOMContentLoaded */
</script>

@endsection
