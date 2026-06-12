@extends('layouts.app')
@section('title', 'Provider Verification')
@section('topbar-title', 'MDCN Verification')

@section('content')
<div class="page-header-row">
  <div>
    <h1>Provider MDCN Verification</h1>
    <p>Verify clinician licences against the MDCN portal.</p>
  </div>
</div>

{{-- Stats --}}
<div class="stat-grid stat-grid-3 mb-20">
  <div class="stat-card">
    <div class="stat-label">Total Providers</div>
    <div class="stat-value">{{ number_format($counts['all']) }}</div>
    <div class="stat-icon">🩺</div>
  </div>
  <div class="stat-card green">
    <div class="stat-label">Verified</div>
    <div class="stat-value">{{ number_format($counts['verified']) }}</div>
    <div class="stat-icon">✅</div>
  </div>
  <div class="stat-card amber">
    <div class="stat-label">Unverified</div>
    <div class="stat-value">{{ number_format($counts['unverified']) }}</div>
    <div class="stat-icon">⏳</div>
  </div>
</div>

{{-- Filters --}}
<div class="card mb-20">
  <div class="card-pad">
    <form method="GET" class="filter-row">
      <div class="form-field filter-field-flex">
        <label>Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Provider name, email, or MDCN folio…">
      </div>
      <div class="form-field filter-field">
        <label>Status</label>
        <select name="status">
          <option value="">All providers</option>
          <option value="verified"   {{ request('status') === 'verified'   ? 'selected' : '' }}>Verified</option>
          <option value="unverified" {{ request('status') === 'unverified' ? 'selected' : '' }}>Unverified</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm self-end">Filter</button>
      @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.providers') }}" class="btn btn-secondary btn-sm self-end">Clear</a>
      @endif
    </form>
  </div>
</div>

{{-- Table --}}
<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Provider</th>
          <th>Facility</th>
          <th>Specialty</th>
          <th>MDCN Folio</th>
          <th>Status</th>
          <th class="th-130">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($providers as $p)
        <tr>
          <td>
            <div class="person-cell">
              <div class="avatar avatar-md">{{ strtoupper(substr($p->user->name, 0, 1)) }}</div>
              <div>
                <div class="td-fw">{{ $p->user->name }}</div>
                <div class="td-xs-muted">{{ $p->user->email }}</div>
              </div>
            </div>
          </td>
          <td class="td-sm">{{ $p->facility?->name ?? '—' }}</td>
          <td class="td-sm">{{ $p->specialty ?? '—' }}</td>
          <td class="td-mono">{{ $p->mdcn_folio ?: '—' }}</td>
          <td>
            @if($p->mdcn_verified)
              <span class="pill green">✓ Verified</span>
              @if($p->mdcn_verified_at)
              <div class="td-xs-top">{{ $p->mdcn_verified_at->format('d M Y') }}</div>
              @endif
            @elseif($p->mdcn_status === 'pending')
              <span class="pill amber">Pending</span>
            @else
              <span class="pill grey">Unverified</span>
            @endif
          </td>
          <td>
            <form method="POST" action="{{ route('admin.mdcn.verify', $p->id) }}">
              @csrf
              <button type="submit" class="btn btn-secondary btn-sm">
                {{ $p->mdcn_verified ? '↺ Re-verify' : 'Verify now' }}
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <div class="empty-icon">🩺</div>
              <h3>No providers found</h3>
              <p>
                @if(request()->hasAny(['search','status']))
                  Try adjusting your filters.
                @else
                  No providers have registered yet.
                @endif
              </p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($providers->hasPages())
  <div class="pager-row">{{ $providers->links() }}</div>
  @endif
</div>
@endsection
