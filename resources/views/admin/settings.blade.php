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

<form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm">
  @csrf

  {{-- Tab nav --}}
  <div class="settings-tabs">
    <button type="button" class="stab active" data-tab="general">⚙️ General</button>
    <button type="button" class="stab" data-tab="contact">📞 Contact</button>
    <button type="button" class="stab" data-tab="social">🔗 Social Media</button>
    <button type="button" class="stab" data-tab="smtp">✉️ Email / SMTP</button>
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
            <label>Logo URL</label>
            <input type="url" name="site_logo_url" value="{{ $settings['site_logo_url'] }}" placeholder="https://example.com/logo.png">
            <div class="form-hint">Direct URL to your logo image (PNG or SVG recommended, transparent background)</div>
          </div>
          @if($settings['site_logo_url'])
          <div class="form-field span-2">
            <label>Current Logo Preview</label>
            <div style="padding: 16px; background: #F8FAFC; border: 1.5px solid var(--border); border-radius: 8px; display: inline-flex; gap: 20px; align-items: center;">
              <img src="{{ $settings['site_logo_url'] }}" alt="Logo" style="height: 48px; object-fit: contain;">
              <span style="font-size: 12px; color: var(--text-2);">{{ $settings['site_logo_url'] }}</span>
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
  flex: 1; min-width: 120px;
  padding: 9px 16px; border-radius: 8px;
  font-size: 13px; font-weight: 600;
  border: none; background: none;
  color: var(--text-2); cursor: pointer;
  transition: all 0.15s; white-space: nowrap;
  font-family: inherit;
}
.stab:hover { background: var(--navy-lt); color: var(--navy); }
.stab.active { background: var(--navy); color: #fff; }
</style>

<script>
document.querySelectorAll('.stab').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.stab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.stab-panel').forEach(p => p.style.display = 'none');
    this.classList.add('active');
    document.getElementById('tab-' + this.dataset.tab).style.display = '';
  });
});
</script>
@endsection
