@extends('layouts.app')
@section('title', 'Break-Glass Review')
@section('topbar-title', 'Break-Glass Review')

@section('sidebar')
<aside class="sidebar">
  <div class="sidebar-brand">MedRecords<div class="sub">Administration</div></div>
  <nav class="sidebar-nav">
    <a href="{{ route('admin.dashboard') }}" class="nav-item">📊 Dashboard</a>
    <a href="{{ route('admin.facilities') }}" class="nav-item">🏥 Facilities</a>
    <a href="{{ route('admin.break-glass') }}" class="nav-item active">⚡ Break-glass review</a>
    <a href="{{ route('admin.providers') }}" class="nav-item">🩺 MDCN verification</a>
  </nav>
  <div class="sidebar-footer"><div class="name">{{ auth()->user()->name }}</div>Administrator</div>
</aside>
@endsection

@section('content')
<div class="page-header">
  <h1>Break-Glass Events</h1>
  <p>All emergency access events — review within 7 days</p>
</div>

@forelse($events as $e)
  <div class="card" style="margin-bottom:16px;{{ $e->status === 'active' ? 'border-left:4px solid var(--red);' : '' }}">
    <div style="display:grid;grid-template-columns:1fr auto;gap:16px;align-items:flex-start;">
      <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
          <span style="font-size:15px;font-weight:700;color:var(--navy);">{{ $e->patient->user->name }}</span>
          <span class="pill {{ match($e->status) { 'active' => 'red', 'approved' => 'green', 'disputed' => 'amber', 'closed' => 'grey', default => 'grey' } }}">{{ $e->status }}</span>
        </div>
        <div style="font-size:12px;color:var(--grey-500);margin-bottom:8px;">
          Provider: <strong>{{ $e->provider->name }}</strong> ·
          {{ $e->created_at->format('d M Y H:i') }} ·
          Reason: {{ str_replace('_', ' ', $e->reason_code) }}
        </div>
        <div style="background:var(--grey-50);padding:10px 12px;border-radius:8px;font-size:13px;color:var(--grey-700);">
          {{ $e->justification }}
        </div>
        @if($e->review_notes)
          <div style="margin-top:8px;font-size:12px;color:var(--grey-500);font-style:italic;">
            Review note: {{ $e->review_notes }}
          </div>
        @endif
      </div>

      @if(in_array($e->status, ['active', 'under_review']))
        <div style="display:flex;flex-direction:column;gap:8px;min-width:200px;">
          <form method="POST" action="{{ route('admin.break-glass.approve', $e->id) }}">
            @csrf
            <input type="hidden" name="review_notes" value="Reviewed and approved by compliance.">
            <button type="submit" class="btn btn-green btn-sm" style="width:100%;">✓ Approve</button>
          </form>
          <form method="POST" action="{{ route('admin.break-glass.dispute', $e->id) }}" onsubmit="return confirm('Mark as disputed? Patient will be notified.')">
            @csrf
            <input type="hidden" name="review_notes" value="Under investigation by compliance team.">
            <button type="submit" class="btn btn-danger btn-sm" style="width:100%;">⚠ Dispute</button>
          </form>
          <form method="POST" action="{{ route('admin.break-glass.close', $e->id) }}">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;">Close</button>
          </form>
        </div>
      @elseif($e->status === 'disputed')
        <div style="display:flex;flex-direction:column;gap:8px;min-width:160px;">
          <form method="POST" action="{{ route('admin.break-glass.close', $e->id) }}">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;">Close investigation</button>
          </form>
        </div>
      @endif
    </div>
  </div>
@empty
  <div class="card" style="text-align:center;padding:40px;color:var(--grey-500);">No break-glass events found.</div>
@endforelse

<div style="margin-top:16px;">{{ $events->links() }}</div>
@endsection
