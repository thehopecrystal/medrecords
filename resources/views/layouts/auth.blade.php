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
@vite(['resources/css/app.css'])
@stack('styles')
<style nonce="{{ $cspNonce }}">
/* Auth layout: two-panel full-height grid */
body { height: 100vh; overflow: hidden; display: grid; grid-template-columns: 1fr 1fr; }
@media (max-width: 768px) {
  body { grid-template-columns: 1fr; height: auto; overflow: visible; }
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

@stack('scripts')
</body>
</html>
