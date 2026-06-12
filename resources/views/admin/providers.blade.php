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
<div class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
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
<div class="card" style="margin-bottom:20px;">
  <div class="card-pad">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
      <div class="form-field" style="flex:1;min-width:200px;">
        <label>Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Provider name, email, or MDCN folio…">
      </div>
      <div class="form-field" style="width:160px;">
        <label>Status</label>
        <select name="status">
          <option value="">All providers</option>
          <option value="verified"   {{ request('status') === 'verified'   ? 'selected' : '' }}>Verified</option>
          <option value="unverified" {{ request('status') === 'unverified' ? 'selected' : '' }}>Unverified</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm" style="align-self:flex-end;">Filter</button>
      @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.providers') }}" class="btn btn-secondary btn-sm" style="align-self:flex-end;">Clear</a>
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
          <th style="width:130px;">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($providers as $p)
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px;">
              <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--green));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
                {{ strtoupper(substr($p->user->name, 0, 1)) }}
              </div>
              <div>
                <div style="font-weight:600;">{{ $p->user->name }}</div>
                <div style="font-size:11px;color:var(--text-3);">{{ $p->user->email }}</div>
              </div>
            </div>
          </td>
          <td style="font-size:13px;">{{ $p->facility?->name ?? '—' }}</td>
          <td style="font-size:13px;">{{ $p->specialty ?? '—' }}</td>
          <td style="font-family:monospace;font-size:12px;color:var(--text-2);">
            {{ $p->mdcn_folio ?: '—' }}
          </td>
          <td>
            @if($p->mdcn_verified)
              <span class="pill green">✓ Verified</span>
              @if($p->mdcn_verified_at)
              <div style="font-size:10px;color:var(--text-3);margin-top:3px;">
                {{ $p->mdcn_verified_at->format('d M Y') }}
              </div>
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
  <div style="padding:14px 22px;border-top:1px solid var(--border);">
    {{ $providers->links() }}
  </div>
  @endif
</div>
@endsection
