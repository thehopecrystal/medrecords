@extends('layouts.app')
@section('title', 'My Team')
@section('topbar-title', 'Team Management')

@section('content')

@if(session('success'))
  <div class="alert green" style="margin-bottom:20px;"><span>✓</span><span>{{ session('success') }}</span></div>
@endif

{{-- ── Super admin: facility picker ───────────────────────── --}}
@if($facilities)
  <div class="page-header">
    <h1>All Facility Teams</h1>
    <p>Manage providers across all registered facilities</p>
  </div>

  @forelse($facilities as $fac)
    <div class="card" style="margin-bottom:20px;">
      <div class="card-header">
        <div>
          <div class="card-title">{{ $fac->name }}</div>
          <div class="card-sub">{{ ucfirst($fac->type) }} · {{ $fac->providers->count() }} provider(s)</div>
        </div>
      </div>

      @if($fac->providers->isEmpty())
        <div style="padding:16px 20px;font-size:13px;color:var(--grey-500);">No providers assigned to this facility yet.</div>
      @else
        @foreach($fac->providers as $p)
          <div class="entry">
            <div class="entry-head">
              <div>
                <div class="entry-title">{{ $p->user->name }}</div>
                <div class="entry-meta">{{ $p->specialty }} · {{ $p->user->email }}</div>
              </div>
              <form method="POST" action="{{ route('admin.team.remove', $p->id) }}" onsubmit="return confirm('Remove this provider from the facility?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm" style="background:var(--red);color:#fff;border:none;cursor:pointer;padding:6px 12px;border-radius:6px;font-size:12px;">Remove</button>
              </form>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  @empty
    <div class="card" style="text-align:center;padding:40px;">
      <div style="font-size:32px;margin-bottom:12px;">🏥</div>
      <div style="font-size:15px;font-weight:600;color:var(--navy);">No facilities registered yet</div>
    </div>
  @endforelse

@else
{{-- ── Facility admin: own facility ───────────────────────── --}}
  <div class="page-header-row">
    <div>
      <h1>{{ $facility->name }}</h1>
      <p style="color:var(--grey-500);font-size:13px;">{{ ucfirst($facility->type) }}
        @if($facility->city || $facility->state) · {{ implode(', ', array_filter([$facility->city, $facility->state])) }} @endif
      </p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Dashboard</a>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
    <div class="stat-card navy"><div class="stat-label">Team members</div><div class="stat-value">{{ $team->count() }}</div></div>
    <div class="stat-card"><div class="stat-label">Available providers</div><div class="stat-value">{{ $available->count() }}</div></div>
  </div>

  {{-- Current team --}}
  <div class="card" style="margin-bottom:20px;">
    <div class="card-header">
      <div class="card-title">Your Team</div>
      <div class="card-sub">Providers currently assigned to {{ $facility->name }}</div>
    </div>
    @forelse($team as $p)
      <div class="entry">
        <div class="entry-head">
          <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--navy),var(--green));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">
              {{ strtoupper(substr($p->user->name,0,1)) }}
            </div>
            <div>
              <div class="entry-title">{{ $p->user->name }}</div>
              <div class="entry-meta">{{ $p->specialty }}
                @if($p->mdcn_folio) · MDCN: {{ $p->mdcn_folio }} @endif
                @if($p->mdcn_status === 'verified') <span class="pill green" style="font-size:10px;">Verified</span> @endif
              </div>
            </div>
          </div>
          <form method="POST" action="{{ route('admin.team.remove', $p->id) }}" onsubmit="return confirm('Remove {{ $p->user->name }} from your facility?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm" style="background:#FEF2F2;color:var(--red);border:1px solid #FECACA;padding:6px 14px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;">Remove</button>
          </form>
        </div>
      </div>
    @empty
      <div style="padding:24px;text-align:center;color:var(--grey-500);font-size:13px;">
        No providers on your team yet. Add providers from the list below.
      </div>
    @endforelse
  </div>

  {{-- Available providers --}}
  @if($available->isNotEmpty())
    <div class="card">
      <div class="card-header">
        <div class="card-title">Available Providers</div>
        <div class="card-sub">Registered providers not yet assigned to a facility</div>
      </div>
      @foreach($available as $p)
        <div class="entry">
          <div class="entry-head">
            <div style="display:flex;align-items:center;gap:12px;">
              <div style="width:38px;height:38px;border-radius:50%;background:var(--grey-100);color:var(--navy);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">
                {{ strtoupper(substr($p->user->name,0,1)) }}
              </div>
              <div>
                <div class="entry-title">{{ $p->user->name }}</div>
                <div class="entry-meta">{{ $p->specialty }} · {{ $p->user->email }}</div>
              </div>
            </div>
            <form method="POST" action="{{ route('admin.team.add') }}">
              @csrf
              <input type="hidden" name="provider_id" value="{{ $p->id }}">
              <button type="submit" class="btn btn-primary btn-sm" style="font-size:12px;padding:6px 14px;">Add to team</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="card" style="text-align:center;padding:32px;">
      <div style="font-size:28px;margin-bottom:10px;">🩺</div>
      <div style="font-size:14px;font-weight:600;color:var(--navy);margin-bottom:6px;">No unassigned providers</div>
      <div style="font-size:13px;color:var(--grey-500);">Share your facility with providers so they can register and join your team.</div>
    </div>
  @endif

@endif
@endsection
