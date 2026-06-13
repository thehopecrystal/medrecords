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

{{-- Apply saved theme before first paint to prevent flash --}}
<script nonce="{{ $cspNonce }}">
(function(){var t=localStorage.getItem('medrecords_theme')||'light';document.documentElement.setAttribute('data-theme',t)})()
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('styles')
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

{{-- Bootstrap 5 JS (modal behaviour — CDN allowed by CSP script-src) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Global confirmation modal --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmTitle">Confirm Action</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="confirmBody">Are you sure?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmOk">Confirm</button>
      </div>
    </div>
  </div>
</div>

<script nonce="{{ $cspNonce }}">
(function () {
  'use strict';
  var _pending = null;
  var _modal   = null;

  function getModal() {
    if (!_modal) {
      var el = document.getElementById('confirmModal');
      _modal = el ? new bootstrap.Modal(el, { keyboard: true }) : null;
      if (el) {
        el.addEventListener('hidden.bs.modal', function () { _pending = null; });
      }
    }
    return _modal;
  }

  /* Public API — call window._showConfirm(opts) from any page script */
  window._showConfirm = function (opts) {
    var m = getModal();
    if (!m) return opts.onConfirm && opts.onConfirm();
    document.getElementById('confirmTitle').textContent = opts.title   || 'Confirm Action';
    document.getElementById('confirmBody').textContent  = opts.message || 'Are you sure?';
    var ok = document.getElementById('confirmOk');
    ok.className  = 'btn ' + (opts.btnClass || 'btn-danger');
    ok.textContent = opts.okLabel || 'Confirm';
    _pending = opts.onConfirm || null;
    m.show();
  };

  /* Confirm button handler */
  document.getElementById('confirmOk').addEventListener('click', function () {
    getModal().hide();
    if (_pending) { var fn = _pending; _pending = null; fn(); }
  });

  /* Intercept any form[data-confirm] submission */
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form.hasAttribute('data-confirm')) return;
    e.preventDefault();
    window._showConfirm({
      title:    form.dataset.confirmTitle || 'Confirm Action',
      message:  form.dataset.confirm,
      btnClass: form.dataset.confirmBtn   || 'btn-danger',
      okLabel:  form.dataset.confirmOk    || 'Confirm',
      onConfirm: function () {
        form.removeAttribute('data-confirm');
        form.submit();
      }
    });
  }, false);
})();
</script>

@stack('scripts')
</body>
</html>
