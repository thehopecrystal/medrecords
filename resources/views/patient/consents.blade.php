@extends('layouts.app')
@section('title', 'My Consents')
@section('topbar-title', 'Consent Management')

@section('content')
<div class="page-header">
  <h1>My Consents</h1>
  <p>Who can see what, for how long</p>
</div>

{{-- Pending requests --}}
@if($pending->count())
<div class="card card-accent-navy mb-20">
  <div class="card-header">
    <div class="card-title">Pending consent requests <span class="pill navy">{{ $pending->count() }}</span></div>
    <div class="card-sub">These providers are asking to access your record — approve or decline each one.</div>
  </div>
  @foreach($pending as $c)
    <div class="entry">
      <div class="entry-head entry-head-top">
        <div class="flex-1">
          <div class="entry-title">{{ $c->grantee->name }} — <span class="text-navy-link">{{ $c->scopeLabel }}</span></div>
          <div class="entry-meta">{{ ucfirst($c->grantee->role) }} · Requested {{ $c->created_at->diffForHumans() }} · Valid for {{ $c->expires_at->diffForHumans(null, true) }}</div>
          <div class="consent-scope-box">
            <strong>Scope:</strong> {{ $c->scopeLabel }}<br>
            <strong>Expires:</strong> {{ $c->expires_at->format('d M Y') }}<br>
            <strong>Access type:</strong> Read + write
          </div>
          <p class="consent-note">You can revoke this at any time. All access is logged.</p>
        </div>
        <div class="entry-actions">
          <form method="POST" action="{{ route('patient.consents.decline', $c->id) }}">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-secondary btn-sm">Decline</button>
          </form>
          <form method="POST" action="{{ route('patient.consents.approve', $c->id) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-green btn-sm">Approve</button>
          </form>
        </div>
      </div>
    </div>
  @endforeach
</div>
@endif

{{-- Active --}}
<div class="card mb-20">
  <div class="card-header">
    <div class="card-title">Active consents <span class="pill green">{{ $active->count() }}</span></div>
  </div>
  @forelse($active as $c)
    <div class="entry">
      <div class="entry-head">
        <div>
          <div class="entry-title">{{ $c->grantee->name }} <span class="pill navy" style="margin-left:6px;">{{ $c->scopeLabel }}</span></div>
          <div class="entry-meta">{{ $c->grantee->role }} · expires {{ $c->expires_at ? $c->expires_at->format('d M Y') : 'no expiry' }}</div>
        </div>
        <form method="POST" action="{{ route('patient.consents.revoke', $c->id) }}"
              data-confirm="Revoke this consent?"
              data-confirm-ok="Revoke">
          @csrf @method('PATCH')
          <button type="submit" class="btn btn-danger btn-sm">Revoke</button>
        </form>
      </div>
    </div>
  @empty
    <div class="card-empty-body">No active consents.</div>
  @endforelse
</div>

{{-- Expired --}}
@if($expired->count())
<div class="card mb-20">
  <div class="card-header">
    <div class="card-title">Expired consents</div>
  </div>
  @foreach($expired as $c)
    <div class="entry">
      <div class="entry-head">
        <div>
          <div class="entry-title">{{ $c->grantee->name }}</div>
          <div class="entry-meta">{{ $c->scopeLabel }} · expired {{ $c->expires_at?->format('d M Y') }}</div>
        </div>
        <span class="pill grey">Expired</span>
      </div>
    </div>
  @endforeach
</div>
@endif

{{-- Revoked --}}
@if($revoked->count())
<div class="card">
  <div class="card-header">
    <div class="card-title">Revoked consents</div>
  </div>
  @foreach($revoked as $c)
    <div class="entry">
      <div class="entry-head">
        <div>
          <div class="entry-title">{{ $c->grantee->name }}</div>
          <div class="entry-meta">{{ $c->scopeLabel }} · revoked {{ $c->revoked_at?->format('d M Y') }}</div>
        </div>
        <span class="pill red">Revoked</span>
      </div>
    </div>
  @endforeach
</div>
@endif
@endsection
