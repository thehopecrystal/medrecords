@extends('layouts.app')
@section('title', 'Access History')
@section('topbar-title', 'Who Saw My Record')

@section('sidebar')
<aside class="sidebar">
  <div class="sidebar-brand">MedRecords<div class="sub">Patient Portal</div></div>
  <nav class="sidebar-nav">
    <a href="{{ route('patient.dashboard') }}" class="nav-item">🏠 Home</a>
    <a href="{{ route('patient.record') }}" class="nav-item">📋 My Record</a>
    <a href="{{ route('patient.consents') }}" class="nav-item">🔐 Consents</a>
    <a href="{{ route('patient.access-code') }}" class="nav-item">📱 Share Record</a>
    <a href="{{ route('patient.audit-log') }}" class="nav-item active">👁 Access History</a>
    <a href="{{ route('patient.family') }}" class="nav-item">👥 My Family</a>
  </nav>
  <div class="sidebar-footer"><div class="name">{{ auth()->user()->name }}</div>Patient</div>
</aside>
@endsection

@section('content')
<div class="page-header">
  <h1>Access History</h1>
  <p>Every access to your medical record is logged here</p>
</div>

{{-- Filter bar --}}
<div class="card" style="margin-bottom:20px;">
  <form method="GET" action="{{ route('patient.audit-log') }}" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:12px;align-items:flex-end;">
    <div class="form-field" style="margin:0;">
      <label>Action type</label>
      <select name="action">
        <option value="">All actions</option>
        @foreach($actions as $a)
          <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ $a }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-field" style="margin:0;">
      <label>Person</label>
      <input type="text" name="actor" value="{{ request('actor') }}" placeholder="Name…">
    </div>
    <div class="form-field" style="margin:0;">
      <label>From date</label>
      <input type="date" name="from" value="{{ request('from') }}">
    </div>
    <div class="form-field" style="margin:0;">
      <label>To date</label>
      <input type="date" name="to" value="{{ request('to') }}">
    </div>
    <div style="display:flex;gap:8px;">
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      <a href="{{ route('patient.audit-log') }}" class="btn btn-secondary btn-sm">Clear</a>
    </div>
  </form>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Action</th>
          <th>By</th>
          <th>Description</th>
          <th>When</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          <tr>
            <td><span class="pill {{ match(true) {
              str_contains($log->action,'break_glass') => 'red',
              str_contains($log->action,'consent') => 'navy',
              str_contains($log->action,'dispense') => 'amber',
              default => 'grey'
            } }}">{{ $log->action }}</span></td>
            <td>{{ $log->actor->name }}</td>
            <td style="max-width:380px;">{{ $log->description }}</td>
            <td style="white-space:nowrap;font-size:12px;color:var(--grey-500);">{{ $log->created_at->format('d M Y H:i') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4" style="text-align:center;color:var(--grey-500);padding:24px;">
              No access history {{ request()->hasAny(['action','actor','from','to']) ? 'matching your filters.' : '.' }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px;">{{ $logs->links() }}</div>
</div>
@endsection
