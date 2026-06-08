<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'MedRecords') — MedRecords</title>
<style>
:root {
  --navy: #1E3A5F;
  --navy-dark: #15293F;
  --navy-tint: #E8EEF5;
  --green: #2C5F2D;
  --green-tint: #E8F0E8;
  --amber: #B8860B;
  --amber-tint: #FAF3E0;
  --red: #B23A48;
  --red-tint: #FBEAEC;
  --grey-900: #1A1F26;
  --grey-700: #3D4754;
  --grey-500: #6B7280;
  --grey-300: #D1D5DB;
  --grey-200: #E5E7EB;
  --grey-100: #F3F4F6;
  --grey-50: #F9FAFB;
  --white: #FFFFFF;
  --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 12px rgba(15,23,42,0.05);
  --shadow-lg: 0 10px 25px rgba(15,23,42,0.12);
}
* { box-sizing: border-box; margin: 0; padding: 0; }
html, body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  color: var(--grey-900);
  background: var(--grey-50);
  line-height: 1.5;
}

/* --- Layout --- */
.app-layout { display: flex; min-height: 100vh; }
.sidebar {
  width: 220px;
  background: var(--navy-dark);
  color: var(--white);
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0; left: 0; bottom: 0;
  z-index: 100;
}
.sidebar-brand {
  padding: 20px 18px 16px;
  font-size: 18px;
  font-weight: 800;
  letter-spacing: -0.02em;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}
.sidebar-brand .sub { font-size: 10px; font-weight: 400; opacity: 0.7; margin-top: 2px; }
.sidebar-nav { flex: 1; padding: 8px 12px; overflow-y: auto; }
.nav-section { padding: 12px 6px 4px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255,255,255,0.4); }
.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  border-radius: 8px;
  font-size: 13px;
  color: rgba(255,255,255,0.7);
  text-decoration: none;
  margin-bottom: 2px;
  transition: background 0.15s;
}
.nav-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
.nav-item.active { background: rgba(255,255,255,0.12); color: #fff; font-weight: 600; }
.nav-item .badge {
  margin-left: auto;
  background: var(--amber);
  color: #fff;
  padding: 1px 7px;
  border-radius: 8px;
  font-size: 10px;
}
.sidebar-footer {
  padding: 14px 18px;
  border-top: 1px solid rgba(255,255,255,0.1);
  font-size: 12px;
  color: rgba(255,255,255,0.6);
}
.sidebar-footer .name { color: #fff; font-weight: 600; font-size: 13px; }

.main-content { margin-left: 220px; flex: 1; min-height: 100vh; }
.topbar {
  background: var(--white);
  border-bottom: 1px solid var(--grey-200);
  padding: 12px 28px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 50;
}
.topbar-title { font-size: 17px; font-weight: 700; color: var(--navy); }
.topbar-actions { display: flex; align-items: center; gap: 12px; }
.page-body { padding: 28px; }

/* --- Cards --- */
.card {
  background: var(--white);
  border-radius: 12px;
  padding: 20px;
  box-shadow: var(--shadow);
}
.card-header { margin-bottom: 16px; }
.card-title { font-size: 15px; font-weight: 700; color: var(--navy); }
.card-sub { font-size: 12px; color: var(--grey-500); margin-top: 2px; }

/* --- Stat grid --- */
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
.stat-card {
  background: var(--white);
  border-radius: 12px;
  padding: 18px 20px;
  box-shadow: var(--shadow);
}
.stat-card .stat-label { font-size: 11px; color: var(--grey-500); text-transform: uppercase; letter-spacing: 0.05em; }
.stat-card .stat-value { font-size: 28px; font-weight: 800; color: var(--navy); margin-top: 4px; }
.stat-card .stat-sub { font-size: 11px; color: var(--grey-500); margin-top: 2px; }
.stat-card.green .stat-value { color: var(--green); }
.stat-card.amber .stat-value { color: var(--amber); }
.stat-card.red .stat-value { color: var(--red); }

/* --- Pills & badges --- */
.pill {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 8px;
  font-size: 11px;
  font-weight: 600;
}
.pill.green  { background: var(--green-tint); color: var(--green); }
.pill.amber  { background: var(--amber-tint); color: var(--amber); }
.pill.red    { background: var(--red-tint); color: var(--red); }
.pill.navy   { background: var(--navy-tint); color: var(--navy); }
.pill.grey   { background: var(--grey-100); color: var(--grey-700); }

/* --- Buttons --- */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 9px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: opacity 0.15s;
}
.btn:hover { opacity: 0.88; }
.btn-primary   { background: var(--navy); color: #fff; }
.btn-green     { background: var(--green); color: #fff; }
.btn-secondary { background: var(--white); color: var(--navy); border: 1px solid var(--grey-300); }
.btn-danger    { background: var(--red-tint); color: var(--red); }
.btn-sm { padding: 6px 12px; font-size: 12px; }

/* --- Alerts --- */
.alert {
  padding: 12px 14px;
  border-radius: 8px;
  font-size: 13px;
  display: flex;
  gap: 10px;
  margin-bottom: 16px;
}
.alert.green { background: var(--green-tint); color: #1F4520; border-left: 3px solid var(--green); }
.alert.amber { background: var(--amber-tint); color: #7B5C00; border-left: 3px solid var(--amber); }
.alert.red   { background: var(--red-tint); color: #7A1F2C; border-left: 3px solid var(--red); }
.alert.navy  { background: var(--navy-tint); color: var(--navy); border-left: 3px solid var(--navy); }

/* --- Tables --- */
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; font-size: 13px; }
thead th {
  padding: 10px 12px;
  text-align: left;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--grey-500);
  background: var(--grey-50);
  border-bottom: 1px solid var(--grey-200);
}
tbody td { padding: 12px; border-bottom: 1px solid var(--grey-100); }
tbody tr:last-child td { border-bottom: 0; }
tbody tr:hover { background: var(--grey-50); }

/* --- Forms --- */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-grid .full { grid-column: 1 / -1; }
.form-field { display: flex; flex-direction: column; }
.form-field label {
  font-size: 11px;
  color: var(--grey-500);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin-bottom: 5px;
  font-weight: 600;
}
.form-field input, .form-field select, .form-field textarea {
  background: var(--grey-50);
  border: 1px solid var(--grey-200);
  border-radius: 8px;
  padding: 9px 12px;
  font-size: 13px;
  color: var(--grey-900);
  width: 100%;
  transition: border-color 0.15s;
}
.form-field input:focus, .form-field select:focus, .form-field textarea:focus {
  outline: none;
  border-color: var(--navy);
}
.form-field textarea { min-height: 80px; resize: vertical; }
.form-actions { display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end; }

/* --- Patient banner --- */
.patient-banner {
  background: var(--white);
  border-radius: 12px;
  padding: 18px 22px;
  margin-bottom: 20px;
  display: grid;
  grid-template-columns: 1fr auto auto;
  gap: 18px;
  align-items: center;
  box-shadow: var(--shadow);
}
.pb-name { font-size: 20px; font-weight: 700; color: var(--navy); }
.pb-meta { font-size: 12px; color: var(--grey-500); margin-top: 2px; }
.pb-flags { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px; }
.pb-consent .label { font-size: 10px; text-transform: uppercase; color: var(--grey-500); letter-spacing: 0.05em; }
.pb-consent .value { font-size: 15px; color: var(--green); font-weight: 700; margin-top: 2px; }
.pb-consent .sub { font-size: 11px; color: var(--grey-500); }

/* --- Detail rows --- */
.detail-row {
  display: flex;
  justify-content: space-between;
  padding: 9px 0;
  border-bottom: 1px solid var(--grey-100);
  font-size: 13px;
}
.detail-row:last-child { border-bottom: 0; }
.detail-row .k { color: var(--grey-500); }
.detail-row .v { color: var(--grey-900); font-weight: 500; text-align: right; }

/* --- Tab strip --- */
.tab-strip { display: flex; gap: 4px; border-bottom: 2px solid var(--grey-200); margin-bottom: 20px; overflow-x: auto; }
.tab-item {
  padding: 10px 16px;
  font-size: 13px;
  color: var(--grey-500);
  border-bottom: 3px solid transparent;
  font-weight: 500;
  text-decoration: none;
  white-space: nowrap;
  margin-bottom: -2px;
  transition: color 0.15s;
}
.tab-item:hover { color: var(--navy); }
.tab-item.active { color: var(--navy); border-bottom-color: var(--navy); font-weight: 700; }
.tab-count { background: var(--grey-100); padding: 1px 6px; border-radius: 8px; font-size: 10px; margin-left: 4px; }

/* --- Entry list --- */
.entry { padding: 14px 0; border-bottom: 1px solid var(--grey-100); }
.entry:last-child { border-bottom: 0; }
.entry-head { display: flex; justify-content: space-between; align-items: flex-start; }
.entry-title { font-size: 14px; font-weight: 600; color: var(--grey-900); }
.entry-date  { font-size: 11px; color: var(--grey-500); }
.entry-meta  { font-size: 12px; color: var(--grey-500); margin-top: 2px; }
.entry-body  { font-size: 13px; color: var(--grey-700); margin-top: 8px; line-height: 1.55; }
.entry-foot  { font-size: 11px; color: var(--grey-500); margin-top: 6px; font-style: italic; }

/* --- MedID card --- */
.medid-card {
  padding: 18px 20px;
  background: linear-gradient(135deg, var(--navy) 0%, var(--green) 100%);
  color: #fff;
  border-radius: 14px;
  box-shadow: var(--shadow);
}
.medid-card .label { font-size: 10px; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.05em; }
.medid-card .number { font-size: 24px; font-weight: 800; letter-spacing: 0.04em; margin-top: 4px; }
.medid-card .row { display: flex; justify-content: space-between; margin-top: 12px; font-size: 12px; }
.medid-card .verified { background: rgba(255,255,255,0.18); padding: 3px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }

/* --- Page header --- */
.page-header { margin-bottom: 24px; }
.page-header h1 { font-size: 22px; font-weight: 800; color: var(--navy); }
.page-header p  { font-size: 13px; color: var(--grey-500); margin-top: 3px; }
.page-header-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }

/* --- Access code --- */
.access-code-box {
  background: var(--navy);
  color: #fff;
  border-radius: 16px;
  padding: 28px 24px;
  text-align: center;
  max-width: 320px;
  margin: 0 auto;
}
.access-code-box .code-label { font-size: 12px; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.05em; }
.access-code-box .code { font-size: 44px; font-weight: 800; letter-spacing: 0.12em; margin: 10px 0; }
.access-code-box .timer { font-size: 12px; opacity: 0.85; }

/* --- Responsive --- */
@media (max-width: 768px) {
  .sidebar { width: 0; overflow: hidden; }
  .main-content { margin-left: 0; }
  .patient-banner { grid-template-columns: 1fr; }
  .form-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>
<div class="app-layout">
  @yield('sidebar')
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-title">@yield('topbar-title', 'MedRecords')</div>
      <div class="topbar-actions">
        @yield('topbar-actions')
        <span style="font-size:13px;color:var(--grey-500);">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
          @csrf
          <button type="submit" class="btn btn-secondary btn-sm">Sign out</button>
        </form>
      </div>
    </div>
    <div class="page-body">
      @if(session('success'))
        <div class="alert green"><span>✓</span><span>{{ session('success') }}</span></div>
      @endif
      @if(session('error'))
        <div class="alert red"><span>✗</span><span>{{ session('error') }}</span></div>
      @endif
      @yield('content')
    </div>
  </div>
</div>
</body>
</html>
