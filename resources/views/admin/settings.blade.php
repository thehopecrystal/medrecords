@extends('layouts.app')
@section('title', 'Site Settings')
@section('topbar-title', 'Settings')

@section('content')
<div class="flex gap-7 items-start">

  {{-- Left settings nav --}}
  <div class="settings-nav-sticky">
    <div class="settings-nav-card">
      <div class="settings-nav-header">
        <p class="settings-nav-heading">Configuration</p>
      </div>
      <nav class="settings-nav-list">
        @foreach([
          'general'  => ['⚙️',  'General',      'Identity & branding'],
          'contact'  => ['📞', 'Contact',       'Phone, email, address'],
          'social'   => ['🔗', 'Social Media',  'Profile links'],
          'smtp'     => ['✉️',  'Email / SMTP',  'Outgoing mail'],
          'sms'      => ['📱', 'SMS / Twilio',  'SMS notifications'],
          'payments' => ['💳', 'Payments',      'Payment gateway'],
          'landing'  => ['🌐', 'Landing Page',  'Hero & CTA content'],
        ] as $key => [$icon, $label, $hint])
        <button type="button" data-stab="{{ $key }}"
          class="snav-btn {{ $loop->first ? 'snav-active' : '' }}">
          <span class="snav-icon">{{ $icon }}</span>
          <div class="snav-text">
            <span class="snav-label">{{ $label }}</span>
            <span class="snav-hint">{{ $hint }}</span>
          </div>
        </button>
        @endforeach
      </nav>
    </div>
  </div>

  {{-- Main content --}}
  <div class="flex-1 min-w-0">

    {{-- ══════════════ GENERAL ══════════════ --}}
    <div class="stab-panel stab-active" id="stab-general">
      <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Platform Identity --}}
        <div class="s-card mb-4">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">Platform Identity</p>
              <p class="s-card-sub">Core branding shown throughout the app and in outgoing emails.</p>
            </div>
          </div>
          <div class="s-card-body">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="s-label">Site Name <span class="text-danger">*</span></label>
                <input type="text" name="site_name" value="{{ $settings['site_name'] }}" required
                  placeholder="MedRecords" class="s-input">
                <p class="s-hint">Shown in browser tabs, emails, and the app header.</p>
              </div>
              <div>
                <label class="s-label">Tagline</label>
                <input type="text" name="site_tagline" value="{{ $settings['site_tagline'] }}"
                  placeholder="Your health record, always with you" class="s-input">
              </div>
              <div class="col-span-2">
                <label class="s-label">Footer About Text</label>
                <textarea name="footer_about" class="s-textarea"
                  placeholder="Short description of your platform for the website footer…">{{ $settings['footer_about'] }}</textarea>
              </div>
            </div>
          </div>
        </div>

        {{-- Logo & Favicon --}}
        <div class="s-card mb-4">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">Logo &amp; Favicon</p>
              <p class="s-card-sub">Visual identity used in the navigation and browser tab.</p>
            </div>
          </div>
          <div class="s-card-body">

            {{-- Logo --}}
            <div class="flex items-start gap-5 mb-6">
              <div class="asset-preview asset-preview-logo">
                @if($settings['site_logo_url'])
                  <img src="{{ $settings['site_logo_url'] }}" alt="Logo">
                @else
                  <span class="asset-icon-lg">🖼</span>
                @endif
              </div>
              <div class="flex-1">
                <p class="s-asset-label">Site Logo</p>
                <p class="s-hint s-hint-mb">PNG or SVG, transparent background. Max 2 MB. Uploading a file overrides the URL below.</p>
                <input type="file" name="site_logo_file" accept="image/*" class="s-file">
                <input type="url" name="site_logo_url" value="{{ $settings['site_logo_url'] }}"
                  placeholder="https://example.com/logo.png"
                  class="s-input s-input-sm mt-2">
              </div>
            </div>

            <hr class="s-divider">

            {{-- Favicon --}}
            <div class="flex items-start gap-5">
              <div class="asset-preview asset-preview-favicon">
                @if($settings['site_favicon_url'])
                  <img src="{{ $settings['site_favicon_url'] }}" alt="Favicon">
                @else
                  <span class="asset-icon-sm">🔲</span>
                @endif
              </div>
              <div class="flex-1">
                <p class="s-asset-label">Favicon</p>
                <p class="s-hint s-hint-mb">ICO, PNG, or SVG. 32 × 32 px recommended. Max 512 KB.</p>
                <input type="file" name="site_favicon_file" accept="image/*,.ico" class="s-file">
                <input type="url" name="site_favicon_url" value="{{ $settings['site_favicon_url'] }}"
                  placeholder="https://example.com/favicon.ico"
                  class="s-input s-input-sm mt-2">
              </div>
            </div>
          </div>
        </div>

        <div class="s-save-bar">
          <button type="submit" class="s-btn-primary">💾 Save general settings</button>
        </div>
      </form>
    </div>

    {{-- ══════════════ CONTACT ══════════════ --}}
    <div class="stab-panel" id="stab-contact">
      <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        <div class="s-card mb-4">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">Contact Information</p>
              <p class="s-card-sub">Displayed on the landing page and in email footers.</p>
            </div>
          </div>
          <div class="s-card-body">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="s-label">Primary Phone</label>
                <input type="text" name="contact_phone_1" value="{{ $settings['contact_phone_1'] }}"
                  placeholder="+234 (0) 800 000 0000" class="s-input">
              </div>
              <div>
                <label class="s-label">Secondary Phone</label>
                <input type="text" name="contact_phone_2" value="{{ $settings['contact_phone_2'] }}"
                  placeholder="+234 (0) 700 000 0000" class="s-input">
              </div>
              <div class="col-span-2">
                <label class="s-label">Email Address</label>
                <input type="email" name="contact_email" value="{{ $settings['contact_email'] }}"
                  placeholder="hello@yoursite.com" class="s-input">
              </div>
              <div class="col-span-2">
                <label class="s-label">Street Address</label>
                <input type="text" name="contact_address" value="{{ $settings['contact_address'] }}"
                  placeholder="14 Adeola Odeku Street" class="s-input">
              </div>
              <div>
                <label class="s-label">City</label>
                <input type="text" name="contact_city" value="{{ $settings['contact_city'] }}"
                  placeholder="Victoria Island" class="s-input">
              </div>
              <div>
                <label class="s-label">State</label>
                <select name="contact_state" class="s-input">
                  <option value="">Select state…</option>
                  @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $st)
                    <option value="{{ $st }}" {{ $settings['contact_state'] === $st ? 'selected' : '' }}>{{ $st }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="s-save-bar">
          <button type="submit" class="s-btn-primary">💾 Save contact settings</button>
        </div>
      </form>
    </div>

    {{-- ══════════════ SOCIAL MEDIA ══════════════ --}}
    <div class="stab-panel" id="stab-social">
      <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        <div class="s-card mb-4">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">Social Media Links</p>
              <p class="s-card-sub">Leave any field blank to hide that icon on the website footer.</p>
            </div>
          </div>
          <div class="s-card-body">
            <div class="grid grid-cols-2 gap-4">
              @foreach([
                ['social_facebook',  '📘', 'Facebook',   'https://facebook.com/yourpage'],
                ['social_twitter',   '𝕏',  'Twitter / X','https://twitter.com/yourhandle'],
                ['social_instagram', '📷', 'Instagram',  'https://instagram.com/yourhandle'],
                ['social_linkedin',  '🔗', 'LinkedIn',   'https://linkedin.com/company/yourname'],
                ['social_youtube',   '▶',  'YouTube',    'https://youtube.com/@yourchannel'],
              ] as [$field, $icon, $label, $ph])
              <div>
                <label class="s-label">{{ $icon }} {{ $label }}</label>
                <input type="url" name="{{ $field }}" value="{{ $settings[$field] ?? '' }}"
                  placeholder="{{ $ph }}" class="s-input">
              </div>
              @endforeach
            </div>
          </div>
        </div>

        <div class="s-save-bar">
          <button type="submit" class="s-btn-primary">💾 Save social links</button>
        </div>
      </form>
    </div>

    {{-- ══════════════ EMAIL / SMTP ══════════════ --}}
    <div class="stab-panel" id="stab-smtp">
      <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        <div class="s-card mb-4">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">Email / SMTP Settings</p>
              <p class="s-card-sub">Configure outgoing email delivery for notifications and alerts.</p>
            </div>
          </div>
          <div class="s-card-body">

            <p class="s-section-label">Server Configuration</p>
            <div class="grid grid-cols-3 gap-4 mb-6">
              <div class="col-span-1">
                <label class="s-label">SMTP Host</label>
                <input type="text" name="smtp_host" value="{{ $settings['smtp_host'] }}"
                  placeholder="smtp.gmail.com" class="s-input">
              </div>
              <div>
                <label class="s-label">Port</label>
                <input type="number" name="smtp_port" value="{{ $settings['smtp_port'] }}"
                  placeholder="587" class="s-input">
              </div>
              <div>
                <label class="s-label">Encryption</label>
                <select name="smtp_encryption" class="s-input">
                  <option value="tls"  {{ $settings['smtp_encryption'] === 'tls'  ? 'selected' : '' }}>TLS (recommended)</option>
                  <option value="ssl"  {{ $settings['smtp_encryption'] === 'ssl'  ? 'selected' : '' }}>SSL</option>
                  <option value="none" {{ $settings['smtp_encryption'] === 'none' ? 'selected' : '' }}>None</option>
                </select>
              </div>
            </div>

            <hr class="s-divider">

            <p class="s-section-label">Authentication</p>
            <div class="grid grid-cols-2 gap-4 mb-6">
              <div>
                <label class="s-label">Username</label>
                <input type="text" name="smtp_username" value="{{ $settings['smtp_username'] }}"
                  placeholder="your@email.com" autocomplete="off" class="s-input">
              </div>
              <div>
                <label class="s-label">Password</label>
                <input type="password" name="smtp_password"
                  placeholder="Leave blank to keep current"
                  autocomplete="new-password" class="s-input">
                <p class="s-hint">Leave blank to keep the existing password.</p>
              </div>
            </div>

            <hr class="s-divider">

            <p class="s-section-label">Sender Identity</p>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="s-label">From Name</label>
                <input type="text" name="smtp_from_name" value="{{ $settings['smtp_from_name'] }}"
                  placeholder="MedRecords" class="s-input">
              </div>
              <div>
                <label class="s-label">From Email</label>
                <input type="email" name="smtp_from_email" value="{{ $settings['smtp_from_email'] }}"
                  placeholder="noreply@medrecords.ng" class="s-input">
              </div>
            </div>
          </div>
        </div>

        <div class="s-save-bar">
          <button type="submit" class="s-btn-primary">💾 Save SMTP settings</button>
        </div>
      </form>

      {{-- SMTP test — standalone form, not nested --}}
      <div class="s-card mb-4">
        <div class="s-card-head s-card-head-row">
          <div>
            <p class="s-card-title">Test Email Delivery</p>
            <p class="s-card-sub">Verify your SMTP configuration by sending a test message.</p>
          </div>
          <span class="s-badge">Save settings first</span>
        </div>
        <div class="s-card-body">
          <form method="POST" action="{{ route('admin.settings.smtp-test') }}" class="s-form-row">
            @csrf
            <input type="email" name="test_email" value="{{ auth()->user()->email }}"
              placeholder="test@example.com" class="s-input s-flex-1">
            <button type="submit" class="s-btn-secondary s-btn-no-shrink">
              ✉️ Send test email
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- ══════════════ SMS / TWILIO ══════════════ --}}
    <div class="stab-panel" id="stab-sms">
      <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        {{-- Enable/disable --}}
        <div class="s-card mb-4">
          <div class="s-card-head s-card-head-row">
            <div>
              <p class="s-card-title">SMS Notifications</p>
              <p class="s-card-sub">Controls whether OTP codes and alerts are sent via SMS.</p>
            </div>
            <label class="s-toggle">
              <input type="hidden" name="sms_enabled" value="0">
              <input type="checkbox" name="sms_enabled" value="1" id="smsToggle"
                {{ ($settings['sms_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
              <span class="s-toggle-track">
                <span class="s-toggle-thumb"></span>
              </span>
              <span class="s-toggle-label" id="smsLabel">
                {{ ($settings['sms_enabled'] ?? '1') === '1' ? 'Enabled' : 'Disabled' }}
              </span>
            </label>
          </div>
        </div>

        {{-- Twilio credentials --}}
        <div class="s-card mb-4">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">Twilio Credentials</p>
              <p class="s-card-sub">Get these values from your Twilio Console dashboard.</p>
            </div>
          </div>
          <div class="s-card-body">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="s-label">Account SID</label>
                <input type="text" name="twilio_account_sid"
                  value="{{ $settings['twilio_account_sid'] }}"
                  placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                  autocomplete="off" class="s-input">
                <p class="s-hint">Found on your Twilio Console dashboard.</p>
              </div>
              <div>
                <label class="s-label">Auth Token</label>
                <input type="password" name="twilio_auth_token"
                  placeholder="Leave blank to keep current"
                  autocomplete="new-password" class="s-input">
                <p class="s-hint">Leave blank to keep the existing token.</p>
              </div>
              <div class="col-span-2">
                <label class="s-label">From Number</label>
                <input type="text" name="twilio_from_number"
                  value="{{ $settings['twilio_from_number'] }}"
                  placeholder="+12345678901" class="s-input">
                <p class="s-hint">Your Twilio number in E.164 format (e.g. +12345678901). Nigerian numbers are converted automatically for delivery.</p>
              </div>
            </div>
          </div>
        </div>

        {{-- OTP / 2FA channel toggles --}}
        <div class="s-card mb-4">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">Two-Factor Authentication (OTP)</p>
              <p class="s-card-sub">Control which channels deliver one-time codes to users who have 2FA enabled. Super admins always bypass OTP regardless of these settings.</p>
            </div>
          </div>
          <div class="s-card-body s-card-body-col">
            <div class="s-toggle-row">
              <div>
                <p class="s-label s-label-tight">OTP via SMS</p>
                <p class="s-hint s-hint-flush">Send the 6-digit code by text message using your Twilio number above.</p>
              </div>
              <label class="s-toggle">
                <input type="hidden" name="otp_sms_enabled" value="0">
                <input type="checkbox" name="otp_sms_enabled" value="1" id="otpSmsToggle"
                  {{ ($settings['otp_sms_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                <span class="s-toggle-track"><span class="s-toggle-thumb"></span></span>
                <span class="s-toggle-label" id="otpSmsLabel">
                  {{ ($settings['otp_sms_enabled'] ?? '1') === '1' ? 'Enabled' : 'Disabled' }}
                </span>
              </label>
            </div>
            <div class="s-toggle-row">
              <div>
                <p class="s-label s-label-tight">OTP via Email</p>
                <p class="s-hint s-hint-flush">Also (or instead) deliver the code to the user's registered email address.</p>
              </div>
              <label class="s-toggle">
                <input type="hidden" name="otp_email_enabled" value="0">
                <input type="checkbox" name="otp_email_enabled" value="1" id="otpEmailToggle"
                  {{ ($settings['otp_email_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                <span class="s-toggle-track"><span class="s-toggle-thumb"></span></span>
                <span class="s-toggle-label" id="otpEmailLabel">
                  {{ ($settings['otp_email_enabled'] ?? '0') === '1' ? 'Enabled' : 'Disabled' }}
                </span>
              </label>
            </div>
          </div>
        </div>

        <div class="s-save-bar">
          <button type="submit" class="s-btn-primary">💾 Save SMS settings</button>
        </div>
      </form>

      {{-- SMS test — standalone form, not nested --}}
      <div class="s-card mb-4">
        <div class="s-card-head s-card-head-row">
          <div>
            <p class="s-card-title">Test SMS Delivery</p>
            <p class="s-card-sub">Verify your Twilio credentials by sending a test message.</p>
          </div>
          <span class="s-badge">Save settings first</span>
        </div>
        <div class="s-card-body">
          <form method="POST" action="{{ route('admin.settings.sms-test') }}" class="s-form-row">
            @csrf
            <input type="text" name="test_phone" value="{{ auth()->user()->phone }}"
              placeholder="08012345678" class="s-input s-flex-1">
            <button type="submit" class="s-btn-secondary s-btn-no-shrink">
              📱 Send test SMS
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- ══════════════ PAYMENTS ══════════════ --}}
    <div class="stab-panel" id="stab-payments">
      <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        {{-- Gateway selector --}}
        <div class="s-card mb-4">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">Active Payment Gateway</p>
              <p class="s-card-sub">Select which gateway handles subscription and payment collection.</p>
            </div>
          </div>
          <div class="s-card-body">
            <div class="grid grid-cols-3 gap-3">
              @foreach([
                'none'        => ['🚫', 'None / Disabled', 'Payments not collected'],
                'paystack'    => ['💚', 'Paystack',        'Leading Nigerian gateway'],
                'flutterwave' => ['🦋', 'Flutterwave',     'Pan-African payments'],
              ] as $val => [$icon, $label, $desc])
              <label class="gw-card {{ ($settings['payment_gateway'] ?? 'none') === $val ? 'gw-active' : '' }}">
                <input type="radio" name="payment_gateway" value="{{ $val }}"
                  {{ ($settings['payment_gateway'] ?? 'none') === $val ? 'checked' : '' }}>
                <span class="gw-icon">{{ $icon }}</span>
                <span class="gw-label">{{ $label }}</span>
                <span class="gw-desc">{{ $desc }}</span>
              </label>
              @endforeach
            </div>
          </div>
        </div>

        {{-- Paystack keys --}}
        <div class="s-card mb-4 {{ ($settings['payment_gateway'] ?? 'none') === 'paystack' ? 'gw-visible' : '' }}" id="paystack-section">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">💚 Paystack API Keys</p>
              <p class="s-card-sub">Dashboard → Settings → API Keys &amp; Webhooks.</p>
            </div>
          </div>
          <div class="s-card-body">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="s-label">Public Key</label>
                <input type="text" name="paystack_public_key"
                  value="{{ $settings['paystack_public_key'] }}"
                  placeholder="pk_live_xxxxxxxxxxxxxxxxxxxxxxxx"
                  autocomplete="off" class="s-input">
                <p class="s-hint">Safe to use in frontend JavaScript.</p>
              </div>
              <div>
                <label class="s-label">Secret Key</label>
                <input type="password" name="paystack_secret_key"
                  placeholder="Leave blank to keep current"
                  autocomplete="new-password" class="s-input">
                <p class="s-hint">Keep secret — server-side only. Leave blank to preserve current value.</p>
              </div>
            </div>
            <div class="s-warn-box mt-4">
              <span>⚠️</span>
              <span>Never use test keys in production. Switch to live keys before going live.</span>
            </div>
          </div>
        </div>

        {{-- Flutterwave keys --}}
        <div class="s-card mb-4 {{ ($settings['payment_gateway'] ?? 'none') === 'flutterwave' ? 'gw-visible' : '' }}" id="flutterwave-section">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">🦋 Flutterwave API Keys</p>
              <p class="s-card-sub">Dashboard → Settings → API Keys.</p>
            </div>
          </div>
          <div class="s-card-body">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="s-label">Public Key</label>
                <input type="text" name="flutterwave_public_key"
                  value="{{ $settings['flutterwave_public_key'] }}"
                  placeholder="FLWPUBK-xxxxxxxxxxxxxxxx-X"
                  autocomplete="off" class="s-input">
              </div>
              <div>
                <label class="s-label">Secret Key</label>
                <input type="password" name="flutterwave_secret_key"
                  placeholder="Leave blank to keep current"
                  autocomplete="new-password" class="s-input">
                <p class="s-hint">Leave blank to preserve the existing key.</p>
              </div>
            </div>
            <div class="s-warn-box mt-4">
              <span>⚠️</span>
              <span>Never use test keys in production. Switch to live keys before going live.</span>
            </div>
          </div>
        </div>

        <div class="s-save-bar">
          <button type="submit" class="s-btn-primary">💾 Save payment settings</button>
        </div>
      </form>
    </div>

    {{-- ══════════════ LANDING PAGE ══════════════ --}}
    <div class="stab-panel" id="stab-landing">
      <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        <div class="s-card mb-4">
          <div class="s-card-head s-card-head-row">
            <div>
              <p class="s-card-title">Landing Page Content</p>
              <p class="s-card-sub">Customise the public-facing homepage hero section.</p>
            </div>
            <a href="{{ url('/') }}" target="_blank" class="s-btn-secondary s-btn-sm">
              👀 Preview →
            </a>
          </div>
          <div class="s-card-body">
            <div class="grid grid-cols-2 gap-4">
              <div class="col-span-2">
                <label class="s-label">Hero Headline</label>
                <input type="text" name="hero_headline" value="{{ $settings['hero_headline'] }}"
                  placeholder="Your health record, always with you" class="s-input">
                <p class="s-hint">Use &lt;br&gt; for a line break. The second line is highlighted in green on the page.</p>
              </div>
              <div class="col-span-2">
                <label class="s-label">Hero Subtext</label>
                <textarea name="hero_subtext" class="s-textarea"
                  placeholder="Brief description shown below the headline…">{{ $settings['hero_subtext'] }}</textarea>
              </div>
              <div>
                <label class="s-label">Primary CTA Button</label>
                <input type="text" name="hero_cta_primary" value="{{ $settings['hero_cta_primary'] }}"
                  placeholder="Get started free" class="s-input">
                <p class="s-hint">The main call-to-action button text.</p>
              </div>
              <div>
                <label class="s-label">Secondary CTA Button</label>
                <input type="text" name="hero_cta_secondary" value="{{ $settings['hero_cta_secondary'] }}"
                  placeholder="Learn more" class="s-input">
                <p class="s-hint">The secondary "Learn more" link text.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="s-save-bar">
          <button type="submit" class="s-btn-primary">💾 Save landing page</button>
        </div>
      </form>
    </div>

  </div>
</div>

<script nonce="{{ $cspNonce }}">
// Tab navigation — CSS class toggling (CSP-safe, no inline styles)
const navBtns = document.querySelectorAll('.snav-btn');
const panels  = document.querySelectorAll('.stab-panel');

function switchTab(key) {
  navBtns.forEach(b => b.classList.toggle('snav-active', b.dataset.stab === key));
  panels.forEach(p => p.classList.toggle('stab-active', p.id === 'stab-' + key));
}

navBtns.forEach(btn => {
  btn.addEventListener('click', function() { switchTab(this.dataset.stab); });
});

// Toggle labels
[['smsToggle','smsLabel'],['otpSmsToggle','otpSmsLabel'],['otpEmailToggle','otpEmailLabel']].forEach(function([tid,lid]) {
  var t = document.getElementById(tid), l = document.getElementById(lid);
  if (t) t.addEventListener('change', function() { l.textContent = this.checked ? 'Enabled' : 'Disabled'; });
});

// Payment gateway card selector
document.querySelectorAll('.gw-card').forEach(card => {
  card.addEventListener('click', function() {
    document.querySelectorAll('.gw-card').forEach(c => c.classList.remove('gw-active'));
    this.classList.add('gw-active');
    const val = this.querySelector('input').value;
    document.getElementById('paystack-section').classList.toggle('gw-visible',    val === 'paystack');
    document.getElementById('flutterwave-section').classList.toggle('gw-visible', val === 'flutterwave');
  });
});
</script>
@endsection
