@extends('layouts.app')
@section('title', 'Site Settings')
@section('topbar-title', 'Settings')

@section('content')
<div class="flex gap-7 items-start">

  {{-- â”€â”€ Left settings nav â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
  <div class="flex-none" style="width:216px;position:sticky;top:calc(var(--topbar-h) + 28px);">
    <div class="settings-nav-card">
      <div class="settings-nav-header">
        <p class="settings-nav-heading">Configuration</p>
      </div>
      <nav class="settings-nav-list">
        @foreach([
          'general'  => ['âš™ï¸', 'General',      'Identity & branding'],
          'contact'  => ['ðŸ“ž', 'Contact',       'Phone, email, address'],
          'social'   => ['ðŸ”—', 'Social Media',  'Profile links'],
          'smtp'     => ['âœ‰ï¸', 'Email / SMTP',  'Outgoing mail'],
          'sms'      => ['ðŸ“±', 'SMS / Twilio',  'SMS notifications'],
          'payments' => ['ðŸ’³', 'Payments',      'Payment gateway'],
          'landing'  => ['ðŸŒ', 'Landing Page',  'Hero & CTA content'],
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

  {{-- â”€â”€ Main content â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
  <div class="flex-1 min-w-0">
    <form method="POST" action="{{ route('admin.settings.update') }}"
          enctype="multipart/form-data" id="settingsForm">
      @csrf

      {{-- â•”â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•— --}}
      {{-- â•‘  GENERAL                           â•‘ --}}
      {{-- â•šâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
      <div class="stab-panel" id="stab-general">

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
                  placeholder="Short description of your platform for the website footerâ€¦">{{ $settings['footer_about'] }}</textarea>
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

            {{-- Logo upload --}}
            <div class="flex items-start gap-5 mb-6">
              <div class="asset-preview" style="width:112px;height:72px;border-radius:12px;">
                @if($settings['site_logo_url'])
                  <img src="{{ $settings['site_logo_url'] }}" alt="Logo"
                    style="max-width:96px;max-height:56px;object-fit:contain;">
                @else
                  <span style="font-size:24px;opacity:.25;">ðŸ–¼</span>
                @endif
              </div>
              <div class="flex-1">
                <p class="text-sm font-semibold" style="color:var(--navy);margin-bottom:4px;">Site Logo</p>
                <p class="s-hint" style="margin-bottom:10px;">PNG or SVG, transparent background. Max 2 MB. Uploading a file overrides the URL below.</p>
                <input type="file" name="site_logo_file" accept="image/*" class="s-file">
                <input type="url" name="site_logo_url" value="{{ $settings['site_logo_url'] }}"
                  placeholder="https://example.com/logo.png"
                  class="s-input mt-2" style="font-size:12px;padding:7px 11px;">
              </div>
            </div>

            <div style="border-top:1px solid #F1F5F9;margin-bottom:20px;"></div>

            {{-- Favicon upload --}}
            <div class="flex items-start gap-5">
              <div class="asset-preview" style="width:56px;height:56px;border-radius:10px;">
                @if($settings['site_favicon_url'])
                  <img src="{{ $settings['site_favicon_url'] }}" alt="Favicon"
                    style="width:32px;height:32px;object-fit:contain;">
                @else
                  <span style="font-size:20px;opacity:.25;">ðŸ”²</span>
                @endif
              </div>
              <div class="flex-1">
                <p class="text-sm font-semibold" style="color:var(--navy);margin-bottom:4px;">Favicon</p>
                <p class="s-hint" style="margin-bottom:10px;">ICO, PNG, or SVG. 32 Ã— 32 px recommended. Max 512 KB.</p>
                <input type="file" name="site_favicon_file" accept="image/*,.ico" class="s-file">
                <input type="url" name="site_favicon_url" value="{{ $settings['site_favicon_url'] }}"
                  placeholder="https://example.com/favicon.ico"
                  class="s-input mt-2" style="font-size:12px;padding:7px 11px;">
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- â•”â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•— --}}
      {{-- â•‘  CONTACT                           â•‘ --}}
      {{-- â•šâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
      <div class="stab-panel" id="stab-contact" style="display:none;">
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
                  <option value="">Select stateâ€¦</option>
                  @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $st)
                    <option value="{{ $st }}" {{ $settings['contact_state'] === $st ? 'selected' : '' }}>{{ $st }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- â•”â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•— --}}
      {{-- â•‘  SOCIAL MEDIA                      â•‘ --}}
      {{-- â•šâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
      <div class="stab-panel" id="stab-social" style="display:none;">
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
                ['social_facebook',  'ðŸ“˜', 'Facebook',   'https://facebook.com/yourpage'],
                ['social_twitter',   'ð•',  'Twitter / X','https://twitter.com/yourhandle'],
                ['social_instagram', 'ðŸ“·', 'Instagram',  'https://instagram.com/yourhandle'],
                ['social_linkedin',  'ðŸ”—', 'LinkedIn',   'https://linkedin.com/company/yourname'],
                ['social_youtube',   'â–¶',  'YouTube',    'https://youtube.com/@yourchannel'],
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
      </div>

      {{-- â•”â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•— --}}
      {{-- â•‘  EMAIL / SMTP                      â•‘ --}}
      {{-- â•šâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
      <div class="stab-panel" id="stab-smtp" style="display:none;">
        <div class="s-card mb-4">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">Email / SMTP Settings</p>
              <p class="s-card-sub">Configure outgoing email delivery for notifications and alerts.</p>
            </div>
          </div>
          <div class="s-card-body">

            {{-- Server config --}}
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

            <div class="s-divider"></div>

            {{-- Authentication --}}
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

            <div class="s-divider"></div>

            {{-- Sender identity --}}
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

        {{-- SMTP test (separate form) --}}
        <div class="s-card mb-4">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">Test Email Delivery</p>
              <p class="s-card-sub">Verify your SMTP configuration by sending a test message.</p>
            </div>
            <span class="s-badge">Save settings first</span>
          </div>
          <div class="s-card-body">
            <form method="POST" action="{{ route('admin.settings.smtp-test') }}" class="flex gap-3">
              @csrf
              <input type="email" name="test_email" value="{{ auth()->user()->email }}"
                placeholder="test@example.com" class="s-input" style="flex:1;">
              <button type="submit" class="s-btn-secondary" style="flex-shrink:0;white-space:nowrap;">
                âœ‰ï¸ Send test email
              </button>
            </form>
          </div>
        </div>
      </div>

      {{-- â•”â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•— --}}
      {{-- â•‘  SMS / TWILIO                      â•‘ --}}
      {{-- â•šâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
      <div class="stab-panel" id="stab-sms" style="display:none;">

        {{-- Enable/disable toggle --}}
        <div class="s-card mb-4">
          <div class="s-card-head" style="flex-direction:row;align-items:center;justify-content:space-between;">
            <div>
              <p class="s-card-title">SMS Notifications</p>
              <p class="s-card-sub">Controls whether OTP codes and alerts are sent via SMS.</p>
            </div>
            <label class="s-toggle">
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
          <div class="s-card-body" style="display:flex;flex-direction:column;gap:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
              <div>
                <p class="s-label" style="margin-bottom:2px;">OTP via SMS</p>
                <p class="s-hint" style="margin:0;">Send the 6-digit code by text message using your Twilio number above.</p>
              </div>
              <label class="s-toggle">
                <input type="checkbox" name="otp_sms_enabled" value="1" id="otpSmsToggle"
                  {{ ($settings['otp_sms_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                <span class="s-toggle-track"><span class="s-toggle-thumb"></span></span>
                <span class="s-toggle-label" id="otpSmsLabel">
                  {{ ($settings['otp_sms_enabled'] ?? '1') === '1' ? 'Enabled' : 'Disabled' }}
                </span>
              </label>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
              <div>
                <p class="s-label" style="margin-bottom:2px;">OTP via Email</p>
                <p class="s-hint" style="margin:0;">Also (or instead) deliver the code to the user's registered email address.</p>
              </div>
              <label class="s-toggle">
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

        {{-- SMS test (separate form) --}}
        <div class="s-card mb-4">
          <div class="s-card-head" style="flex-direction:row;align-items:center;justify-content:space-between;">
            <div>
              <p class="s-card-title">Test SMS Delivery</p>
              <p class="s-card-sub">Verify your Twilio credentials by sending a test message.</p>
            </div>
            <span class="s-badge">Save settings first</span>
          </div>
          <div class="s-card-body">
            <form method="POST" action="{{ route('admin.settings.sms-test') }}" class="flex gap-3">
              @csrf
              <input type="text" name="test_phone" value="{{ auth()->user()->phone }}"
                placeholder="08012345678" class="s-input" style="flex:1;">
              <button type="submit" class="s-btn-secondary" style="flex-shrink:0;white-space:nowrap;">
                ðŸ“± Send test SMS
              </button>
            </form>
          </div>
        </div>
      </div>

      {{-- â•”â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•— --}}
      {{-- â•‘  PAYMENTS                          â•‘ --}}
      {{-- â•šâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
      <div class="stab-panel" id="stab-payments" style="display:none;">

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
                'none'        => ['ðŸš«', 'None / Disabled', 'Payments not collected'],
                'paystack'    => ['ðŸ’š', 'Paystack',        'Leading Nigerian gateway'],
                'flutterwave' => ['ðŸ¦‹', 'Flutterwave',     'Pan-African payments'],
              ] as $val => [$icon, $label, $desc])
              <label class="gw-card {{ ($settings['payment_gateway'] ?? 'none') === $val ? 'gw-active' : '' }}">
                <input type="radio" name="payment_gateway" value="{{ $val }}"
                  {{ ($settings['payment_gateway'] ?? 'none') === $val ? 'checked' : '' }}
                  style="display:none;">
                <span style="font-size:34px;display:block;margin-bottom:10px;">{{ $icon }}</span>
                <span style="display:block;font-size:14px;font-weight:700;margin-bottom:4px;">{{ $label }}</span>
                <span style="display:block;font-size:12px;opacity:.7;">{{ $desc }}</span>
              </label>
              @endforeach
            </div>
          </div>
        </div>

        {{-- Paystack keys --}}
        <div class="s-card mb-4" id="paystack-section"
          style="{{ ($settings['payment_gateway'] ?? 'none') !== 'paystack' ? 'display:none;' : '' }}">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">ðŸ’š Paystack API Keys</p>
              <p class="s-card-sub">Dashboard â†’ Settings â†’ API Keys &amp; Webhooks.</p>
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
                <p class="s-hint">Keep secret â€” server-side only. Leave blank to preserve current value.</p>
              </div>
            </div>
            <div class="s-warn-box mt-4">
              <span>âš ï¸</span>
              <span>Never use test keys in production. Switch to live keys before going live.</span>
            </div>
          </div>
        </div>

        {{-- Flutterwave keys --}}
        <div class="s-card mb-4" id="flutterwave-section"
          style="{{ ($settings['payment_gateway'] ?? 'none') !== 'flutterwave' ? 'display:none;' : '' }}">
          <div class="s-card-head">
            <div>
              <p class="s-card-title">ðŸ¦‹ Flutterwave API Keys</p>
              <p class="s-card-sub">Dashboard â†’ Settings â†’ API Keys.</p>
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
              <span>âš ï¸</span>
              <span>Never use test keys in production. Switch to live keys before going live.</span>
            </div>
          </div>
        </div>
      </div>

      {{-- â•”â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•— --}}
      {{-- â•‘  LANDING PAGE                      â•‘ --}}
      {{-- â•šâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
      <div class="stab-panel" id="stab-landing" style="display:none;">
        <div class="s-card mb-4">
          <div class="s-card-head" style="flex-direction:row;align-items:center;justify-content:space-between;">
            <div>
              <p class="s-card-title">Landing Page Content</p>
              <p class="s-card-sub">Customise the public-facing homepage hero section.</p>
            </div>
            <a href="{{ url('/') }}" target="_blank" class="s-btn-secondary" style="font-size:12px;padding:6px 12px;">
              ðŸ‘€ Preview â†’
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
                  placeholder="Brief description shown below the headlineâ€¦">{{ $settings['hero_subtext'] }}</textarea>
              </div>
              <div>
                <label class="s-label">Primary CTA Button</label>
                <div class="relative">
                  <input type="text" name="hero_cta_primary" value="{{ $settings['hero_cta_primary'] }}"
                    placeholder="Get started free" class="s-input">
                </div>
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
      </div>

      {{-- â”€â”€ Sticky save bar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
      <div class="s-save-bar">
        <p class="text-sm text-slate-500" style="font-size:12px;">All sections save together. Save before switching tabs.</p>
        <button type="submit" class="s-btn-primary">
          ðŸ’¾ Save all settings
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Settings page CSS is in resources/css/app.css, served via Vite --}}

<script nonce=”{{ $cspNonce }}”>
// Tab navigation
const navBtns = document.querySelectorAll('.snav-btn');
const panels  = document.querySelectorAll('.stab-panel');

function switchTab(key) {
  navBtns.forEach(b => b.classList.toggle('snav-active', b.dataset.stab === key));
  panels.forEach(p => {
    p.style.display = (p.id === 'stab-' + key) ? '' : 'none';
  });
}

navBtns.forEach(btn => {
  btn.addEventListener('click', () => switchTab(btn.dataset.stab));
});

// SMS toggle label
const smsToggle = document.getElementById('smsToggle');
const smsLabel  = document.getElementById('smsLabel');
if (smsToggle) {
  smsToggle.addEventListener('change', function() {
    smsLabel.textContent = this.checked ? 'Enabled' : 'Disabled';
  });
}

// OTP channel toggle labels
[['otpSmsToggle', 'otpSmsLabel'], ['otpEmailToggle', 'otpEmailLabel']].forEach(function([tid, lid]) {
  var t = document.getElementById(tid), l = document.getElementById(lid);
  if (t) t.addEventListener('change', function() { l.textContent = this.checked ? 'Enabled' : 'Disabled'; });
});

// Payment gateway card selector
document.querySelectorAll('.gw-card').forEach(card => {
  card.addEventListener('click', function() {
    document.querySelectorAll('.gw-card').forEach(c => c.classList.remove('gw-active'));
    this.classList.add('gw-active');
    const val = this.querySelector('input').value;
    document.getElementById('paystack-section').style.display    = val === 'paystack'    ? '' : 'none';
    document.getElementById('flutterwave-section').style.display = val === 'flutterwave' ? '' : 'none';
  });
});
</script>
@endsection
