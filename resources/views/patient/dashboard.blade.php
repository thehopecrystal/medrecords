@extends('layouts.app')
@section('title', 'Dashboard')
@section('topbar-title', 'Patient Dashboard')


@section('content')
<div class="page-header-row">
  <div>
    <p class="page-header-sub">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},</p>
    <h1 class="page-header">{{ explode(' ', auth()->user()->name)[0] }}</h1>
  </div>
</div>

{{-- MedID card --}}
<div class="medid-card" style="margin-bottom:24px;max-width:400px;">
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
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:28px;max-width:600px;">
  <a href="{{ route('patient.access-code') }}" style="background:#fff;border-radius:12px;padding:16px 8px;text-align:center;box-shadow:var(--shadow);text-decoration:none;">
    <div style="font-size:22px;">📱</div>
    <div style="font-size:11px;color:var(--grey-700);margin-top:6px;font-weight:500;">Share record</div>
  </a>
  <a href="{{ route('patient.consents') }}" style="background:#fff;border-radius:12px;padding:16px 8px;text-align:center;box-shadow:var(--shadow);text-decoration:none;">
    <div style="font-size:22px;">🔐</div>
    <div style="font-size:11px;color:var(--grey-700);margin-top:6px;font-weight:500;">Consents</div>
  </a>
  <a href="{{ route('patient.record') }}" style="background:#fff;border-radius:12px;padding:16px 8px;text-align:center;box-shadow:var(--shadow);text-decoration:none;">
    <div style="font-size:22px;">📋</div>
    <div style="font-size:11px;color:var(--grey-700);margin-top:6px;font-weight:500;">My record</div>
  </a>
  <a href="{{ route('patient.audit-log') }}" style="background:#fff;border-radius:12px;padding:16px 8px;text-align:center;box-shadow:var(--shadow);text-decoration:none;">
    <div style="font-size:22px;">👁</div>
    <div style="font-size:11px;color:var(--grey-700);margin-top:6px;font-weight:500;">Who saw me</div>
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
    <div style="padding:24px;text-align:center;color:var(--grey-500);font-size:13px;">No recent activity.</div>
  @endforelse
</div>
@endsection
