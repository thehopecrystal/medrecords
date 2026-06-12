@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('topbar-title', 'Dashboard')

@section('content')
{{-- Stats --}}
<div class="stat-grid mb-28">
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
<div class="dash-grid-2-1 mb-20">
  <div class="card">
    <div class="card-header card-header-tight">
      <div>
        <div class="card-title">New Registrations</div>
        <div class="card-sub">Users registered per day — last 30 days</div>
      </div>
      <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-sm">View Users</a>
    </div>
    <div class="card-body card-body-pt0">
      <canvas id="regChart" height="90"></canvas>
    </div>
  </div>

  <div class="card">
    <div class="card-header card-header-tight">
      <div>
        <div class="card-title">User Roles</div>
        <div class="card-sub">Distribution by role</div>
      </div>
    </div>
    <div class="card-body card-body-chart-center">
      <canvas id="roleChart" height="200" class="canvas-max"></canvas>
    </div>
  </div>
</div>

{{-- Charts row 2: encounters + dispenses --}}
<div class="dash-grid-1-1 mb-24">
  <div class="card">
    <div class="card-header card-header-tight">
      <div>
        <div class="card-title">Clinical Encounters</div>
        <div class="card-sub">Encounters per week — last 8 weeks</div>
      </div>
    </div>
    <div class="card-body card-body-pt0">
      <canvas id="encChart" height="120"></canvas>
    </div>
  </div>

  <div class="card">
    <div class="card-header card-header-tight">
      <div>
        <div class="card-title">Pharmacy Dispenses</div>
        <div class="card-sub">Prescriptions dispensed per month — last 6 months</div>
      </div>
    </div>
    <div class="card-body card-body-pt0">
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
    <div class="card-body card-body-0">
      @forelse($recentLogs as $log)
      @php
        $actorName = $log->actor?->name ?? $log->adminActor?->name ?? 'System';
        $action    = $log->action ?? '';
        $icon = '🔵';
        if (str_contains($action, 'view'))      $icon = '👁';
        elseif (str_contains($action, 'break'))     $icon = '⚡';
        elseif (str_contains($action, 'dispense'))  $icon = '💊';
        elseif (str_contains($action, 'encounter')) $icon = '📋';
        elseif (str_contains($action, 'login'))     $icon = '🔑';
      @endphp
      <div class="audit-row">
        <div class="audit-icon">{{ $icon }}</div>
        <div class="audit-body">
          <div class="audit-actor">{{ $actorName }}</div>
          <div class="audit-action">{{ ucfirst(str_replace('_', ' ', $action)) }}</div>
        </div>
        <div class="audit-time">{{ $log->created_at->diffForHumans() }}</div>
      </div>
      @empty
      <div class="empty-state empty-state-sm">
        <div class="empty-icon">📋</div>
        <p>No audit events yet.</p>
      </div>
      @endforelse
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title card-title-red">⚡ Active Break-glass</div>
        <div class="card-sub">Requires review</div>
      </div>
      <a href="{{ route('admin.break-glass') }}" class="btn btn-secondary btn-sm">All</a>
    </div>
    <div class="card-body card-body-0">
      @forelse($breakGlass as $event)
      <div class="bgrow">
        <div class="bgrow-name">{{ $event->patient?->user?->name ?? 'Unknown patient' }}</div>
        <div class="bgrow-sub">Dr. {{ $event->provider?->name ?? '—' }}</div>
        <div class="bgrow-meta">
          <span class="pill amber">{{ ucfirst($event->status) }}</span>
          <span class="bgrow-date">{{ $event->created_at->diffForHumans() }}</span>
        </div>
      </div>
      @empty
      <div class="empty-state empty-state-sm">
        <div class="empty-icon">✅</div>
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
