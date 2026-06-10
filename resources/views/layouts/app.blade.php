<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
  use App\Models\SiteSetting;
  use Illuminate\Support\Str;
  SiteSetting::loadAll();
  $siteName   = SiteSetting::get('site_name', 'MedRecords');
  $logoUrl    = SiteSetting::get('site_logo_url', '');
  $authUser   = auth()->user();
  $userRole   = $authUser?->role ?? 'guest';
  $userName   = $authUser?->name ?? '';
  $nameParts  = explode(' ', $userName);
  $initials   = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
  $unreadCount  = $authUser
    ? \App\Models\Notification::where('user_id', $authUser->id)->whereNull('read_at')->count()
    : 0;
  $patientId    = ($userRole === 'patient') ? $authUser?->patient?->id : null;
  $pendingConsents = $patientId
    ? \App\Models\Consent::where('patient_id', $patientId)->where('status','pending')->exists()
    : false;
  $activeBreakGlass = (in_array($userRole, ['facility_admin','super_admin']))
    ? \App\Models\BreakGlassEvent::where('status','active')->count()
    : 0;
@endphp
<title>@yield('title', $siteName) — {{ $siteName }}</title>
@php $faviconUrl = \App\Models\SiteSetting::get('site_favicon_url', ''); @endphp
@if($faviconUrl)<link rel="icon" href="{{ $faviconUrl }}">@endif
<style nonce="{{ $cspNonce }}">
/* ─── Reset & Base ──────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { font-size: 14px; -webkit-text-size-adjust: 100%; }
body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Inter", Roboto, Arial, sans-serif;
  background: #F0F4F8;
  color: #0F172A;
  line-height: 1.55;
}
a { color: inherit; text-decoration: none; }
button, input, select, textarea { font-family: inherit; }

/* ─── Dark mode ─────────────────────────────────── */
[data-theme="dark"] {
  --navy:      #4A9CF8;
  --navy-dk:   #0A0F1A;
  --navy-md:   #2563EB;
  --navy-lt:   #1E2D45;
  --green:     #4ADE80;
  --green-lt:  #0D2E1A;
  --amber:     #FCD34D;
  --amber-lt:  #2D2008;
  --red:       #F87171;
  --red-lt:    #2D1218;

  --sb-bg:     #070D18;
  --page-bg:   #0F172A;
  --text:      #E2E8F0;
  --text-2:    #94A3B8;
  --text-3:    #475569;
  --border:    #1E293B;
  --card:      #0F1C2E;
  --shadow-sm: 0 1px 2px rgba(0,0,0,0.4);
  --shadow:    0 1px 3px rgba(0,0,0,0.3), 0 4px 16px rgba(0,0,0,0.25);
  --shadow-lg: 0 8px 30px rgba(0,0,0,0.5);
  --grey-900: #E2E8F0;
  --grey-700: #94A3B8;
  --grey-500: #64748B;
  --grey-300: #1E293B;
  --grey-200: #1E293B;
  --grey-100: #0F1C2E;
  --grey-50:  #0D1627;
  --white:    #0F1C2E;
}
[data-theme="dark"] body { background: var(--page-bg); color: var(--text); }
[data-theme="dark"] thead th { background: #0D1627; }
[data-theme="dark"] tbody tr:hover { background: rgba(255,255,255,0.03); }
[data-theme="dark"] .form-field input,
[data-theme="dark"] .form-field select,
[data-theme="dark"] .form-field textarea { background: #0D1627; color: var(--text); border-color: var(--border); }
[data-theme="dark"] .user-dropdown { background: #0F1C2E; }
[data-theme="dark"] .topbar { background: #0F1C2E; }
[data-theme="dark"] pre { background: #0D1627 !important; color: #94A3B8 !important; }

/* ─── Design tokens ─────────────────────────────── */
:root {
  --navy:      #1E3A5F;
  --navy-dk:   #0D1F35;
  --navy-md:   #163050;
  --navy-lt:   #EEF3FA;
  --green:     #2C5F2D;
  --green-lt:  #EDF7ED;
  --amber:     #B8860B;
  --amber-lt:  #FEF9E7;
  --red:       #B23A48;
  --red-lt:    #FBEAEC;

  --sb-bg:     #0D1F35;
  --sb-hover:  rgba(255,255,255,0.06);
  --sb-active: rgba(255,255,255,0.12);
  --sb-text:   rgba(255,255,255,0.65);
  --sb-w:      260px;

  --topbar-h:  62px;
  --page-bg:   #F0F4F8;

  --text:      #0F172A;
  --text-2:    #475569;
  --text-3:    #94A3B8;
  --border:    #E2E8F0;
  --card:      #FFFFFF;
  --shadow-sm: 0 1px 2px rgba(15,23,42,0.05);
  --shadow:    0 1px 3px rgba(15,23,42,0.06), 0 4px 16px rgba(15,23,42,0.05);
  --shadow-lg: 0 8px 30px rgba(15,23,42,0.12);
  --radius:    10px;
  --radius-lg: 14px;

  /* ── Backwards-compat aliases (used in view-level inline CSS) ── */
  --navy-tint:  #EEF3FA;
  --green-tint: #EDF7ED;
  --amber-tint: #FEF9E7;
  --red-tint:   #FBEAEC;
  --grey-900: #0F172A;
  --grey-700: #334155;
  --grey-500: #64748B;
  --grey-300: #CBD5E1;
  --grey-200: #E2E8F0;
  --grey-100: #F1F5F9;
  --grey-50:  #F8FAFC;
  --white:    #FFFFFF;
  --shadow:   0 1px 3px rgba(15,23,42,0.06), 0 4px 16px rgba(15,23,42,0.05);
}

/* ─── App shell ─────────────────────────────────── */
.app { display: flex; min-height: 100vh; }

/* ─── Sidebar ───────────────────────────────────── */
.sb {
  width: var(--sb-w);
  background: var(--sb-bg);
  display: flex;
  flex-direction: column;
  position: fixed;
  inset: 0 auto 0 0;
  z-index: 200;
  overflow: hidden;
  transition: width 0.25s ease, transform 0.25s ease;
}
.sb-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 18px 20px 16px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  flex-shrink: 0;
}
.sb-logo-img { width: 30px; height: 30px; border-radius: 8px; object-fit: contain; }
.sb-logo-mark {
  width: 30px; height: 30px;
  background: linear-gradient(135deg, var(--navy) 0%, #2C5F2D 100%);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 800; color: #fff; flex-shrink: 0;
}
.sb-brand-name {
  font-size: 15px; font-weight: 800;
  color: #fff; letter-spacing: -0.02em; line-height: 1;
}
.sb-brand-role {
  font-size: 10px; color: rgba(255,255,255,0.45);
  text-transform: uppercase; letter-spacing: 0.06em; margin-top: 2px;
}

.sb-user {
  display: flex; align-items: center; gap: 10px;
  padding: 14px 20px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  flex-shrink: 0;
}
.sb-avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: linear-gradient(135deg, #2C5F2D, #1E3A5F);
  color: #fff; font-size: 13px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.sb-uname { font-size: 13px; font-weight: 600; color: #fff; line-height: 1.2; }
.sb-urole { font-size: 11px; color: rgba(255,255,255,0.45); margin-top: 1px; text-transform: capitalize; }

.sb-nav { flex: 1; padding: 8px 12px; overflow-y: auto; }
.sb-nav::-webkit-scrollbar { width: 4px; }
.sb-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }
.sb-section {
  padding: 14px 8px 5px;
  font-size: 10px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.08em;
  color: rgba(255,255,255,0.3);
}
.sb-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 12px;
  border-radius: 8px;
  font-size: 13px; font-weight: 500;
  color: var(--sb-text);
  transition: background 0.15s, color 0.15s;
  margin-bottom: 1px;
  position: relative;
}
.sb-item:hover { background: var(--sb-hover); color: #fff; }
.sb-item.active {
  background: var(--sb-active);
  color: #fff; font-weight: 600;
}
.sb-item.active::before {
  content: ''; position: absolute;
  left: 0; top: 25%; bottom: 25%;
  width: 3px; background: #4A9CF8;
  border-radius: 0 3px 3px 0;
}
.sb-icon { font-size: 15px; width: 20px; text-align: center; flex-shrink: 0; }
.sb-badge {
  margin-left: auto;
  background: var(--red); color: #fff;
  font-size: 10px; font-weight: 700;
  padding: 1px 6px; border-radius: 10px;
  min-width: 18px; text-align: center;
}
.sb-footer {
  padding: 10px 12px 14px;
  border-top: 1px solid rgba(255,255,255,0.08);
  flex-shrink: 0;
}
.sb-logout {
  width: 100%;
  background: none; border: none; cursor: pointer;
  color: var(--sb-text);
  text-align: left;
}
.sb-logout:hover { color: var(--red-lt); background: rgba(178,58,72,0.12); }

/* ─── Mobile overlay ────────────────────────────── */
.sb-overlay {
  display: none;
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 190;
}

/* ─── Main area ─────────────────────────────────── */
.main { margin-left: var(--sb-w); flex: 1; min-height: 100vh; display: flex; flex-direction: column; }

/* ─── Topbar ────────────────────────────────────── */
.topbar {
  height: var(--topbar-h);
  background: var(--card);
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center;
  padding: 0 24px;
  gap: 14px;
  position: sticky; top: 0; z-index: 100;
  box-shadow: var(--shadow-sm);
}
.topbar-toggle {
  display: none;
  background: none; border: none; cursor: pointer;
  font-size: 20px; padding: 4px; color: var(--text-2);
}
.topbar-title {
  font-size: 16px; font-weight: 700; color: var(--navy);
  flex: 1;
}
.topbar-right { display: flex; align-items: center; gap: 8px; }

/* Notification bell */
.notif-btn {
  position: relative;
  background: none; border: none; cursor: pointer;
  width: 36px; height: 36px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 17px; color: var(--text-2);
  transition: background 0.15s;
}
.notif-btn:hover { background: var(--navy-lt); }
.notif-dot {
  position: absolute; top: 5px; right: 5px;
  width: 8px; height: 8px; border-radius: 50%;
  background: var(--red); border: 2px solid white;
}

/* User menu */
.user-menu { position: relative; }
.user-btn {
  display: flex; align-items: center; gap: 8px;
  background: none; border: none; cursor: pointer;
  padding: 6px 10px; border-radius: 8px;
  transition: background 0.15s;
}
.user-btn:hover { background: var(--navy-lt); }
.user-btn-avatar {
  width: 32px; height: 32px; border-radius: 50%;
  background: linear-gradient(135deg, #2C5F2D, #1E3A5F);
  color: #fff; font-size: 12px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
}
.user-btn-name { font-size: 13px; font-weight: 600; color: var(--text); }
.user-btn-caret { font-size: 10px; color: var(--text-3); }
.user-dropdown {
  display: none;
  position: absolute; right: 0; top: calc(100% + 6px);
  background: var(--card); border: 1px solid var(--border);
  border-radius: var(--radius); box-shadow: var(--shadow-lg);
  min-width: 200px; z-index: 300; overflow: hidden;
}
.user-dropdown.open { display: block; }
.ud-head { padding: 14px 16px; border-bottom: 1px solid var(--border); }
.ud-name { font-size: 14px; font-weight: 700; }
.ud-email { font-size: 12px; color: var(--text-2); margin-top: 1px; }
.ud-item {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 16px; font-size: 13px; color: var(--text-2);
  transition: background 0.12s;
}
.ud-item:hover { background: var(--navy-lt); color: var(--navy); }
.ud-divider { border: none; border-top: 1px solid var(--border); margin: 4px 0; }
.ud-logout { color: var(--red); }
.ud-logout:hover { background: var(--red-lt); color: var(--red); }

/* ─── Page body ─────────────────────────────────── */
.page-body { padding: 28px; flex: 1; }

/* ─── Flash messages ────────────────────────────── */
.flash {
  display: flex; align-items: flex-start; gap: 10px;
  padding: 12px 16px; border-radius: var(--radius);
  font-size: 13px; margin-bottom: 20px;
  animation: fadeIn 0.2s ease;
}
.flash.success { background: var(--green-lt); color: #1A4D1B; border-left: 3px solid var(--green); }
.flash.error   { background: var(--red-lt);   color: #7A1F2C; border-left: 3px solid var(--red); }
.flash.info    { background: var(--navy-lt);  color: var(--navy); border-left: 3px solid var(--navy); }
.flash.warning { background: var(--amber-lt); color: #7B5C00; border-left: 3px solid var(--amber); }
.flash-icon { font-size: 15px; flex-shrink: 0; margin-top: 1px; }
.flash-close {
  margin-left: auto; background: none; border: none;
  cursor: pointer; font-size: 16px; opacity: 0.5; padding: 0;
}
.flash-close:hover { opacity: 1; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: none; } }

/* ─── Cards ─────────────────────────────────────── */
.card {
  background: var(--card);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow);
  border: 1px solid var(--border);
}
.card-pad { padding: 22px; }
.card-header {
  padding: 18px 22px 0;
  display: flex; align-items: flex-start; justify-content: space-between;
}
.card-title { font-size: 15px; font-weight: 700; color: var(--navy); }
.card-sub { font-size: 12px; color: var(--text-2); margin-top: 2px; }
.card-body { padding: 16px 22px 22px; }
.card-footer { padding: 14px 22px; border-top: 1px solid var(--border); background: #FAFBFC; border-radius: 0 0 var(--radius-lg) var(--radius-lg); }

/* ─── Stat grid ─────────────────────────────────── */
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 16px; margin-bottom: 24px; }
.stat-card {
  background: var(--card);
  border-radius: var(--radius-lg); border: 1px solid var(--border);
  padding: 20px 22px; box-shadow: var(--shadow);
  position: relative; overflow: hidden;
}
.stat-card::after {
  content: ''; position: absolute;
  top: 0; right: 0;
  width: 60px; height: 100%;
  background: linear-gradient(135deg, transparent 40%, rgba(30,58,95,0.03));
}
.stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-2); }
.stat-value { font-size: 30px; font-weight: 800; color: var(--navy); margin-top: 6px; line-height: 1; }
.stat-sub   { font-size: 12px; color: var(--text-2); margin-top: 4px; }
.stat-icon  { font-size: 28px; position: absolute; top: 16px; right: 18px; opacity: 0.15; }
.stat-card.green .stat-value { color: var(--green); }
.stat-card.amber .stat-value { color: var(--amber); }
.stat-card.red   .stat-value { color: var(--red);   }

/* ─── Pills ─────────────────────────────────────── */
.pill {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 3px 10px; border-radius: 20px;
  font-size: 11px; font-weight: 600; white-space: nowrap;
}
.pill.green  { background: var(--green-lt); color: var(--green); }
.pill.amber  { background: var(--amber-lt); color: var(--amber); }
.pill.red    { background: var(--red-lt);   color: var(--red); }
.pill.navy   { background: var(--navy-lt);  color: var(--navy); }
.pill.grey   { background: #F1F5F9;         color: var(--text-2); }

/* ─── Buttons ───────────────────────────────────── */
.btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 18px; border-radius: 8px;
  font-size: 13px; font-weight: 600;
  border: 1.5px solid transparent;
  cursor: pointer; transition: all 0.15s;
  text-decoration: none; white-space: nowrap;
  font-family: inherit;
}
.btn-primary { background: var(--navy); color: #fff; }
.btn-primary:hover { background: var(--navy-md); }
.btn-green  { background: var(--green); color: #fff; }
.btn-green:hover { filter: brightness(1.08); }
.btn-secondary { background: var(--card); color: var(--text); border-color: var(--border); }
.btn-secondary:hover { background: #F8FAFC; border-color: #CBD5E1; }
.btn-danger { background: var(--red-lt); color: var(--red); border-color: transparent; }
.btn-danger:hover { background: #F5C6CC; }
.btn-ghost { background: transparent; color: var(--text-2); border-color: transparent; }
.btn-ghost:hover { background: var(--navy-lt); color: var(--navy); }
.btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 6px; }
.btn-lg { padding: 12px 24px; font-size: 15px; border-radius: 10px; }
.btn:disabled { opacity: 0.55; cursor: not-allowed; }

/* ─── Tables ────────────────────────────────────── */
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; font-size: 13px; }
thead th {
  padding: 10px 14px;
  text-align: left;
  font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.05em;
  color: var(--text-2); background: #FAFBFC;
  border-bottom: 1px solid var(--border);
}
tbody td { padding: 13px 14px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
tbody tr:last-child td { border-bottom: 0; }
tbody tr:hover { background: #FAFCFF; }

/* ─── Forms ─────────────────────────────────────── */
.form-section { margin-bottom: 28px; }
.form-section-title {
  font-size: 14px; font-weight: 700; color: var(--navy);
  padding-bottom: 10px; border-bottom: 2px solid var(--navy-lt);
  margin-bottom: 18px;
}
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
.form-grid .span-2 { grid-column: 1 / -1; }
.form-field { display: flex; flex-direction: column; gap: 5px; }
.form-field label {
  font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.05em;
  color: var(--text-2);
}
.form-field input, .form-field select, .form-field textarea {
  background: #FAFBFC;
  border: 1.5px solid var(--border);
  border-radius: 8px;
  padding: 9px 12px;
  font-size: 13px; color: var(--text);
  width: 100%; transition: border-color 0.15s, box-shadow 0.15s;
}
.form-field input:focus, .form-field select:focus, .form-field textarea:focus {
  outline: none; border-color: var(--navy);
  box-shadow: 0 0 0 3px rgba(30,58,95,0.08);
}
.form-field input::placeholder { color: var(--text-3); }
.form-field textarea { min-height: 90px; resize: vertical; }
.form-hint { font-size: 11px; color: var(--text-3); margin-top: 2px; }
.form-error { font-size: 11px; color: var(--red); margin-top: 2px; }
.form-actions { display: flex; gap: 10px; align-items: center; }

/* ─── Alerts ────────────────────────────────────── */
.alert {
  padding: 12px 16px; border-radius: var(--radius);
  font-size: 13px; display: flex; gap: 10px;
  margin-bottom: 16px; border-left: 3px solid transparent;
}
.alert.green { background: var(--green-lt); color: #1A4D1B; border-color: var(--green); }
.alert.amber { background: var(--amber-lt); color: #7B5C00; border-color: var(--amber); }
.alert.red   { background: var(--red-lt);   color: #7A1F2C; border-color: var(--red); }
.alert.navy  { background: var(--navy-lt);  color: var(--navy); border-color: var(--navy); }

/* ─── Page header ───────────────────────────────── */
.page-header { margin-bottom: 24px; }
.page-header h1 { font-size: 22px; font-weight: 800; color: var(--navy); line-height: 1.2; }
.page-header p  { font-size: 13px; color: var(--text-2); margin-top: 4px; }
.page-header-row { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; }
.page-header-row h1 { font-size: 22px; font-weight: 800; color: var(--navy); }
.page-header-row p  { font-size: 13px; color: var(--text-2); margin-top: 3px; }

/* ─── Entry list ────────────────────────────────── */
.entry { padding: 14px 0; border-bottom: 1px solid #F1F5F9; }
.entry:last-child { border-bottom: 0; }
.entry-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
.entry-title { font-size: 14px; font-weight: 600; }
.entry-date  { font-size: 11px; color: var(--text-3); white-space: nowrap; }
.entry-meta  { font-size: 12px; color: var(--text-2); margin-top: 3px; }
.entry-body  { font-size: 13px; color: var(--text-2); margin-top: 8px; line-height: 1.55; }

/* ─── Detail rows ───────────────────────────────── */
.detail-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 0; border-bottom: 1px solid #F1F5F9; font-size: 13px;
}
.detail-row:last-child { border-bottom: 0; }
.detail-row .k { color: var(--text-2); font-weight: 500; }
.detail-row .v { color: var(--text); font-weight: 600; text-align: right; }

/* ─── Tab strip ─────────────────────────────────── */
.tab-strip {
  display: flex; gap: 2px;
  border-bottom: 2px solid var(--border);
  margin-bottom: 24px; overflow-x: auto;
}
.tab-item {
  padding: 10px 18px;
  font-size: 13px; font-weight: 500; color: var(--text-2);
  border-bottom: 3px solid transparent; margin-bottom: -2px;
  white-space: nowrap; transition: color 0.15s;
  text-decoration: none;
}
.tab-item:hover { color: var(--navy); }
.tab-item.active { color: var(--navy); border-bottom-color: var(--navy); font-weight: 700; }
.tab-count {
  display: inline-flex; align-items: center; justify-content: center;
  background: var(--navy-lt); color: var(--navy);
  padding: 1px 7px; border-radius: 10px; font-size: 10px;
  margin-left: 5px; font-weight: 700;
}

/* ─── Patient banner ────────────────────────────── */
.patient-banner {
  background: var(--card); border-radius: var(--radius-lg);
  border: 1px solid var(--border); box-shadow: var(--shadow);
  padding: 20px 24px; margin-bottom: 22px;
  display: grid; grid-template-columns: 1fr auto auto;
  gap: 20px; align-items: center;
}
.pb-name { font-size: 20px; font-weight: 800; color: var(--navy); }
.pb-meta { font-size: 12px; color: var(--text-2); margin-top: 3px; }
.pb-flags { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px; }

/* ─── MedID card ────────────────────────────────── */
.medid-card {
  background: linear-gradient(135deg, var(--navy-dk) 0%, var(--navy) 60%, #2C5F2D 100%);
  color: #fff; border-radius: var(--radius-lg);
  padding: 20px 22px; box-shadow: var(--shadow-lg);
  position: relative; overflow: hidden;
}
.medid-card::before {
  content: ''; position: absolute;
  top: -30px; right: -30px; width: 120px; height: 120px;
  background: rgba(255,255,255,0.05); border-radius: 50%;
}
.medid-card .label { font-size: 10px; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.07em; }
.medid-card .number { font-size: 22px; font-weight: 800; letter-spacing: 0.05em; margin-top: 5px; }
.medid-card .row { display: flex; justify-content: space-between; margin-top: 14px; font-size: 12px; opacity: 0.8; }
.medid-card .verified { background: rgba(255,255,255,0.15); padding: 2px 9px; border-radius: 10px; font-size: 10px; font-weight: 700; }

/* ─── Access code ───────────────────────────────── */
.access-code-box {
  background: linear-gradient(135deg, var(--navy-dk), var(--navy));
  color: #fff; border-radius: 20px;
  padding: 32px 24px; text-align: center;
  max-width: 340px; margin: 0 auto;
  box-shadow: var(--shadow-lg);
}
.access-code-box .code-label { font-size: 11px; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.08em; }
.access-code-box .code { font-size: 52px; font-weight: 800; letter-spacing: 0.14em; margin: 12px 0; }
.access-code-box .timer { font-size: 12px; opacity: 0.75; }

/* ─── Pagination ────────────────────────────────── */
.pagination { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; margin-top: 16px; }
.pagination a, .pagination span {
  display: inline-flex; align-items: center; justify-content: center;
  min-width: 34px; height: 34px; padding: 0 10px;
  border-radius: 7px; font-size: 13px; border: 1px solid var(--border);
  transition: all 0.12s;
}
.pagination a { color: var(--text); }
.pagination a:hover { background: var(--navy-lt); border-color: var(--navy); color: var(--navy); }
.pagination span.active { background: var(--navy); color: #fff; border-color: var(--navy); font-weight: 700; }
.pagination span.disabled { color: var(--text-3); cursor: not-allowed; }

/* ─── Empty state ───────────────────────────────── */
.empty-state {
  text-align: center; padding: 48px 24px;
  color: var(--text-2);
}
.empty-state .empty-icon { font-size: 40px; margin-bottom: 14px; opacity: 0.5; }
.empty-state h3 { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
.empty-state p { font-size: 13px; }

/* ─── Responsive ────────────────────────────────── */
@media (max-width: 900px) {
  .sb { transform: translateX(-100%); }
  .sb.mobile-open { transform: translateX(0); }
  .sb-overlay.mobile-open { display: block; }
  .main { margin-left: 0 !important; }
  .topbar-toggle { display: flex; }
  .page-body { padding: 16px; }
  .patient-banner { grid-template-columns: 1fr; }
  .form-grid, .form-grid-3 { grid-template-columns: 1fr; }
  .stat-grid { grid-template-columns: 1fr 1fr; }
  .user-btn-name { display: none; }
}
@media (max-width: 480px) {
  .stat-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>
<div class="app">

  {{-- ── Sidebar ───────────────────────────────── --}}
  <aside class="sb" id="sidebar">
    {{-- Logo --}}
    <div class="sb-logo">
      @if($logoUrl)
        <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="sb-logo-img">
      @else
        <div class="sb-logo-mark">M</div>
      @endif
      <div>
        <div class="sb-brand-name">{{ $siteName }}</div>
        <div class="sb-brand-role">
          @if($userRole === 'patient') Patient Portal
          @elseif($userRole === 'provider') Clinical Workstation
          @elseif($userRole === 'pharmacist') Pharmacy
          @elseif(in_array($userRole, ['facility_admin','super_admin'])) Administration
          @else MedRecords @endif
        </div>
      </div>
    </div>

    {{-- User info --}}
    <div class="sb-user">
      @if($authUser?->avatar)
        <img src="{{ asset('storage/' . $authUser->avatar) }}" alt="" class="sb-avatar" style="object-fit:cover;">
      @else
        <div class="sb-avatar">{{ $initials ?: '?' }}</div>
      @endif
      <div>
        <div class="sb-uname">{{ Str::limit($userName, 22) }}</div>
        <div class="sb-urole">
          @if($userRole === 'facility_admin' || $userRole === 'super_admin') Administrator
          @else {{ $userRole }} @endif
        </div>
      </div>
    </div>

    {{-- Navigation --}}
    <nav class="sb-nav">
      @if($userRole === 'patient')
        <div class="sb-section">Menu</div>
        <a href="{{ route('patient.dashboard') }}" class="sb-item {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
          <span class="sb-icon">🏠</span> Dashboard
        </a>
        <a href="{{ route('patient.record') }}" class="sb-item {{ request()->routeIs('patient.record','patient.prescription') ? 'active' : '' }}">
          <span class="sb-icon">📋</span> My Health Records
        </a>
        <a href="{{ route('patient.consents') }}" class="sb-item {{ request()->routeIs('patient.consents*') ? 'active' : '' }}">
          <span class="sb-icon">🔐</span> Consent Management
          @if($pendingConsents)<span class="sb-badge">!</span>@endif
        </a>
        <a href="{{ route('patient.access-code') }}" class="sb-item {{ request()->routeIs('patient.access-code*') ? 'active' : '' }}">
          <span class="sb-icon">🔑</span> Share Record
        </a>
        <a href="{{ route('patient.family') }}" class="sb-item {{ request()->routeIs('patient.family*') ? 'active' : '' }}">
          <span class="sb-icon">👨‍👩‍👧</span> My Family
        </a>
        <a href="{{ route('patient.family.summary') }}" class="sb-item {{ request()->routeIs('patient.family.summary') ? 'active' : '' }}">
          <span class="sb-icon">📊</span> Family Summary
        </a>
        <div class="sb-section">Health Tools</div>
        <a href="{{ route('patient.appointments') }}" class="sb-item {{ request()->routeIs('patient.appointments*') ? 'active' : '' }}">
          <span class="sb-icon">📅</span> Appointments
        </a>
        <a href="{{ route('patient.vitals') }}" class="sb-item {{ request()->routeIs('patient.vitals*') ? 'active' : '' }}">
          <span class="sb-icon">❤️</span> Vitals Diary
        </a>
        <a href="{{ route('patient.refills') }}" class="sb-item {{ request()->routeIs('patient.refills*') ? 'active' : '' }}">
          <span class="sb-icon">💊</span> Refill Requests
        </a>
        <a href="{{ route('patient.lab-upload') }}" class="sb-item {{ request()->routeIs('patient.lab-upload*') ? 'active' : '' }}">
          <span class="sb-icon">🧪</span> Lab Upload
        </a>
        <a href="{{ route('patient.notifications') }}" class="sb-item {{ request()->routeIs('patient.notifications*') ? 'active' : '' }}">
          <span class="sb-icon">🔔</span> Notifications
          @if($unreadCount > 0)<span class="sb-badge">{{ $unreadCount }}</span>@endif
        </a>
        <div class="sb-section">Account</div>
        <a href="{{ route('patient.audit-log') }}" class="sb-item {{ request()->routeIs('patient.audit-log') ? 'active' : '' }}">
          <span class="sb-icon">👁</span> Access History
        </a>
        <a href="{{ route('patient.profile') }}" class="sb-item {{ request()->routeIs('patient.profile*') ? 'active' : '' }}">
          <span class="sb-icon">👤</span> Edit Profile
        </a>

      @elseif($userRole === 'provider')
        <div class="sb-section">Workstation</div>
        <a href="{{ route('provider.dashboard') }}" class="sb-item {{ request()->routeIs('provider.dashboard') ? 'active' : '' }}">
          <span class="sb-icon">🏥</span> Dashboard
        </a>
        <a href="{{ route('provider.search') }}" class="sb-item {{ request()->routeIs('provider.search','provider.patient') ? 'active' : '' }}">
          <span class="sb-icon">🔍</span> Patient Search
        </a>
        <a href="{{ route('provider.appointments') }}" class="sb-item {{ request()->routeIs('provider.appointments*') ? 'active' : '' }}">
          <span class="sb-icon">📅</span> My Schedule
        </a>
        <a href="{{ route('provider.refills') }}" class="sb-item {{ request()->routeIs('provider.refills*') ? 'active' : '' }}">
          <span class="sb-icon">💊</span> Refill Requests
        </a>
        <a href="{{ route('provider.referrals') }}" class="sb-item {{ request()->routeIs('provider.referrals*') ? 'active' : '' }}">
          <span class="sb-icon">📋</span> Referral Inbox
        </a>

      @elseif($userRole === 'pharmacist')
        <div class="sb-section">Pharmacy</div>
        <a href="{{ route('pharmacy.dashboard') }}" class="sb-item {{ request()->routeIs('pharmacy.dashboard') ? 'active' : '' }}">
          <span class="sb-icon">🏥</span> Dashboard
        </a>
        <a href="{{ route('pharmacy.lookup') }}" class="sb-item {{ request()->routeIs('pharmacy.lookup') ? 'active' : '' }}">
          <span class="sb-icon">🔍</span> Verify Prescription
        </a>
        <a href="{{ route('pharmacy.history') }}" class="sb-item {{ request()->routeIs('pharmacy.history') ? 'active' : '' }}">
          <span class="sb-icon">📋</span> Dispense History
        </a>

      @elseif(in_array($userRole, ['facility_admin','super_admin']))
        <div class="sb-section">Administration</div>
        <a href="{{ route('admin.dashboard') }}" class="sb-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <span class="sb-icon">📊</span> Dashboard
        </a>
        <a href="{{ route('admin.facilities') }}" class="sb-item {{ request()->routeIs('admin.facilities') ? 'active' : '' }}">
          <span class="sb-icon">🏥</span> Facilities
        </a>
        <a href="{{ route('admin.facilities.import') }}" class="sb-item {{ request()->routeIs('admin.facilities.import*') ? 'active' : '' }}">
          <span class="sb-icon">📥</span> Import Facilities
        </a>
        <a href="{{ route('admin.break-glass') }}" class="sb-item {{ request()->routeIs('admin.break-glass') ? 'active' : '' }}">
          <span class="sb-icon">⚡</span> Break-glass Review
          @if($activeBreakGlass > 0)<span class="sb-badge">{{ $activeBreakGlass }}</span>@endif
        </a>
        <a href="{{ route('admin.providers') }}" class="sb-item {{ request()->routeIs('admin.providers') ? 'active' : '' }}">
          <span class="sb-icon">🩺</span> MDCN Verification
        </a>
        @if($userRole === 'facility_admin')
          <a href="{{ route('admin.team') }}" class="sb-item {{ request()->routeIs('admin.team*') ? 'active' : '' }}">
            <span class="sb-icon">👨‍⚕️</span> My Team
          </a>
        @endif
        @if($userRole === 'super_admin')
          <div class="sb-section">Super Admin</div>
          <a href="{{ route('admin.team') }}" class="sb-item {{ request()->routeIs('admin.team*') ? 'active' : '' }}">
            <span class="sb-icon">👨‍⚕️</span> Team Management
          </a>
          <a href="{{ route('admin.users') }}" class="sb-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <span class="sb-icon">👥</span> Users &amp; Roles
          </a>
          <a href="{{ route('admin.plans') }}" class="sb-item {{ request()->routeIs('admin.plans*') ? 'active' : '' }}">
            <span class="sb-icon">💳</span> Plans &amp; Pricing
          </a>
        @endif
        <div class="sb-section">System</div>
        <a href="{{ route('admin.notifications') }}" class="sb-item {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
          <span class="sb-icon">📣</span> Send Notification
        </a>
        <a href="{{ route('admin.settings') }}" class="sb-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
          <span class="sb-icon">⚙️</span> Settings
        </a>
      @endif
    </nav>

    {{-- Sign out --}}
    <div class="sb-footer">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sb-item sb-logout">
          <span class="sb-icon">🚪</span> Sign out
        </button>
      </form>
    </div>
  </aside>

  {{-- Mobile overlay --}}
  <div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

  {{-- ── Main content ─────────────────────────── --}}
  <div class="main">
    {{-- Topbar --}}
    <div class="topbar">
      <button class="topbar-toggle" onclick="toggleSidebar()" aria-label="Toggle menu">☰</button>
      <div class="topbar-title">@yield('topbar-title', $siteName)</div>
      <div class="topbar-right">
        {{-- Dark mode toggle --}}
        <button class="notif-btn" id="darkToggle" title="Toggle dark mode" onclick="toggleDark()">☀️</button>

        {{-- Notifications --}}
        <a href="{{ $userRole === 'patient' ? route('patient.notifications') : '#' }}" class="notif-btn" title="Notifications">
          🔔
          @if($unreadCount > 0)<span class="notif-dot"></span>@endif
        </a>

        {{-- User menu --}}
        <div class="user-menu" id="userMenu">
          <button class="user-btn" onclick="toggleUserMenu()">
            <div class="user-btn-avatar">
              @if($authUser?->avatar)
                <img src="{{ asset('storage/' . $authUser->avatar) }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
              @else
                {{ $initials ?: '?' }}
              @endif
            </div>
            <span class="user-btn-name">{{ Str::limit($userName, 16) }}</span>
            <span class="user-btn-caret">▾</span>
          </button>
          <div class="user-dropdown" id="userDropdown">
            <div class="ud-head">
              <div class="ud-name">{{ $userName }}</div>
              <div class="ud-email">{{ $authUser?->email }}</div>
            </div>
            @if($userRole === 'patient')
              <a href="{{ route('patient.profile') }}" class="ud-item">👤 Edit profile</a>
              <a href="{{ route('patient.access-code') }}" class="ud-item">🔑 Access code</a>
            @endif
            <hr class="ud-divider">
            <a href="{{ url('/') }}" class="ud-item">🏠 Homepage</a>
            <hr class="ud-divider">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="ud-item ud-logout" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">🚪 Sign out</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    {{-- Page body --}}
    <div class="page-body">
      @if(session('success'))
        <div class="flash success" id="flashMsg">
          <span class="flash-icon">✓</span>
          <span>{{ session('success') }}</span>
          <button class="flash-close" onclick="this.parentElement.remove()">×</button>
        </div>
      @endif
      @if(session('error'))
        <div class="flash error">
          <span class="flash-icon">✗</span>
          <span>{{ session('error') }}</span>
          <button class="flash-close" onclick="this.parentElement.remove()">×</button>
        </div>
      @endif
      @if(session('warning'))
        <div class="flash warning">
          <span class="flash-icon">⚠</span>
          <span>{{ session('warning') }}</span>
          <button class="flash-close" onclick="this.parentElement.remove()">×</button>
        </div>
      @endif
      @if($errors->any() && !$errors->has('login') && !$errors->has('current_password'))
        <div class="flash error">
          <span class="flash-icon">✗</span>
          <div>
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
          </div>
          <button class="flash-close" onclick="this.parentElement.remove()">×</button>
        </div>
      @endif

      @yield('content')
    </div>
  </div>
</div>

<script nonce="{{ $cspNonce }}">
// Dark mode
(function() {
  const saved = localStorage.getItem('medrecords_theme') || 'light';
  applyTheme(saved);
})();
function applyTheme(t) {
  document.documentElement.setAttribute('data-theme', t);
  const btn = document.getElementById('darkToggle');
  if (btn) btn.textContent = t === 'dark' ? '🌙' : '☀️';
}
function toggleDark() {
  const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  localStorage.setItem('medrecords_theme', next);
  applyTheme(next);
}

// Sidebar toggle
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('mobile-open');
  document.getElementById('sbOverlay').classList.toggle('mobile-open');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('mobile-open');
  document.getElementById('sbOverlay').classList.remove('mobile-open');
}

// User dropdown
function toggleUserMenu() {
  document.getElementById('userDropdown').classList.toggle('open');
}
document.addEventListener('click', function(e) {
  const menu = document.getElementById('userMenu');
  if (menu && !menu.contains(e.target)) {
    document.getElementById('userDropdown').classList.remove('open');
  }
});

// Auto-dismiss flash after 5s
setTimeout(function() {
  const f = document.getElementById('flashMsg');
  if (f) f.style.opacity = '0', f.style.transition = 'opacity 0.4s', setTimeout(() => f.remove(), 400);
}, 5000);
</script>

@stack('scripts')
</body>
</html>
