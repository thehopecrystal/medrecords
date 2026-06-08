@extends('layouts.app')
@section('title', 'Provider Verification')
@section('topbar-title', 'MDCN Verification')

@section('sidebar')
<aside class="sidebar">
  <div class="sidebar-brand">MedRecords<div class="sub">Administration</div></div>
  <nav class="sidebar-nav">
    <a href="{{ route('admin.dashboard') }}" class="nav-item">📊 Dashboard</a>
    <a href="{{ route('admin.facilities') }}" class="nav-item">🏥 Facilities</a>
    <a href="{{ route('admin.break-glass') }}" class="nav-item">⚡ Break-glass review</a>
    <a href="{{ route('admin.providers') }}" class="nav-item active">🩺 MDCN verification</a>
  </nav>
  <div class="sidebar-footer"><div class="name">{{ auth()->user()->name }}</div>Administrator</div>
</aside>
@endsection

@section('content')
<div class="page-header">
  <h1>Provider MDCN Verification</h1>
  <p>Verify clinician licenses against the MDCN portal</p>
</div>

<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Provider</th><th>Facility</th><th>Specialty</th><th>MDCN Folio</th><th>Status</th><th>Action</th></tr>
      </thead>
      <tbody>
        @forelse($providers as $p)
          <tr>
            <td><strong>{{ $p->user->name }}</strong><br><span style="font-size:11px;color:var(--grey-500);">{{ $p->user->email }}</span></td>
            <td>{{ $p->facility->name }}</td>
            <td>{{ $p->specialty }}</td>
            <td style="font-family:monospace;font-size:12px;">{{ $p->mdcn_folio ?: '—' }}</td>
            <td>
              @if($p->mdcn_verified)
                <span class="pill green">✓ Verified</span>
                <div style="font-size:10px;color:var(--grey-500);margin-top:2px;">{{ $p->mdcn_verified_at?->format('d M Y') }}</div>
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
                  {{ $p->mdcn_verified ? 'Re-verify' : 'Verify now' }}
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;color:var(--grey-500);padding:24px;">No providers registered.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px;">{{ $providers->links() }}</div>
</div>
@endsection
