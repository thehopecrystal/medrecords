<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
  use App\Models\SiteSetting;
  SiteSetting::loadAll();
  $siteName = SiteSetting::get('site_name', 'MedRecords');
  $logoUrl  = SiteSetting::get('site_logo_url', '');
@endphp
<title>@yield('title', 'Sign In') — {{ $siteName }}</title>
<style nonce="{{ $cspNonce }}">
:root { --navy: #1E3A5F; --forest: #2C5F2D; }
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Inter", Roboto, Arial, sans-serif;
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1fr 1fr;
  color: #0F172A;
  line-height: 1.55;
}

/* ─── Left panel (branding) ─────────────────── */
.auth-left {
  background: linear-gradient(160deg, #0D1F35 0%, #1E3A5F 50%, #1A4A2E 100%);
  display: flex; flex-direction: column;
  justify-content: space-between;
  padding: 40px 48px;
  position: relative; overflow: hidden;
}
.auth-left::before {
  content: '';
  position: absolute; inset: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.auth-left-content { position: relative; z-index: 1; }
.auth-logo {
  display: flex; align-items: center; gap: 12px;
  margin-bottom: 64px;
}
.auth-logo-mark {
  width: 40px; height: 40px; border-radius: 12px;
  background: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.2);
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; font-weight: 900; color: #fff;
}
.auth-logo img { width: 40px; height: 40px; border-radius: 12px; object-fit: contain; }
.auth-logo-text { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: -0.02em; }

.auth-headline {
  font-size: 36px; font-weight: 900; color: #fff;
  line-height: 1.15; letter-spacing: -0.02em;
  margin-bottom: 18px;
}
.auth-headline span { color: #6EE4A8; }
.auth-subhead { font-size: 15px; color: rgba(255,255,255,0.7); line-height: 1.65; max-width: 340px; }

.auth-features { margin-top: 48px; display: flex; flex-direction: column; gap: 16px; }
.auth-feature {
  display: flex; align-items: center; gap: 12px;
  font-size: 13px; color: rgba(255,255,255,0.8);
}
.auth-feature-dot {
  width: 28px; height: 28px; border-radius: 8px;
  background: rgba(255,255,255,0.1);
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; flex-shrink: 0;
}

.auth-left-footer {
  position: relative; z-index: 1;
  font-size: 12px; color: rgba(255,255,255,0.4);
}

/* ─── Right panel (form) ────────────────────── */
.auth-right {
  background: #F8FAFC;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 32px;
  overflow-y: auto;
  min-height: 100vh;
}
.auth-box {
  width: 100%; max-width: 440px;
  margin: auto;
  padding: 24px 0;
}
.auth-box-header { margin-bottom: 28px; }
.auth-box-header h2 { font-size: 24px; font-weight: 800; color: #0F172A; letter-spacing: -0.02em; }
.auth-box-header p  { font-size: 14px; color: #64748B; margin-top: 5px; }
h2 { font-size: 22px; font-weight: 800; color: #0F172A; letter-spacing: -0.02em; margin-bottom: 4px; }
.subtitle { font-size: 14px; color: #64748B; margin-bottom: 20px; line-height: 1.5; }

/* ─── Form elements ─────────────────────────── */
.form-field { margin-bottom: 16px; }
.form-field label {
  display: block;
  font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.06em;
  color: #475569; margin-bottom: 6px;
}
.form-field input, .form-field select {
  width: 100%;
  background: #fff;
  border: 1.5px solid #E2E8F0;
  border-radius: 9px;
  padding: 10px 14px;
  font-size: 14px; color: #0F172A;
  transition: border-color 0.15s, box-shadow 0.15s;
  font-family: inherit;
}
.form-field input:focus, .form-field select:focus {
  outline: none;
  border-color: #1E3A5F;
  box-shadow: 0 0 0 3px rgba(30,58,95,0.08);
}
.form-field input::placeholder { color: #CBD5E1; }
.btn-submit {
  width: 100%;
  background: #1E3A5F; color: #fff;
  border: none; border-radius: 10px;
  padding: 13px 16px;
  font-size: 15px; font-weight: 700;
  cursor: pointer; margin-top: 6px;
  transition: background 0.15s, transform 0.1s;
  font-family: inherit;
}
.btn-submit:hover { background: #163050; }
.btn-submit:active { transform: scale(0.99); }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

/* ─── Alerts ────────────────────────────────── */
.alert-error {
  background: #FBEAEC;
  color: #7A1F2C;
  border-left: 3px solid #B23A48;
  border-radius: 9px;
  padding: 11px 14px;
  font-size: 13px; margin-bottom: 18px;
}
.alert-success {
  background: #EDF7ED;
  color: #1A4D1B;
  border-left: 3px solid #2C5F2D;
  border-radius: 9px;
  padding: 11px 14px;
  font-size: 13px; margin-bottom: 18px;
}

/* ─── Divider ───────────────────────────────── */
.divider {
  display: flex; align-items: center; gap: 12px;
  margin: 22px 0; font-size: 12px; color: #94A3B8;
  text-transform: uppercase; letter-spacing: 0.06em;
}
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #E2E8F0; }

/* ─── Role links ────────────────────────────── */
.role-links { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.role-link {
  text-align: center; padding: 10px 8px;
  border-radius: 9px; border: 1.5px solid #E2E8F0;
  font-size: 12px; font-weight: 600;
  color: #475569; background: #fff;
  cursor: pointer; transition: all 0.15s;
  font-family: inherit;
}
.role-link:hover { border-color: #1E3A5F; color: #1E3A5F; background: #EEF3FA; }

/* ─── Footer link ───────────────────────────── */
.form-footer {
  text-align: center; margin-top: 22px;
  font-size: 13px; color: #64748B;
}
.form-footer a { color: #1E3A5F; font-weight: 700; text-decoration: none; }
.form-footer a:hover { text-decoration: underline; }

/* ─── Input action (eye icon) ───────────────── */
.input-with-action { position: relative; display: flex; align-items: center; }
.input-with-action input { padding-right: 42px; }
.input-action {
  position: absolute; right: 12px;
  background: none; border: none; cursor: pointer;
  font-size: 16px; color: #94A3B8; padding: 2px;
  transition: color 0.15s;
}
.input-action:hover { color: #475569; }

/* ─── Input prefix ───────────────────────────── */
.input-prefix-wrap { display: flex; }
.input-prefix {
  background: #F1F5F9; border: 1.5px solid #E2E8F0;
  border-right: none; border-radius: 9px 0 0 9px;
  padding: 10px 12px; font-size: 12px; color: #64748B;
  white-space: nowrap; display: flex; align-items: center;
}
.input-prefix-wrap input { border-radius: 0 9px 9px 0 !important; }

/* ─── Progress bar (register wizard) ────────── */
.progress-bar {
  display: flex; align-items: center;
  margin-bottom: 28px;
}
.progress-step { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.step-dot {
  width: 30px; height: 30px; border-radius: 50%;
  background: #E2E8F0; color: #94A3B8;
  font-size: 12px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.2s;
}
.step-label { font-size: 10px; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap; transition: color 0.2s; }
.progress-step.active .step-dot { background: #1E3A5F; color: #fff; }
.progress-step.active .step-label { color: #1E3A5F; }
.progress-step.done .step-dot { background: #2C5F2D; color: #fff; }
.progress-line { flex: 1; height: 2px; background: #E2E8F0; min-width: 24px; margin: 0 6px; margin-bottom: 20px; transition: background 0.2s; }
.progress-line.done { background: #2C5F2D; }

/* ─── Wizard form helpers ────────────────────── */
.req { color: #B23A48; }
.field-hint { font-size: 11px; color: #94A3B8; margin-top: 4px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.strength-bar { height: 3px; background: #E2E8F0; border-radius: 3px; margin-top: 6px; overflow: hidden; }
.strength-fill { height: 100%; width: 0; border-radius: 3px; transition: width 0.3s, background 0.3s; }
.match-hint { min-height: 14px; font-size: 11px; margin-top: 4px; }
.btn-back {
  background: #F1F5F9; color: #334155;
  border: 1.5px solid #E2E8F0; border-radius: 10px;
  padding: 12px 18px; font-size: 14px; font-weight: 600;
  cursor: pointer; font-family: inherit; transition: background 0.15s;
}
.btn-back:hover { background: #E2E8F0; }
.review-card { border: 1.5px solid #E2E8F0; border-radius: 12px; overflow: hidden; margin-bottom: 12px; }
.review-section { background: #F8FAFC; padding: 8px 16px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748B; }
.review-row { display: flex; justify-content: space-between; padding: 8px 16px; font-size: 13px; border-top: 1px solid #F1F5F9; }
.review-row span { color: #64748B; }
.review-row strong { text-align: right; max-width: 55%; word-break: break-all; }
.checkbox-label { display: flex; align-items: flex-start; gap: 8px; font-size: 12px; color: #64748B; line-height: 1.55; text-transform: none; letter-spacing: 0; font-weight: 400; cursor: pointer; }

/* ─── Responsive ────────────────────────────── */
@media (max-width: 768px) {
  body { grid-template-columns: 1fr; }
  .auth-left { display: none; }
  .auth-right { padding: 24px 20px; background: #fff; }
}
</style>
</head>
<body>

{{-- ── Left branding panel ──────────────────────────── --}}
<div class="auth-left">
  <div class="auth-left-content">
    <div class="auth-logo">
      @if($logoUrl)
        <img src="{{ $logoUrl }}" alt="{{ $siteName }}">
      @else
        <div class="auth-logo-mark">M</div>
      @endif
      <span class="auth-logo-text">{{ $siteName }}</span>
    </div>

    <h1 class="auth-headline" id="leftHeadline">Your health<br>record, <span>always<br>with you</span></h1>
    <p class="auth-subhead" id="leftSubhead">Secure, patient-owned electronic health records for every Nigerian — shared only with your consent.</p>

    <div class="auth-features" id="leftFeatures">
      <div class="auth-feature">
        <div class="auth-feature-dot">🔐</div>
        <span>Consent-gated access — you control who sees your data</span>
      </div>
      <div class="auth-feature">
        <div class="auth-feature-dot">📋</div>
        <span>Complete health history across all facilities</span>
      </div>
      <div class="auth-feature">
        <div class="auth-feature-dot">📲</div>
        <span>Share access codes instantly via SMS</span>
      </div>
      <div class="auth-feature">
        <div class="auth-feature-dot">🏥</div>
        <span>NHIA eligibility checks and prescription verification</span>
      </div>
    </div>
  </div>
  <div class="auth-left-footer">
    © {{ date('Y') }} {{ $siteName }} · Built for Nigeria 🇳🇬
  </div>
</div>

{{-- ── Right form panel ─────────────────────────────── --}}
<div class="auth-right">
  <div class="auth-box">
    {{-- Logo (mobile only) --}}
    <div style="text-align:center;margin-bottom:28px;display:none;" class="mobile-logo">
      @if($logoUrl)
        <img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="height:36px;">
      @else
        <div style="font-size:22px;font-weight:900;color:#1E3A5F;">{{ $siteName }}</div>
      @endif
    </div>

    @yield('content')
  </div>
</div>

<style nonce="{{ $cspNonce }}">
@media (max-width: 768px) {
  .mobile-logo { display: block !important; }
  .auth-box { max-width: 100%; }
}
</style>
</body>
</html>
