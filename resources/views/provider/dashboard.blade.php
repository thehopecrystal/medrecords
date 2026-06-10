@extends('layouts.app')
@section('title', 'Provider Workstation')
@section('topbar-title', 'Provider Workstation')


@section('content')
<div class="page-header-row">
  <div>
    <h1>Good {{ now()->hour < 12 ? 'morning' : 'afternoon' }}, Dr {{ explode(' ', auth()->user()->name)[count(explode(' ', auth()->user()->name))-1] }}</h1>
    <p style="color:var(--grey-500);font-size:13px;">{{ now()->format('l, d F Y') }}</p>
  </div>
  <a href="{{ route('provider.search') }}" class="btn btn-primary">+ New encounter</a>
</div>

@if(!$provider->facility_id)
  <div class="alert amber" style="margin-bottom:20px;">
    <span>📋</span>
    <span><strong>Welcome!</strong> Your account is active but you are not yet assigned to a facility. A facility administrator can add you to their team from their Team Management page.</span>
  </div>
@endif

@if(session('onboarding'))
  <div class="alert green" style="margin-bottom:20px;">
    <span>✓</span>
    <span><strong>Account created successfully.</strong> Complete your profile and wait for a facility to add you to their team.</span>
  </div>
@endif

<div class="stat-grid">
  <div class="stat-card"><div class="stat-label">Encounters today</div><div class="stat-value">{{ $recentEncounters->where('created_at', '>=', today())->count() }}</div></div>
  <div class="stat-card green"><div class="stat-label">This week</div><div class="stat-value">{{ $recentEncounters->where('created_at', '>=', now()->startOfWeek())->count() }}</div></div>
  <div class="stat-card navy"><div class="stat-label">Total encounters</div><div class="stat-value">{{ $recentEncounters->count() }}</div></div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Recent encounters</div>
    <div class="card-sub">Your most recent patient interactions</div>
  </div>
  @forelse($recentEncounters as $enc)
    <div class="entry">
      <div class="entry-head">
        <div>
          <div class="entry-title">
            <a href="{{ route('provider.patient', $enc->patient_id) }}" style="color:inherit;text-decoration:none;">
              {{ $enc->patient->user->name }}
            </a>
            — {{ $enc->chief_complaint }}
          </div>
          <div class="entry-meta">{{ $enc->facility?->name ?? 'No facility' }} · {{ ucfirst($enc->type) }}</div>
        </div>
        <div style="text-align:right;">
          <div class="entry-date">{{ $enc->created_at->format('d M Y') }}</div>
          <span class="pill {{ $enc->status === 'signed' ? 'green' : 'amber' }}">{{ $enc->status }}</span>
        </div>
      </div>
    </div>
  @empty
    <div style="padding:24px;text-align:center;color:var(--grey-500);font-size:13px;">No encounters yet. <a href="{{ route('provider.search') }}" style="color:var(--navy);">Search for a patient</a> to begin.</div>
  @endforelse
</div>
@endsection
