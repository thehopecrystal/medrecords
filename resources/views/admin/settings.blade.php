@extends('layouts.app')
@section('title', 'Site Settings')
@section('topbar-title', 'Site Settings')

@section('content')
<div class="page-header-row">
  <div>
    <h1>Site Settings</h1>
    <p>Configure your website, contact details, social media and email settings</p>
  </div>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm" enctype="multipart/form-data">
  @csrf

  {{-- Tab nav --}}
  <div class="settings-tabs">
    <button type="button" class="stab active" data-tab="general">⚙️ General</button>
    <button type="button" class="stab" data-tab="contact">📞 Contact</button>
    <button type="button" class="stab" data-tab="social">🔗 Social Media</button>
    <button type="button" class="stab" data-tab="smtp">✉️ Email / SMTP</button>
    <button type="button" class="stab" data-tab="sms">📱 SMS / Twilio</button>
    <button type="button" class="stab" data-tab="payments">💳 Payments</button>
    <button type="button" class="stab" data-tab="landing">🌐 Landing Page</button>
  </div>

  {{-- ── Tab: General ────────────────────────────── --}}
  <div class="stab-panel active" id="tab-general">
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">General Settings</div>
          <div class="card-sub">Core identity and branding for your platform</div>
        </div>
      </div>
      <div class="card-body">
        <div class="form-grid">
          <div class="form-field">
            <label>Site Name *</label>
            <input type="text" name="site_name" value="{{ $settings['site_name'] }}" required placeholder="MedRecords">
            <div class="form-hint">Appears in browser tabs, emails, and throughout the app</div>
          </div>
          <div class="form-field">
            <label>Tagline</label>
            <input type="text" name="site_tagline" value="{{ $settings['site_tagline'] }}" placeholder="Your health record, always with you">
          </div>
          <div class="form-field span-2">
            <label>Upload Logo</label>
            <input type="file" name="site_logo_file" accept="image/*">
            <div class="form-hint">PNG or SVG recommended, transparent background. Max 2 MB. Uploading a file overrides the URL below.</div>
          </div>
          @if($settings['site_logo_url'])
          <div class="form-field span-2">
            <label>Current Logo</label>
            <div style="padding: 16px; background: #F8FAFC; border: 1.5px solid var(--border); border-radius: 8px; display: inline-flex; gap: 20px; align-items: center;">
              <img src="{{ $settings['site_logo_url'] }}" alt="Logo" style="height: 48px; object-fit: contain;">
              <span style="font-size: 12px; color: var(--text-2);">{{ $settings['site_logo_url'] }}</span>
            </div>
          </div>
          @endif
          <div class="form-field span-2">
            <label>Logo URL</label>
            <input type="url" name="site_logo_url" value="{{ $settings['site_logo_url'] }}" placeholder="https://example.com/logo.png">
            <div class="form-hint">Or paste a direct URL (upload above takes priority if both provided)</div>
          </div>
          <div class="form-field span-2">
            <label>Upload Favicon</label>
            <input type="file" name="site_favicon_file" accept="image/*,.ico">
            <div class="form-hint">Shown in browser tabs. ICO, PNG, or SVG. 32×32 px recommended. Max 512 KB.</div>
          </div>
          @if($settings['site_favicon_url'])
          <div class="form-field span-2">
            <label>Current Favicon</label>
            <div style="padding: 12px 16px; background: #F8FAFC; border: 1.5px solid var(--border); border-radius: 8px; display: inline-flex; gap: 16px; align-items: center;">
              <img src="{{ $settings['site_favicon_url'] }}" alt="Favicon" style="height: 32px; width: 32px; object-fit: contain;">
              <span style="font-size: 12px; color: var(--text-2);">{{ $settings['site_favicon_url'] }}</span>
            </div>
          </div>
          @endif
          <div class="form-field span-2">
            <label>Footer About Text</label>
            <textarea name="footer_about" placeholder="Short description of your platform for the website footer…">{{ $settings['footer_about'] }}</textarea>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Tab: Contact ─────────────────────────────── --}}
  <div class="stab-panel" id="tab-contact" style="display:none;">
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Contact Information</div>
          <div class="card-sub">Displayed on the landing page and in emails</div>
        </div>
      </div>
      <div class="card-body">
        <div class="form-grid">
          <div class="form-field">
            <label>Primary Phone Number</label>
            <input type="text" name="contact_phone_1" value="{{ $settings['contact_phone_1'] }}" placeholder="+234 (0) 800 000 0000">
          </div>
          <div class="form-field">
            <label>Secondary Phone Number</label>
            <input type="text" name="contact_phone_2" value="{{ $settings['contact_phone_2'] }}" placeholder="+234 (0) 700 000 0000">
          </div>
          <div class="form-field span-2">
            <label>Email Address</label>
            <input type="email" name="contact_email" value="{{ $settings['contact_email'] }}" placeholder="hello@yoursite.com">
          </div>
          <div class="form-field span-2">
            <label>Street Address</label>
            <input type="text" name="contact_address" value="{{ $settings['contact_address'] }}" placeholder="14 Adeola Odeku Street">
          </div>
          <div class="form-field">
            <label>City</label>
            <input type="text" name="contact_city" value="{{ $settings['contact_city'] }}" placeholder="Victoria Island">
          </div>
          <div class="form-field">
            <label>State</label>
            <select name="contact_state">
              <option value="">Select state…</option>
              @foreach(['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'] as $st)
                <option value="{{ $st }}" {{ $settings['contact_state'] === $st ? 'selected' : '' }}>{{ $st }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Tab: Social Media ────────────────────────── --}}
  <div class="stab-panel" id="tab-social" style="display:none;">
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Social Media Links</div>
          <div class="card-sub">Leave blank to hide the icon on the website footer</div>
        </div>
      </div>
      <div class="card-body">
        <div class="form-grid">
          <div class="form-field">
            <label>📘 Facebook</label>
            <input type="url" name="social_facebook" value="{{ $settings['social_facebook'] }}" placeholder="https://facebook.com/yourpage">
          </div>
          <div class="form-field">
            <label>𝕏 Twitter / X</label>
            <input type="url" name="social_twitter" value="{{ $settings['social_twitter'] }}" placeholder="https://twitter.com/yourhandle">
          </div>
          <div class="form-field">
            <label>📷 Instagram</label>
            <input type="url" name="social_instagram" value="{{ $settings['social_instagram'] }}" placeholder="https://instagram.com/yourhandle">
          </div>
          <div class="form-field">
            <label>🔗 LinkedIn</label>
            <input type="url" name="social_linkedin" value="{{ $settings['social_linkedin'] }}" placeholder="https://linkedin.com/company/yourname">
          </div>
          <div class="form-field">
            <label>▶ YouTube</label>
            <input type="url" name="social_youtube" value="{{ $settings['social_youtube'] ?? '' }}" placeholder="https://youtube.com/@yourchannel">
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Tab: SMTP ────────────────────────────────── --}}
  <div class="stab-panel" id="tab-smtp" style="display:none;">
    <div class="card" style="margin-bottom: 16px;">
      <div class="card-header">
        <div>
          <div class="card-title">Email / SMTP Settings</div>
          <div class="card-sub">Configure outgoing email delivery. Used for notifications and alerts.</div>
        </div>
      </div>
      <div class="card-body">
        <div class="form-section">
          <div class="form-section-title">Server Configuration</div>
          <div class="form-grid">
            <div class="form-field">
              <label>SMTP Host</label>
              <input type="text" name="smtp_host" value="{{ $settings['smtp_host'] }}" placeholder="smtp.gmail.com">
            </div>
            <div class="form-field">
              <label>Port</label>
              <input type="number" name="smtp_port" value="{{ $settings['smtp_port'] }}" placeholder="587">
            </div>
            <div class="form-field">
              <label>Encryption</label>
              <select name="smtp_encryption">
                <option value="tls"  {{ $settings['smtp_encryption'] === 'tls'  ? 'selected' : '' }}>TLS (recommended)</option>
                <option value="ssl"  {{ $settings['smtp_encryption'] === 'ssl'  ? 'selected' : '' }}>SSL</option>
                <option value="none" {{ $settings['smtp_encryption'] === 'none' ? 'selected' : '' }}>None</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Authentication</div>
          <div class="form-grid">
            <div class="form-field">
              <label>Username</label>
              <input type="text" name="smtp_username" value="{{ $settings['smtp_username'] }}" placeholder="your@email.com" autocomplete="off">
            </div>
            <div class="form-field">
              <label>Password</label>
              <input type="password" name="smtp_password" placeholder="Leave blank to keep current" autocomplete="new-password">
              <div class="form-hint">Leave blank to keep the existing password</div>
            </div>
          </div>
        </div>
        <div class="form-section" style="margin-bottom: 0;">
          <div class="form-section-title">Sender Identity</div>
          <div class="form-grid">
            <div class="form-field">
              <label>From Name</label>
              <input type="text" name="smtp_from_name" value="{{ $settings['smtp_from_name'] }}" placeholder="MedRecords">
            </div>
            <div class="form-field">
              <label>From Email</label>
              <input type="email" name="smtp_from_email" value="{{ $settings['smtp_from_email'] }}" placeholder="noreply@medrecords.ng">
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- SMTP test --}}
    <div class="card">
      <div class="card-header"><div><div class="card-title">Test Email</div><div class="card-sub">Send a test email to verify your SMTP settings are working</div></div></div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.smtp-test') }}" style="display:flex;gap:10px;align-items:flex-end;">
          @csrf
          <div class="form-field" style="flex:1;">
            <label>Send test to</label>
            <input type="email" name="test_email" value="{{ auth()->user()->email }}" placeholder="test@example.com">
          </div>
          <button type="submit" class="btn btn-secondary">Send test email</button>
        </form>
        <div class="alert navy" style="margin-top: 14px; margin-bottom: 0;">
          <span>ℹ</span>
          <span>Save your SMTP settings first before sending a test email.</span>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Tab: SMS / Twilio ──────────────────────────── --}}
  <div class="stab-panel" id="tab-sms" style="display:none;">
    <div class="card" style="margin-bottom:16px;">
      <div class="card-header">
        <div>
          <div class="card-title">SMS Notifications</div>
          <div class="card-sub">Controls whether OTP codes and alerts are sent via SMS. Requires Twilio credentials below.</div>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="sms_enabled" value="1" {{ ($settings['sms_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
          <span class="toggle-track"><span class="toggle-thumb"></span></span>
          <span class="toggle-label" id="smsLabel">{{ ($settings['sms_enabled'] ?? '1') === '1' ? 'Enabled' : 'Disabled' }}</span>
        </label>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Twilio Credentials</div>
          <div class="card-sub">Used to send SMS messages. Get these from your Twilio console.</div>
        </div>
      </div>
      <div class="card-body">
        <div class="form-grid">
          <div class="form-field">
            <label>Account SID</label>
            <input type="text" name="twilio_account_sid"
                   value="{{ $settings['twilio_account_sid'] }}"
                   placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                   autocomplete="off">
            <div class="form-hint">Found on your Twilio Console dashboard</div>
          </div>
          <div class="form-field">
            <label>Auth Token</label>
            <input type="password" name="twilio_auth_token"
                   placeholder="Leave blank to keep current"
                   autocomplete="new-password">
            <div class="form-hint">Leave blank to keep the existing token</div>
          </div>
          <div class="form-field span-2">
            <label>From Number</label>
            <input type="text" name="twilio_from_number"
                   value="{{ $settings['twilio_from_number'] }}"
                   placeholder="+12345678901">
            <div class="form-hint">Your Twilio phone number in E.164 format (e.g. +12345678901). Nigerian numbers will be converted automatically for delivery.</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card" style="margin-top:16px;">
      <div class="card-header"><div><div class="card-title">Test SMS</div><div class="card-sub">Send a test message to verify your Twilio settings</div></div></div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.sms-test') }}" style="display:flex;gap:10px;align-items:flex-end;">
          @csrf
          <div class="form-field" style="flex:1;">
            <label>Send test to phone number</label>
            <input type="text" name="test_phone" placeholder="08012345678" value="{{ auth()->user()->phone }}">
          </div>
          <button type="submit" class="btn btn-secondary">Send test SMS</button>
        </form>
        <div class="alert navy" style="margin-top:14px;margin-bottom:0;">
          <span>ℹ</span>
          <span>Save your Twilio credentials first, then use this to confirm messages are delivered.</span>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Tab: Payments ────────────────────────────── --}}
  <div class="stab-panel" id="tab-payments" style="display:none;">
    <div class="card" style="margin-bottom:16px;">
      <div class="card-header">
        <div>
          <div class="card-title">Active Payment Gateway</div>
          <div class="card-sub">Select which gateway to use for subscription and payment collection</div>
        </div>
      </div>
      <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
          @foreach(['none' => ['label'=>'None / Disabled','icon'=>'🚫','desc'=>'Payments are not collected'],'paystack' => ['label'=>'Paystack','icon'=>'💚','desc'=>'Leading Nigerian gateway'],'flutterwave' => ['label'=>'Flutterwave','icon'=>'🦋','desc'=>'Pan-African payments']] as $val => $gw)
          <label class="gw-card {{ ($settings['payment_gateway'] ?? 'none') === $val ? 'active' : '' }}">
            <input type="radio" name="payment_gateway" value="{{ $val }}" {{ ($settings['payment_gateway'] ?? 'none') === $val ? 'checked' : '' }} style="display:none;">
            <span style="font-size:28px;display:block;margin-bottom:6px;">{{ $gw['icon'] }}</span>
            <span style="font-weight:700;font-size:14px;">{{ $gw['label'] }}</span>
            <span style="font-size:12px;color:inherit;opacity:.75;">{{ $gw['desc'] }}</span>
          </label>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Paystack --}}
    <div class="card" id="paystack-section" style="margin-bottom:16px;{{ ($settings['payment_gateway'] ?? 'none') !== 'paystack' ? 'display:none;' : '' }}">
      <div class="card-header">
        <div>
          <div class="card-title">💚 Paystack Keys</div>
          <div class="card-sub">Available from your Paystack Dashboard → Settings → API Keys</div>
        </div>
      </div>
      <div class="card-body">
        <div class="form-grid">
          <div class="form-field">
            <label>Public Key</label>
            <input type="text" name="paystack_public_key"
                   value="{{ $settings['paystack_public_key'] }}"
                   placeholder="pk_live_xxxxxxxxxxxxxxxxxxxxxxxx"
                   autocomplete="off">
            <div class="form-hint">Safe to expose in frontend JavaScript</div>
          </div>
          <div class="form-field">
            <label>Secret Key</label>
            <input type="password" name="paystack_secret_key"
                   placeholder="Leave blank to keep current"
                   autocomplete="new-password">
            <div class="form-hint">Keep secret — used server-side only. Leave blank to keep existing.</div>
          </div>
        </div>
        <div class="alert amber" style="margin-top:8px;margin-bottom:0;">
          <span>⚠️</span>
          <span>Never use test keys in production. Switch to live keys before going live.</span>
        </div>
      </div>
    </div>

    {{-- Flutterwave --}}
    <div class="card" id="flutterwave-section" style="{{ ($settings['payment_gateway'] ?? 'none') !== 'flutterwave' ? 'display:none;' : '' }}">
      <div class="card-header">
        <div>
          <div class="card-title">🦋 Flutterwave Keys</div>
          <div class="card-sub">Available from your Flutterwave Dashboard → Settings → API Keys</div>
        </div>
      </div>
      <div class="card-body">
        <div class="form-grid">
          <div class="form-field">
            <label>Public Key</label>
            <input type="text" name="flutterwave_public_key"
                   value="{{ $settings['flutterwave_public_key'] }}"
                   placeholder="FLWPUBK-xxxxxxxxxxxxxxxxxxxxxxxx-X"
                   autocomplete="off">
          </div>
          <div class="form-field">
            <label>Secret Key</label>
            <input type="password" name="flutterwave_secret_key"
                   placeholder="Leave blank to keep current"
                   autocomplete="new-password">
            <div class="form-hint">Leave blank to keep the existing key.</div>
          </div>
        </div>
        <div class="alert amber" style="margin-top:8px;margin-bottom:0;">
          <span>⚠️</span>
          <span>Never use test keys in production. Switch to live keys before going live.</span>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Tab: Landing Page ────────────────────────── --}}
  <div class="stab-panel" id="tab-landing" style="display:none;">
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Landing Page Content</div>
          <div class="card-sub">Customise the public-facing homepage</div>
        </div>
      </div>
      <div class="card-body">
        <div class="form-grid">
          <div class="form-field span-2">
            <label>Hero Headline</label>
            <input type="text" name="hero_headline" value="{{ $settings['hero_headline'] }}" placeholder="Your health record, always with you">
            <div class="form-hint">Use &lt;br&gt; for a line break. The second line is highlighted in green on the page.</div>
          </div>
          <div class="form-field span-2">
            <label>Hero Subtext</label>
            <textarea name="hero_subtext" placeholder="Brief description shown below the headline…">{{ $settings['hero_subtext'] }}</textarea>
          </div>
          <div class="form-field">
            <label>Primary CTA Button Text</label>
            <input type="text" name="hero_cta_primary" value="{{ $settings['hero_cta_primary'] }}" placeholder="Get started free">
          </div>
          <div class="form-field">
            <label>Secondary CTA Button Text</label>
            <input type="text" name="hero_cta_secondary" value="{{ $settings['hero_cta_secondary'] }}" placeholder="Learn more">
          </div>
        </div>
        <div class="alert navy" style="margin-top: 16px; margin-bottom: 0;">
          <span>👀</span>
          <span>Preview your landing page at <a href="{{ url('/') }}" target="_blank" style="color: var(--navy); font-weight: 600;">{{ url('/') }}</a></span>
        </div>
      </div>
    </div>
  </div>

  {{-- Save button --}}
  <div style="display:flex;justify-content:flex-end;margin-top:20px;">
    <button type="submit" class="btn btn-primary btn-lg">💾 Save all settings</button>
  </div>
</form>

<style>
.settings-tabs {
  display: flex; gap: 4px;
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 6px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.stab {
  flex: 1; min-width: 100px;
  padding: 9px 14px; border-radius: 8px;
  font-size: 13px; font-weight: 600;
  border: none; background: none;
  color: var(--text-2); cursor: pointer;
  transition: all 0.15s; white-space: nowrap;
  font-family: inherit;
}
.stab:hover { background: var(--navy-lt); color: var(--navy); }
.stab.active { background: var(--navy); color: #fff; }

/* Toggle switch */
.toggle-switch { display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none; }
.toggle-switch input { display: none; }
.toggle-track {
  width: 44px; height: 24px; border-radius: 12px;
  background: #cbd5e1; transition: background 0.2s; position: relative; flex-shrink: 0;
}
.toggle-switch input:checked + .toggle-track { background: var(--navy); }
.toggle-thumb {
  position: absolute; top: 3px; left: 3px;
  width: 18px; height: 18px; border-radius: 50%;
  background: #fff; transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.toggle-switch input:checked + .toggle-track .toggle-thumb { transform: translateX(20px); }
.toggle-label { font-size: 13px; font-weight: 600; color: var(--text-2); min-width: 56px; }

/* Gateway cards */
.gw-card {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 18px 10px; border: 2px solid var(--border); border-radius: 12px;
  cursor: pointer; transition: all 0.15s; text-align: center; gap: 4px;
}
.gw-card:hover { border-color: var(--navy); background: var(--navy-lt); }
.gw-card.active { border-color: var(--navy); background: var(--navy); color: #fff; }
</style>

<script>
// Tab switching
document.querySelectorAll('.stab').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.stab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.stab-panel').forEach(p => p.style.display = 'none');
    this.classList.add('active');
    document.getElementById('tab-' + this.dataset.tab).style.display = '';
  });
});

// SMS toggle label
var smsToggle = document.querySelector('input[name="sms_enabled"]');
var smsLabel  = document.getElementById('smsLabel');
if (smsToggle) {
  smsToggle.addEventListener('change', function() {
    smsLabel.textContent = this.checked ? 'Enabled' : 'Disabled';
  });
}

// Payment gateway card selector + section visibility
document.querySelectorAll('.gw-card').forEach(function(card) {
  card.addEventListener('click', function() {
    document.querySelectorAll('.gw-card').forEach(c => c.classList.remove('active'));
    this.classList.add('active');
    var val = this.querySelector('input').value;
    document.getElementById('paystack-section').style.display    = val === 'paystack'    ? '' : 'none';
    document.getElementById('flutterwave-section').style.display = val === 'flutterwave' ? '' : 'none';
  });
});
</script>
@endsection
