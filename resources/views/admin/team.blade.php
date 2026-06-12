@extends('layouts.app')
@section('title', 'Team Management')
@section('topbar-title', 'Team Management')

@section('content')

{{-- ── Super admin: all facility teams ──────────────────────────── --}}
@if($facilities)
  <div class="page-header">
    <h1>All Facility Teams</h1>
    <p>Manage providers across all registered facilities.</p>
  </div>

  @forelse($facilities as $fac)
    <div class="card" style="margin-bottom:20px;">
      <div class="card-header">
        <div>
          <div class="card-title">{{ $fac->name }}</div>
          <div class="card-sub">{{ ucfirst($fac->type ?? 'Facility') }} · {{ $fac->providers->count() }} provider(s)</div>
        </div>
        <span class="pill navy">{{ ucfirst($fac->type ?? 'facility') }}</span>
      </div>

      @if($fac->providers->isEmpty())
        <div class="card-body" style="color:var(--text-2);font-size:13px;">
          No providers assigned to this facility yet.
        </div>
      @else
        <div class="card-body" style="padding:0;">
          @foreach($fac->providers as $p)
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 22px;border-bottom:1px solid var(--border);">
              <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--green));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
                  {{ strtoupper(substr($p->user->name, 0, 1)) }}
                </div>
                <div>
                  <div style="font-size:13px;font-weight:600;">{{ $p->user->name }}</div>
                  <div style="font-size:11px;color:var(--text-2);">{{ $p->specialty }} · {{ $p->user->email }}</div>
                </div>
              </div>
              <form method="POST" action="{{ route('admin.team.remove', $p->id) }}"
                    onsubmit="return confirm('Remove {{ addslashes($p->user->name) }} from this facility?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
              </form>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  @empty
    <div class="card">
      <div class="empty-state">
        <div class="empty-icon">🏥</div>
        <h3>No facilities registered yet</h3>
        <p>Facilities will appear here once they're added to the platform.</p>
      </div>
    </div>
  @endforelse

@else
{{-- ── Facility admin: own facility ─────────────────────────────── --}}
  <div class="page-header-row">
    <div>
      <h1>{{ $facility->name }}</h1>
      <p>{{ ucfirst($facility->type ?? 'Facility') }}
        @if($facility->city || $facility->state)
          · {{ implode(', ', array_filter([$facility->city, $facility->state])) }}
        @endif
      </p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Dashboard</a>
  </div>

  <div class="stat-grid" style="grid-template-columns:1fr 1fr;margin-bottom:24px;">
    <div class="stat-card">
      <div class="stat-label">Team Members</div>
      <div class="stat-value">{{ $team->count() }}</div>
      <div class="stat-icon">👨‍⚕️</div>
    </div>
    <div class="stat-card green">
      <div class="stat-label">Available Providers</div>
      <div class="stat-value">{{ $available->count() }}</div>
      <div class="stat-sub">Not yet assigned</div>
      <div class="stat-icon">🩺</div>
    </div>
  </div>

  {{-- Current team --}}
  <div class="card" style="margin-bottom:20px;">
    <div class="card-header">
      <div>
        <div class="card-title">Your Team</div>
        <div class="card-sub">Providers currently assigned to {{ $facility->name }}.</div>
      </div>
    </div>
    @if($team->isEmpty())
      <div class="empty-state" style="padding:32px;">
        <div class="empty-icon">👨‍⚕️</div>
        <h3>No team members yet</h3>
        <p>Add providers from the list below to build your team.</p>
      </div>
    @else
      <div class="card-body" style="padding:0;">
        @foreach($team as $p)
          <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 22px;border-bottom:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:12px;">
              <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--green));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">
                {{ strtoupper(substr($p->user->name, 0, 1)) }}
              </div>
              <div>
                <div style="font-size:14px;font-weight:600;">{{ $p->user->name }}</div>
                <div style="font-size:12px;color:var(--text-2);">
                  {{ $p->specialty }}
                  @if($p->mdcn_folio) · MDCN: {{ $p->mdcn_folio }} @endif
                  @if($p->mdcn_status === 'verified')
                    <span class="pill green" style="font-size:10px;margin-left:4px;">Verified</span>
                  @endif
                </div>
              </div>
            </div>
            <form method="POST" action="{{ route('admin.team.remove', $p->id) }}"
                  onsubmit="return confirm('Remove {{ addslashes($p->user->name) }} from your facility?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">Remove</button>
            </form>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Available providers --}}
  @if($available->isNotEmpty())
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Available Providers</div>
          <div class="card-sub">Registered providers not yet assigned to any facility.</div>
        </div>
      </div>
      <div class="card-body" style="padding:0;">
        @foreach($available as $p)
          <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 22px;border-bottom:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:12px;">
              <div style="width:40px;height:40px;border-radius:50%;background:var(--navy-lt);color:var(--navy);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">
                {{ strtoupper(substr($p->user->name, 0, 1)) }}
              </div>
              <div>
                <div style="font-size:14px;font-weight:600;">{{ $p->user->name }}</div>
                <div style="font-size:12px;color:var(--text-2);">{{ $p->specialty }} · {{ $p->user->email }}</div>
              </div>
            </div>
            <form method="POST" action="{{ route('admin.team.add') }}">
              @csrf
              <input type="hidden" name="provider_id" value="{{ $p->id }}">
              <button type="submit" class="btn btn-primary btn-sm">+ Add to team</button>
            </form>
          </div>
        @endforeach
      </div>
    </div>
  @else
    <div class="card">
      <div class="empty-state">
        <div class="empty-icon">🩺</div>
        <h3>No unassigned providers</h3>
        <p>All registered providers are already assigned to a facility.</p>
      </div>
    </div>
  @endif
@endif
@endsection
