@extends('layouts.app')
@section('title', 'Dashboard')
@section('topbar-title', 'Patient Dashboard')

@section('content')
<div class="page-header-row">
  <div>
    <p class="dash-greeting-sub">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},</p>
    <h1 class="dash-greeting-name">{{ explode(' ', auth()->user()->name)[0] }}</h1>
  </div>
</div>

{{-- MedID card --}}
<div class="medid-card mb-24 max-400">
  <div class="label">MedRecords ID</div>
  <div class="number">{{ $patient->med_id }}</div>
  <div class="row">
    <span>{{ auth()->user()->name }}{{ $patient->dob ? ' · ' . $patient->age : '' }}</span>
    @if($patient->nin_verified)
      <span class="verified">✓ NIN verified</span>
    @endif
  </div>
</div>

{{-- Quick actions --}}
<div class="quick-actions">
  <a href="{{ route('patient.access-code') }}" class="quick-action-card">
    <span class="quick-action-icon">📱</span>
    <span class="quick-action-label">Share record</span>
  </a>
  <a href="{{ route('patient.consents') }}" class="quick-action-card">
    <span class="quick-action-icon">🔐</span>
    <span class="quick-action-label">Consents</span>
  </a>
  <a href="{{ route('patient.record') }}" class="quick-action-card">
    <span class="quick-action-icon">📋</span>
    <span class="quick-action-label">My record</span>
  </a>
  <a href="{{ route('patient.audit-log') }}" class="quick-action-card">
    <span class="quick-action-icon">👁</span>
    <span class="quick-action-label">Who saw me</span>
  </a>
</div>

{{-- Recent activity --}}
<div class="card">
  <div class="card-header">
    <div class="card-title">Recent activity</div>
    <div class="card-sub">What happened with your record recently</div>
  </div>
  @forelse($recentActivity as $log)
    <div class="entry">
      <div class="entry-head">
        <div>
          <div class="entry-title">{{ $log->description }}</div>
          <div class="entry-meta">{{ $log->actor->name }}</div>
        </div>
        <div class="entry-date">{{ $log->created_at->diffForHumans() }}</div>
      </div>
    </div>
  @empty
    <div class="card-empty-body text-center">No recent activity.</div>
  @endforelse
</div>
@endsection
