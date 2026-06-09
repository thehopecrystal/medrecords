<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
  use App\Models\SiteSetting;
  SiteSetting::loadAll();
  $siteName    = SiteSetting::get('site_name', 'MedRecords');
  $siteTagline = SiteSetting::get('site_tagline', 'Your health record, always with you');
  $logoUrl     = SiteSetting::get('site_logo_url', '');
  $heroHead    = SiteSetting::get('hero_headline', 'Your health record,<br>always with you');
  $heroSub     = SiteSetting::get('hero_subtext', 'MedRecords gives every Nigerian a secure, patient-owned electronic health record.');
  $heroCta1    = SiteSetting::get('hero_cta_primary', 'Get started free');
  $heroCta2    = SiteSetting::get('hero_cta_secondary', 'Learn more');
  $phone1      = SiteSetting::get('contact_phone_1');
  $phone2      = SiteSetting::get('contact_phone_2');
  $email       = SiteSetting::get('contact_email');
  $address     = SiteSetting::get('contact_address');
  $city        = SiteSetting::get('contact_city');
  $state       = SiteSetting::get('contact_state');
  $fb          = SiteSetting::get('social_facebook');
  $tw          = SiteSetting::get('social_twitter');
  $ig          = SiteSetting::get('social_instagram');
  $li          = SiteSetting::get('social_linkedin');
  $footerAbout = SiteSetting::get('footer_about', "Nigeria's patient-owned electronic health record platform.");
  $plans = \App\Models\Plan::active()->get();
@endphp
<title>{{ $siteName }} — {{ $siteTagline }}</title>
<style nonce="{{ $cspNonce }}">
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Inter", Roboto, Arial, sans-serif;
  color: #0F172A; line-height: 1.6;
}
a { text-decoration: none; color: inherit; }
:root {
  --navy:    #1E3A5F;
  --navy-dk: #0D1F35;
  --green:   #2C5F2D;
  --red:     #B23A48;
  --amber:   #B8860B;
  --text:    #0F172A;
  --text-2:  #475569;
  --text-3:  #94A3B8;
  --border:  #E2E8F0;
  --bg:      #F8FAFC;
}

/* ─── Header ────────────────────────────────────── */
.header {
  position: sticky; top: 0; z-index: 100;
  background: rgba(255,255,255,0.92);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--border);
}
.header-inner {
  max-width: 1160px; margin: 0 auto;
  padding: 0 24px;
  height: 64px;
  display: flex; align-items: center; gap: 32px;
}
.logo {
  display: flex; align-items: center; gap: 10px;
  font-size: 20px; font-weight: 800; color: var(--navy);
  letter-spacing: -0.03em; flex-shrink: 0;
}
.logo-icon {
  width: 36px; height: 36px; border-radius: 10px;
  background: linear-gradient(135deg, var(--navy) 0%, var(--green) 100%);
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; font-weight: 900; color: #fff;
}
.logo img { width: 36px; height: 36px; border-radius: 10px; object-fit: contain; }
.header-nav { display: flex; gap: 4px; flex: 1; }
.nav-link {
  padding: 8px 14px; border-radius: 8px;
  font-size: 14px; font-weight: 500; color: var(--text-2);
  transition: background 0.15s, color 0.15s;
}
.nav-link:hover { background: #F1F5F9; color: var(--navy); }
.header-cta { display: flex; gap: 8px; flex-shrink: 0; }
.btn-outline {
  padding: 9px 18px; border-radius: 8px;
  font-size: 13px; font-weight: 600;
  border: 1.5px solid var(--border); color: var(--text);
  transition: all 0.15s; background: #fff;
}
.btn-outline:hover { border-color: var(--navy); color: var(--navy); }
.btn-solid {
  padding: 9px 18px; border-radius: 8px;
  font-size: 13px; font-weight: 700;
  background: var(--navy); color: #fff;
  transition: all 0.15s;
}
.btn-solid:hover { background: #163050; }

/* ─── Hero ──────────────────────────────────────── */
.hero {
  background: linear-gradient(160deg, #0D1F35 0%, #1E3A5F 45%, #1A4A2E 100%);
  color: #fff;
  padding: 100px 24px 90px;
  text-align: center;
  position: relative; overflow: hidden;
}
.hero::before {
  content: '';
  position: absolute; inset: 0;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.hero-inner { max-width: 800px; margin: 0 auto; position: relative; }
.hero-badge {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.2);
  padding: 6px 14px; border-radius: 20px;
  font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.85);
  margin-bottom: 28px;
}
.hero h1 {
  font-size: 54px; font-weight: 900;
  line-height: 1.1; letter-spacing: -0.03em;
  margin-bottom: 22px;
}
.hero h1 span { color: #6EE4A8; }
.hero-sub {
  font-size: 18px; color: rgba(255,255,255,0.75);
  line-height: 1.65; max-width: 600px; margin: 0 auto 36px;
}
.hero-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.btn-hero-primary {
  padding: 14px 32px; border-radius: 10px;
  font-size: 16px; font-weight: 700;
  background: #fff; color: var(--navy);
  transition: all 0.15s;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}
.btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.25); }
.btn-hero-secondary {
  padding: 14px 32px; border-radius: 10px;
  font-size: 16px; font-weight: 600;
  background: rgba(255,255,255,0.12);
  border: 1.5px solid rgba(255,255,255,0.3);
  color: #fff; transition: all 0.15s;
}
.btn-hero-secondary:hover { background: rgba(255,255,255,0.18); }

/* Hero illustration cards */
.hero-cards {
  display: flex; gap: 14px; justify-content: center;
  margin-top: 56px; flex-wrap: wrap;
}
.hero-card {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.15);
  backdrop-filter: blur(10px);
  border-radius: 14px; padding: 16px 20px;
  text-align: left; min-width: 160px;
}
.hero-card .hc-icon { font-size: 24px; margin-bottom: 8px; }
.hero-card .hc-value { font-size: 22px; font-weight: 800; color: #6EE4A8; }
.hero-card .hc-label { font-size: 12px; color: rgba(255,255,255,0.6); margin-top: 2px; }

/* ─── Section wrapper ───────────────────────────── */
.section { padding: 90px 24px; }
.section-inner { max-width: 1160px; margin: 0 auto; }
.section-label {
  font-size: 12px; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.1em; color: var(--green); margin-bottom: 10px;
}
.section-title {
  font-size: 38px; font-weight: 900; color: var(--navy);
  line-height: 1.15; letter-spacing: -0.02em; margin-bottom: 14px;
}
.section-sub { font-size: 16px; color: var(--text-2); max-width: 560px; line-height: 1.6; }
.section.bg-light { background: var(--bg); }
.section.bg-dark  { background: var(--navy-dk); color: #fff; }
.section.bg-dark .section-title { color: #fff; }
.section.bg-dark .section-sub   { color: rgba(255,255,255,0.7); }

/* ─── Features grid ─────────────────────────────── */
.features-grid {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 24px; margin-top: 52px;
}
.feature-card {
  background: var(--bg); border-radius: 16px;
  padding: 28px 24px; border: 1px solid var(--border);
  transition: transform 0.2s, box-shadow 0.2s;
}
.feature-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(15,23,42,0.1); }
.feature-icon {
  width: 52px; height: 52px; border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 26px; margin-bottom: 18px;
}
.fi-navy  { background: #EEF3FA; }
.fi-green { background: #EDF7ED; }
.fi-amber { background: #FEF9E7; }
.fi-red   { background: #FBEAEC; }
.feature-title { font-size: 16px; font-weight: 700; color: var(--navy); margin-bottom: 8px; }
.feature-desc  { font-size: 13px; color: var(--text-2); line-height: 1.6; }

/* ─── How it works ──────────────────────────────── */
.steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; margin-top: 52px; }
.step { text-align: center; }
.step-num {
  width: 56px; height: 56px; border-radius: 50%;
  background: var(--navy); color: #fff;
  font-size: 22px; font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 20px;
}
.step-title { font-size: 17px; font-weight: 700; color: var(--navy); margin-bottom: 8px; }
.step-desc  { font-size: 13px; color: var(--text-2); line-height: 1.6; }
.steps-connector {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 32px; margin-top: -40px; margin-bottom: 0;
  pointer-events: none;
}
.step-line { text-align: center; padding-top: 28px; color: var(--border); font-size: 28px; }

/* ─── Stats bar ─────────────────────────────────── */
.stats-bar {
  display: grid; grid-template-columns: repeat(4, 1fr);
  gap: 0; margin-top: 52px;
}
.stat-item {
  text-align: center; padding: 32px 16px;
  border-right: 1px solid rgba(255,255,255,0.1);
}
.stat-item:last-child { border-right: 0; }
.stat-item .sv { font-size: 42px; font-weight: 900; color: #6EE4A8; line-height: 1; }
.stat-item .sl { font-size: 13px; color: rgba(255,255,255,0.65); margin-top: 6px; }

/* ─── Testimonials ──────────────────────────────── */
.testimonials-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 52px; }
.testimonial-card {
  background: #fff; border-radius: 16px;
  padding: 28px 24px; border: 1px solid var(--border);
  box-shadow: 0 2px 8px rgba(15,23,42,0.04);
}
.testimonial-quote { font-size: 14px; color: var(--text-2); line-height: 1.7; margin-bottom: 20px; font-style: italic; }
.testimonial-author { display: flex; align-items: center; gap: 12px; }
.testimonial-avatar {
  width: 40px; height: 40px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 700; color: #fff;
}
.testimonial-name { font-size: 14px; font-weight: 700; }
.testimonial-role { font-size: 12px; color: var(--text-3); }
.stars { color: #F59E0B; font-size: 13px; margin-bottom: 12px; }

/* ─── Pricing ───────────────────────────────────── */
.pricing-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 24px; margin-top: 52px; align-items: start;
}
.pricing-card {
  background: #fff; border-radius: 20px;
  border: 1.5px solid var(--border);
  padding: 32px 28px;
  position: relative; overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}
.pricing-card:hover { transform: translateY(-4px); box-shadow: 0 16px 48px rgba(15,23,42,0.12); }
.pricing-card.featured {
  border-color: var(--navy);
  box-shadow: 0 8px 32px rgba(30,58,95,0.15);
}
.pricing-badge {
  position: absolute; top: 16px; right: -24px;
  background: var(--navy); color: #fff;
  font-size: 10px; font-weight: 700; letter-spacing: 0.04em;
  padding: 4px 32px; transform: rotate(35deg);
}
.pricing-name  { font-size: 18px; font-weight: 800; color: var(--navy); margin-bottom: 6px; }
.pricing-price { font-size: 36px; font-weight: 900; color: var(--navy); line-height: 1; margin: 16px 0 6px; }
.pricing-price .period { font-size: 14px; font-weight: 500; color: var(--text-2); }
.pricing-desc  { font-size: 13px; color: var(--text-2); margin-bottom: 22px; min-height: 36px; line-height: 1.5; }
.pricing-features { list-style: none; margin: 0 0 28px; padding: 0; }
.pricing-features li {
  display: flex; align-items: flex-start; gap: 8px;
  font-size: 13px; color: var(--text-2); padding: 5px 0;
  border-bottom: 1px solid #F8FAFC;
}
.pricing-features li:last-child { border-bottom: 0; }
.pricing-features .check { color: var(--green); font-size: 13px; font-weight: 700; flex-shrink: 0; margin-top: 1px; }
.pricing-cta {
  display: block; text-align: center;
  padding: 12px 24px; border-radius: 10px;
  font-size: 14px; font-weight: 700;
  background: var(--navy); color: #fff;
  transition: all 0.15s;
}
.pricing-cta:hover { background: #163050; }
.pricing-cta.free  { background: var(--green); }
.pricing-cta.free:hover { filter: brightness(1.08); }
.pricing-cta.contact { background: var(--text-2); }
.pricing-cta.contact:hover { background: var(--text); }
@media (max-width: 900px) {
  .pricing-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 600px) {
  .pricing-grid { grid-template-columns: 1fr; }
}

/* ─── CTA section ───────────────────────────────── */
.cta-section {
  background: linear-gradient(135deg, var(--navy) 0%, var(--green) 100%);
  padding: 80px 24px; text-align: center;
}
.cta-inner { max-width: 640px; margin: 0 auto; }
.cta-title { font-size: 38px; font-weight: 900; color: #fff; margin-bottom: 16px; }
.cta-sub { font-size: 16px; color: rgba(255,255,255,0.8); margin-bottom: 36px; }
.cta-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

/* ─── Footer ────────────────────────────────────── */
.footer { background: var(--navy-dk); color: rgba(255,255,255,0.7); padding: 64px 24px 32px; }
.footer-inner { max-width: 1160px; margin: 0 auto; }
.footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
.footer-brand .logo { margin-bottom: 14px; }
.footer-about { font-size: 13px; line-height: 1.7; }
.footer-social { display: flex; gap: 10px; margin-top: 18px; }
.social-link {
  width: 34px; height: 34px; border-radius: 8px;
  background: rgba(255,255,255,0.08);
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; transition: background 0.15s;
}
.social-link:hover { background: rgba(255,255,255,0.16); }
.footer-col-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #fff; margin-bottom: 16px; }
.footer-link { display: block; font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 10px; transition: color 0.15s; }
.footer-link:hover { color: #fff; }
.footer-bottom {
  display: flex; justify-content: space-between; align-items: center;
  padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.1);
  font-size: 12px;
}
.footer-contact-item { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 13px; }
.footer-contact-icon { font-size: 14px; opacity: 0.6; }

/* ─── Responsive ────────────────────────────────── */
@media (max-width: 900px) {
  .hero h1 { font-size: 36px; }
  .header-nav { display: none; }
  .features-grid { grid-template-columns: 1fr 1fr; }
  .steps-grid { grid-template-columns: 1fr; }
  .stats-bar { grid-template-columns: 1fr 1fr; }
  .testimonials-grid { grid-template-columns: 1fr; }
  .footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; }
  .section-title { font-size: 28px; }
}
@media (max-width: 600px) {
  .hero h1 { font-size: 28px; }
  .hero-cards { display: none; }
  .features-grid { grid-template-columns: 1fr; }
  .stats-bar { grid-template-columns: 1fr 1fr; }
  .footer-grid { grid-template-columns: 1fr; gap: 24px; }
  .footer-bottom { flex-direction: column; gap: 8px; text-align: center; }
  .header-cta .btn-outline { display: none; }
}
</style>
</head>
<body>

{{-- ── Header ──────────────────────────────────── --}}
<header class="header">
  <div class="header-inner">
    <a href="{{ url('/') }}" class="logo">
      @if($logoUrl)
        <img src="{{ $logoUrl }}" alt="{{ $siteName }}">
      @else
        <div class="logo-icon">M</div>
      @endif
      {{ $siteName }}
    </a>
    <nav class="header-nav">
      <a href="#features" class="nav-link">Features</a>
      <a href="#how-it-works" class="nav-link">How it works</a>
      <a href="#why-us" class="nav-link">Why MedRecords</a>
      <a href="#pricing" class="nav-link">Pricing</a>
      <a href="#contact" class="nav-link">Contact</a>
    </nav>
    <div class="header-cta">
      <a href="{{ route('login') }}" class="btn-outline">Sign in</a>
      <a href="{{ route('register') }}" class="btn-solid">Get started free →</a>
    </div>
  </div>
</header>

{{-- ── Hero ─────────────────────────────────────── --}}
<section class="hero">
  <div class="hero-inner">
    <div class="hero-badge">🇳🇬 Built for Nigeria's healthcare system</div>
    @php
      $hasBreak   = str_contains($heroHead, '<br>');
      $heroParts  = array_map('e', explode('<br>', $heroHead));
      $heroHeadSafe = implode('<br><span>', $heroParts) . ($hasBreak ? '</span>' : '');
    @endphp
    <h1>{!! $heroHeadSafe !!}</h1>
    <p class="hero-sub">{{ $heroSub }}</p>
    <div class="hero-actions">
      <a href="{{ route('register') }}" class="btn-hero-primary">{{ $heroCta1 }} →</a>
      <a href="#features" class="btn-hero-secondary">{{ $heroCta2 }}</a>
    </div>

    <div class="hero-cards">
      <div class="hero-card">
        <div class="hc-icon">🏥</div>
        <div class="hc-value">4+</div>
        <div class="hc-label">Partner facilities</div>
      </div>
      <div class="hero-card">
        <div class="hc-icon">🔐</div>
        <div class="hc-value">100%</div>
        <div class="hc-label">Consent-controlled</div>
      </div>
      <div class="hero-card">
        <div class="hc-icon">📋</div>
        <div class="hc-value">FHIR</div>
        <div class="hc-label">R4-aligned records</div>
      </div>
      <div class="hero-card">
        <div class="hc-icon">📲</div>
        <div class="hc-value">SMS</div>
        <div class="hc-label">Instant access codes</div>
      </div>
    </div>
  </div>
</section>

{{-- ── Features ──────────────────────────────────── --}}
<section class="section" id="features">
  <div class="section-inner">
    <div class="section-label">What we offer</div>
    <h2 class="section-title">Everything you need in one place</h2>
    <p class="section-sub">From GP visits to lab results, prescriptions to dental records — your complete health story, always accessible.</p>

    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon fi-navy">📋</div>
        <div class="feature-title">Complete Health Records</div>
        <div class="feature-desc">Store encounters, prescriptions, lab results, vaccinations, dental and eye care records — all in a single FHIR R4-aligned record.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon fi-green">🔐</div>
        <div class="feature-title">Patient-Controlled Access</div>
        <div class="feature-desc">You decide who sees your records. Grant consent to specific providers for a limited time. Revoke instantly, anytime.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon fi-amber">💊</div>
        <div class="feature-title">Pharmacy Verification</div>
        <div class="feature-desc">Pharmacists verify prescriptions against your MedRecords before dispensing — preventing duplication and fraud.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon fi-red">📲</div>
        <div class="feature-title">SMS Access Codes</div>
        <div class="feature-desc">Share one-time access codes via SMS. Providers verify your identity at reception without requiring a smartphone or internet connection.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon fi-navy">👨‍👩‍👧</div>
        <div class="feature-title">Family Dependents</div>
        <div class="feature-desc">Manage health records for your children and elderly relatives. One account, your whole family covered.</div>
      </div>
      <div class="feature-card">
        <div class="feature-icon fi-green">👁</div>
        <div class="feature-title">Full Audit Trail</div>
        <div class="feature-desc">Every access to your record is logged with time, actor, and reason. Know exactly who saw what and when.</div>
      </div>
    </div>
  </div>
</section>

{{-- ── How it works ─────────────────────────────── --}}
<section class="section bg-light" id="how-it-works">
  <div class="section-inner">
    <div style="text-align:center; margin-bottom: 0;">
      <div class="section-label" style="display:inline-block;">Simple steps</div>
      <h2 class="section-title">How MedRecords works</h2>
      <p class="section-sub" style="margin: 0 auto;">Getting started takes under two minutes. No paperwork, no queues.</p>
    </div>
    <div class="steps-grid">
      <div class="step">
        <div class="step-num">1</div>
        <div class="step-title">Create your account</div>
        <div class="step-desc">Register with your name, email, phone number and a few health details. You get a unique MedID instantly — it's free, always.</div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div class="step-title">Visit any partner facility</div>
        <div class="step-desc">Share your 6-digit access code with the doctor or pharmacist. They can view your records with your permission only.</div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div class="step-title">Stay in control</div>
        <div class="step-desc">Review who accessed your records, revoke any consent, and get your complete health history on demand — anywhere, anytime.</div>
      </div>
    </div>
  </div>
</section>

{{-- ── Stats ────────────────────────────────────── --}}
<section class="section bg-dark" id="why-us">
  <div class="section-inner">
    <div style="text-align:center; margin-bottom: 0;">
      <div class="section-label" style="color: #6EE4A8; display: inline-block;">By the numbers</div>
      <h2 class="section-title">Trusted by patients across Nigeria</h2>
    </div>
    <div class="stats-bar">
      <div class="stat-item">
        <div class="sv">5+</div>
        <div class="sl">Registered patients</div>
      </div>
      <div class="stat-item">
        <div class="sv">3+</div>
        <div class="sl">Partner facilities</div>
      </div>
      <div class="stat-item">
        <div class="sv">100%</div>
        <div class="sl">Consent-driven access</div>
      </div>
      <div class="stat-item">
        <div class="sv">FHIR</div>
        <div class="sl">International standard</div>
      </div>
    </div>
  </div>
</section>

{{-- ── Testimonials ─────────────────────────────── --}}
<section class="section">
  <div class="section-inner">
    <div class="section-label">What people say</div>
    <h2 class="section-title">Trusted by patients & providers</h2>

    <div class="testimonials-grid">
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <p class="testimonial-quote">"I no longer carry a folder of lab results to every hospital visit. My doctor accesses my records the moment I share my code. This is the future of healthcare in Nigeria."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background:linear-gradient(135deg,#1E3A5F,#2C5F2D);">AO</div>
          <div>
            <div class="testimonial-name">Adaeze Okonkwo</div>
            <div class="testimonial-role">Patient, Lagos</div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <p class="testimonial-quote">"Seeing a patient's allergy history before prescribing has prevented serious incidents at our clinic. The consent system is excellent — patients feel safe."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background:linear-gradient(135deg,#2C5F2D,#1E3A5F);">TK</div>
          <div>
            <div class="testimonial-name">Dr. Tunde Kamara</div>
            <div class="testimonial-role">General Practitioner, Reddington Hospital</div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <p class="testimonial-quote">"Prescription verification through MedRecords has reduced fraudulent prescriptions at our pharmacy to almost zero. Every pharmacist in Nigeria should be using this."</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar" style="background:linear-gradient(135deg,#B8860B,#1E3A5F);">BO</div>
          <div>
            <div class="testimonial-name">Bola Okafor</div>
            <div class="testimonial-role">Pharmacist, HealthPlus Pharmacy</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── Pricing ──────────────────────────────────── --}}
@if($plans->isNotEmpty())
<section class="section bg-light" id="pricing">
  <div class="section-inner">
    <div style="text-align:center;margin-bottom:0;">
      <div class="section-label" style="display:inline-block;">Simple, transparent pricing</div>
      <h2 class="section-title">Choose the plan that fits you</h2>
      <p class="section-sub" style="margin:0 auto;">Start free. Upgrade anytime. No hidden fees, no contracts.</p>
    </div>
    <div class="pricing-grid">
      @foreach($plans as $plan)
      <div class="pricing-card {{ $plan->is_featured ? 'featured' : '' }}">
        @if($plan->badge)
          <div class="pricing-badge">{{ $plan->badge }}</div>
        @endif
        <div class="pricing-name">{{ $plan->name }}</div>
        <div class="pricing-price">
          @if($plan->isFree())
            Free
          @elseif($plan->billing_period === 'one-time')
            <span style="font-size:20px;">Contact us</span>
          @else
            ₦{{ number_format($plan->price, 0) }}<span class="period">/{{ $plan->billing_period === 'monthly' ? 'mo' : 'yr' }}</span>
          @endif
        </div>
        <div class="pricing-desc">{{ $plan->description }}</div>
        <ul class="pricing-features">
          @foreach($plan->features ?? [] as $feature)
            <li><span class="check">✓</span> {{ $feature }}</li>
          @endforeach
        </ul>
        @if($plan->billing_period === 'one-time')
          <a href="#contact" class="pricing-cta contact">Contact Sales</a>
        @elseif($plan->isFree())
          <a href="{{ route('register') }}" class="pricing-cta free">Get started free →</a>
        @else
          <a href="{{ route('register') }}" class="pricing-cta">Start {{ $plan->name }} →</a>
        @endif
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ── CTA ──────────────────────────────────────── --}}
<section class="cta-section">
  <div class="cta-inner">
    <h2 class="cta-title">Your health records deserve better</h2>
    <p class="cta-sub">Join thousands of Nigerians taking control of their health data. Registration is free and takes less than 2 minutes.</p>
    <div class="cta-actions">
      <a href="{{ route('register') }}" class="btn-hero-primary">Create your free account →</a>
      <a href="{{ route('login') }}" class="btn-hero-secondary">Sign in</a>
    </div>
  </div>
</section>

{{-- ── Footer ───────────────────────────────────── --}}
<footer class="footer" id="contact">
  <div class="footer-inner">
    <div class="footer-grid">
      {{-- Brand --}}
      <div class="footer-brand">
        <div class="logo">
          @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $siteName }}">
          @else
            <div class="logo-icon">M</div>
          @endif
          {{ $siteName }}
        </div>
        <p class="footer-about">{{ $footerAbout }}</p>
        @if($fb || $tw || $ig || $li)
        <div class="footer-social">
          @if($fb)<a href="{{ $fb }}" class="social-link" title="Facebook">📘</a>@endif
          @if($tw)<a href="{{ $tw }}" class="social-link" title="Twitter / X">𝕏</a>@endif
          @if($ig)<a href="{{ $ig }}" class="social-link" title="Instagram">📷</a>@endif
          @if($li)<a href="{{ $li }}" class="social-link" title="LinkedIn">🔗</a>@endif
        </div>
        @endif
      </div>

      {{-- Platform --}}
      <div>
        <div class="footer-col-title">Platform</div>
        <a href="{{ route('register') }}" class="footer-link">Patient registration</a>
        <a href="{{ route('login') }}" class="footer-link">Provider login</a>
        <a href="{{ route('login') }}" class="footer-link">Pharmacy login</a>
        <a href="{{ route('login') }}" class="footer-link">Admin portal</a>
      </div>

      {{-- Resources --}}
      <div>
        <div class="footer-col-title">Resources</div>
        <a href="#features" class="footer-link">Features</a>
        <a href="#how-it-works" class="footer-link">How it works</a>
        <a href="#" class="footer-link">Privacy policy</a>
        <a href="#" class="footer-link">Terms of service</a>
      </div>

      {{-- Contact --}}
      <div>
        <div class="footer-col-title">Contact</div>
        @if($phone1)
        <div class="footer-contact-item">
          <span class="footer-contact-icon">📞</span>
          <span>{{ $phone1 }}</span>
        </div>
        @endif
        @if($phone2)
        <div class="footer-contact-item">
          <span class="footer-contact-icon">📞</span>
          <span>{{ $phone2 }}</span>
        </div>
        @endif
        @if($email)
        <div class="footer-contact-item">
          <span class="footer-contact-icon">✉️</span>
          <span>{{ $email }}</span>
        </div>
        @endif
        @if($address)
        <div class="footer-contact-item">
          <span class="footer-contact-icon">📍</span>
          <span>{{ $address }}{{ $city ? ', ' . $city : '' }}{{ $state ? ', ' . $state : '' }}</span>
        </div>
        @endif
      </div>
    </div>

    <div class="footer-bottom">
      <span>© {{ date('Y') }} {{ $siteName }}. All rights reserved.</span>
      <span>Built for Nigeria's healthcare future 🇳🇬</span>
    </div>
  </div>
</footer>

</body>
</html>
