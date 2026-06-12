@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('topbar-title', 'Dashboard')

@section('content')
{{-- Stats --}}
<div class="stat-grid" style="margin-bottom:28px;">
  <div class="stat-card">
    <div class="stat-label">Patients</div>
    <div class="stat-value">{{ number_format($stats['patients']) }}</div>
    <div class="stat-sub">Registered</div>
    <div class="stat-icon">👤</div>
  </div>
  <div class="stat-card green">
    <div class="stat-label">Providers</div>
    <div class="stat-value">{{ number_format($stats['providers']) }}</div>
    <div class="stat-sub">Clinicians</div>
    <div class="stat-icon">🩺</div>
  </div>
  <div class="stat-card green">
    <div class="stat-label">Pharmacists</div>
    <div class="stat-value">{{ number_format($stats['pharmacists']) }}</div>
    <div class="stat-sub">Dispensing</div>
    <div class="stat-icon">💊</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Facilities</div>
    <div class="stat-value">{{ number_format($stats['facilities']) }}</div>
    <div class="stat-sub">Registered</div>
    <div class="stat-icon">🏥</div>
  </div>
  <div class="stat-card amber">
    <div class="stat-label">Break-glass</div>
    <div class="stat-value">{{ $stats['break_glass'] }}</div>
    <div class="stat-sub">Active events</div>
    <div class="stat-icon">⚡</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Users</div>
    <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
    <div class="stat-sub">All roles</div>
    <div class="stat-icon">🌐</div>
  </div>
</div>

{{-- Charts row 1: registrations + role donut --}}
<div class="dash-grid-2-1" style="margin-bottom:20px;">
  <div class="card">
    <div class="card-header" style="padding-bottom:16px;">
      <div>
        <div class="card-title">New Registrations</div>
        <div class="card-sub">Users registered per day — last 30 days</div>
      </div>
      <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-sm">View Users</a>
    </div>
    <div class="card-body" style="padding-top:0;">
      <canvas id="regChart" height="90"></canvas>
    </div>
  </div>

  <div class="card">
    <div class="card-header" style="padding-bottom:16px;">
      <div>
        <div class="card-title">User Roles</div>
        <div class="card-sub">Distribution by role</div>
      </div>
    </div>
    <div class="card-body" style="padding-top:0;display:flex;align-items:center;justify-content:center;">
      <canvas id="roleChart" height="200" style="max-height:200px;"></canvas>
    </div>
  </div>
</div>

{{-- Charts row 2: encounters + dispenses --}}
<div class="dash-grid-1-1" style="margin-bottom:24px;">
  <div class="card">
    <div class="card-header" style="padding-bottom:16px;">
      <div>
        <div class="card-title">Clinical Encounters</div>
        <div class="card-sub">Encounters per week — last 8 weeks</div>
      </div>
    </div>
    <div class="card-body" style="padding-top:0;">
      <canvas id="encChart" height="120"></canvas>
    </div>
  </div>

  <div class="card">
    <div class="card-header" style="padding-bottom:16px;">
      <div>
        <div class="card-title">Pharmacy Dispenses</div>
        <div class="card-sub">Prescriptions dispensed per month — last 6 months</div>
      </div>
    </div>
    <div class="card-body" style="padding-top:0;">
      <canvas id="dispChart" height="120"></canvas>
    </div>
  </div>
</div>

{{-- Bottom row: recent audit log + active break-glass --}}
<div class="dash-grid-2-1">
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title">Recent Audit Events</div>
        <div class="card-sub">Latest access and system events</div>
      </div>
    </div>
    <div class="card-body" style="padding:0;">
      @forelse($recentLogs as $log)
      @php
        $actorName = $log->actor?->name ?? $log->adminActor?->name ?? 'System';
        $action    = $log->action ?? '';
        $icon = '🔵';
        if (str_contains($action, 'view'))     $icon = '👁';
        elseif (str_contains($action, 'break'))    $icon = '⚡';
        elseif (str_contains($action, 'dispense')) $icon = '💊';
        elseif (str_contains($action, 'encounter'))$icon = '📋';
        elseif (str_contains($action, 'login'))    $icon = '🔑';
      @endphp
      <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 22px;border-bottom:1px solid var(--border);font-size:12px;">
        <div style="width:32px;height:32px;border-radius:50%;background:var(--navy-lt);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">
          {{ $icon }}
        </div>
        <div style="flex:1;min-width:0;">
          <div style="font-weight:600;color:var(--text);">{{ $actorName }}</div>
          <div style="color:var(--text-2);margin-top:1px;">{{ ucfirst(str_replace('_', ' ', $action)) }}</div>
        </div>
        <div style="color:var(--text-3);white-space:nowrap;">{{ $log->created_at->diffForHumans() }}</div>
      </div>
      @empty
      <div class="empty-state" style="padding:32px;">
        <div class="empty-icon" style="font-size:28px;">📋</div>
        <p>No audit events yet.</p>
      </div>
      @endforelse
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title" style="color:var(--red);">⚡ Active Break-glass</div>
        <div class="card-sub">Requires review</div>
      </div>
      <a href="{{ route('admin.break-glass') }}" class="btn btn-secondary btn-sm">All</a>
    </div>
    <div class="card-body" style="padding:0;">
      @forelse($breakGlass as $event)
      <div style="padding:12px 22px;border-bottom:1px solid var(--border);font-size:12px;">
        <div style="font-weight:600;color:var(--text);">{{ $event->patient?->user?->name ?? 'Unknown patient' }}</div>
        <div style="color:var(--text-2);margin-top:2px;">Dr. {{ $event->provider?->name ?? '—' }}</div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;">
          <span class="pill amber">{{ ucfirst($event->status) }}</span>
          <span style="color:var(--text-3);">{{ $event->created_at->diffForHumans() }}</span>
        </div>
      </div>
      @empty
      <div class="empty-state" style="padding:32px;">
        <div class="empty-icon" style="font-size:28px;">✅</div>
        <p>No active break-glass events.</p>
      </div>
      @endforelse
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script nonce="{{ $cspNonce }}">
Chart.defaults.font = { family: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif', size: 11 };
Chart.defaults.color = '#64748B';

new Chart(document.getElementById('regChart'), {
  type: 'line',
  data: {
    labels: {!! json_encode($charts['regLabels']) !!},
    datasets: [{
      label: 'Registrations',
      data: {!! json_encode($charts['regCounts']) !!},
      borderColor: '#1E3A5F',
      backgroundColor: 'rgba(30,58,95,0.07)',
      borderWidth: 2, pointRadius: 2, pointHoverRadius: 5,
      fill: true, tension: 0.4,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false }, ticks: { maxTicksLimit: 10 } },
      y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F1F5F9' } }
    }
  }
});

new Chart(document.getElementById('roleChart'), {
  type: 'doughnut',
  data: {
    labels: {!! json_encode($charts['roleLabels']) !!},
    datasets: [{
      data: {!! json_encode($charts['roleValues']) !!},
      backgroundColor: ['#1E3A5F','#2C5F2D','#3B8C6E','#B8860B','#B23A48'],
      borderWidth: 2, borderColor: '#fff', hoverOffset: 6,
    }]
  },
  options: {
    responsive: true, cutout: '65%',
    plugins: {
      legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11 } } }
    }
  }
});

new Chart(document.getElementById('encChart'), {
  type: 'bar',
  data: {
    labels: {!! json_encode($charts['encLabels']) !!},
    datasets: [{
      label: 'Encounters',
      data: {!! json_encode($charts['encCounts']) !!},
      backgroundColor: 'rgba(44,95,45,0.75)',
      borderRadius: 5, borderSkipped: false,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false } },
      y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F1F5F9' } }
    }
  }
});

new Chart(document.getElementById('dispChart'), {
  type: 'bar',
  data: {
    labels: {!! json_encode($charts['dispLabels']) !!},
    datasets: [{
      label: 'Dispenses',
      data: {!! json_encode($charts['dispCounts']) !!},
      backgroundColor: 'rgba(30,58,95,0.65)',
      borderRadius: 5, borderSkipped: false,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false } },
      y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F1F5F9' } }
    }
  }
});
</script>
@endpush
@endsection
