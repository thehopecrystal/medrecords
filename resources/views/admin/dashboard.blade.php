@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('topbar-title', 'Admin Dashboard')


@section('content')
<div class="page-header"><h1>System Overview</h1><p>{{ now()->format('l, d F Y') }}</p></div>

<div class="stat-grid">
  <div class="stat-card navy"><div class="stat-label">Total patients</div><div class="stat-value">{{ number_format($stats['patients']) }}</div></div>
  <div class="stat-card green"><div class="stat-label">Providers</div><div class="stat-value">{{ number_format($stats['providers']) }}</div></div>
  <div class="stat-card"><div class="stat-label">Facilities</div><div class="stat-value">{{ number_format($stats['facilities']) }}</div></div>
  <div class="stat-card {{ $stats['break_glass'] > 0 ? 'red' : '' }}">
    <div class="stat-label">Active break-glass</div>
    <div class="stat-value">{{ $stats['break_glass'] }}</div>
    @if($stats['break_glass'] > 0)
      <div class="stat-sub"><a href="{{ route('admin.break-glass') }}" style="color:var(--red);">Review →</a></div>
    @endif
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
  {{-- Facilities --}}
  <div class="card">
    <div class="card-header">
      <div class="card-title">Facilities</div>
      <div class="card-sub">Active facilities on the platform</div>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Facility</th><th>Type</th><th>Providers</th></tr></thead>
        <tbody>
          @foreach($facilities as $f)
            <tr>
              <td><strong>{{ $f->name }}</strong><br><span style="font-size:11px;color:var(--grey-500);">{{ $f->city }}</span></td>
              <td><span class="pill navy">{{ $f->type }}</span></td>
              <td>{{ $f->providers_count }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div style="margin-top:12px;"><a href="{{ route('admin.facilities') }}" style="font-size:13px;color:var(--navy);">View all →</a></div>
  </div>

  {{-- Break-glass alerts --}}
  <div class="card">
    <div class="card-header">
      <div class="card-title">Active break-glass events</div>
      <div class="card-sub">Emergency accesses pending review</div>
    </div>
    @forelse($breakGlass as $e)
      <div class="entry">
        <div class="entry-head">
          <div>
            <div class="entry-title">{{ $e->patient->user->name }}</div>
            <div class="entry-meta">{{ $e->provider->name }} · {{ $e->reason_code }}</div>
          </div>
          <span class="pill red">Active</span>
        </div>
        <div class="entry-body" style="font-size:12px;">{{ Str::limit($e->justification, 120) }}</div>
      </div>
    @empty
      <div style="padding:16px;color:var(--grey-500);font-size:13px;">No active break-glass events.</div>
    @endforelse
    @if($breakGlass->count())
      <div style="margin-top:12px;"><a href="{{ route('admin.break-glass') }}" style="font-size:13px;color:var(--navy);">View all →</a></div>
    @endif
  </div>
</div>

{{-- Audit log --}}
<div class="card" style="margin-top:20px;">
  <div class="card-header">
    <div class="card-title">Recent audit events</div>
    <div class="card-sub">All system activity across all patients</div>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Action</th><th>Patient</th><th>By</th><th>Description</th><th>When</th></tr></thead>
      <tbody>
        @foreach($recentLogs as $log)
          <tr>
            <td><span class="pill navy">{{ $log->action }}</span></td>
            <td>{{ $log->patient->user->name }}</td>
            <td>{{ $log->actor->name }}</td>
            <td style="max-width:300px;font-size:12px;">{{ Str::limit($log->description, 80) }}</td>
            <td style="font-size:11px;color:var(--grey-500);white-space:nowrap;">{{ $log->created_at->format('d M H:i') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
