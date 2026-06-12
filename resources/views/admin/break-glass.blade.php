@extends('layouts.app')
@section('title', 'Break-Glass Review')
@section('topbar-title', 'Break-Glass Review')

@section('content')
<div class="page-header-row">
  <div>
    <h1>Break-Glass Events</h1>
    <p>Emergency access events — review and resolve within 7 days.</p>
  </div>
</div>

{{-- Status filter tabs --}}
<div class="tab-strip" style="margin-bottom:24px;">
  <a href="{{ route('admin.break-glass') }}"
     class="tab-item {{ !request('status') ? 'active' : '' }}">
    All
    <span class="tab-count">{{ array_sum($counts) }}</span>
  </a>
  <a href="{{ route('admin.break-glass', ['status' => 'active']) }}"
     class="tab-item {{ request('status') === 'active' ? 'active' : '' }}">
    Active
    @if($counts['active'] > 0)<span class="tab-count" style="background:var(--red-lt);color:var(--red);">{{ $counts['active'] }}</span>@endif
  </a>
  <a href="{{ route('admin.break-glass', ['status' => 'approved']) }}"
     class="tab-item {{ request('status') === 'approved' ? 'active' : '' }}">
    Approved
    @if($counts['approved'] > 0)<span class="tab-count">{{ $counts['approved'] }}</span>@endif
  </a>
  <a href="{{ route('admin.break-glass', ['status' => 'disputed']) }}"
     class="tab-item {{ request('status') === 'disputed' ? 'active' : '' }}">
    Disputed
    @if($counts['disputed'] > 0)<span class="tab-count" style="background:var(--amber-lt);color:var(--amber);">{{ $counts['disputed'] }}</span>@endif
  </a>
  <a href="{{ route('admin.break-glass', ['status' => 'closed']) }}"
     class="tab-item {{ request('status') === 'closed' ? 'active' : '' }}">
    Closed
    @if($counts['closed'] > 0)<span class="tab-count">{{ $counts['closed'] }}</span>@endif
  </a>
</div>

@forelse($events as $e)
@php
  $statusPill = match($e->status) {
    'active'   => 'red',
    'approved' => 'green',
    'disputed' => 'amber',
    default    => 'grey',
  };
@endphp
<div class="card" style="margin-bottom:14px;{{ $e->status === 'active' ? 'border-left:3px solid var(--red);' : '' }}">
  <div class="card-pad">
    <div style="display:grid;grid-template-columns:1fr auto;gap:20px;align-items:flex-start;">
      {{-- Left: event info --}}
      <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;flex-wrap:wrap;">
          <span style="font-size:15px;font-weight:700;color:var(--navy);">
            {{ $e->patient?->user?->name ?? 'Unknown patient' }}
          </span>
          <span class="pill {{ $statusPill }}">{{ ucfirst($e->status) }}</span>
        </div>

        <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:var(--text-2);margin-bottom:10px;">
          <span>🩺 <strong>{{ $e->provider?->name ?? '—' }}</strong></span>
          <span>🕐 {{ $e->created_at->format('d M Y, H:i') }}</span>
          <span>📋 {{ str_replace('_', ' ', ucfirst($e->reason_code ?? '—')) }}</span>
        </div>

        @if($e->justification)
        <div style="background:var(--navy-lt);padding:10px 14px;border-radius:8px;font-size:13px;color:var(--text);line-height:1.5;">
          {{ $e->justification }}
        </div>
        @endif

        @if($e->review_notes)
        <div style="margin-top:8px;font-size:12px;color:var(--text-2);font-style:italic;">
          Review note: {{ $e->review_notes }}
        </div>
        @endif

        @if($e->reviewed_by && $e->reviewed_at)
        <div style="margin-top:6px;font-size:11px;color:var(--text-3);">
          Reviewed {{ $e->reviewed_at->diffForHumans() }}
        </div>
        @endif
      </div>

      {{-- Right: actions --}}
      @if(in_array($e->status, ['active', 'under_review']))
      <div style="display:flex;flex-direction:column;gap:8px;min-width:160px;">
        <form method="POST" action="{{ route('admin.break-glass.approve', $e->id) }}">
          @csrf
          <input type="hidden" name="review_notes" value="Reviewed and approved by compliance.">
          <button type="submit" class="btn btn-green btn-sm" style="width:100%;">✓ Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.break-glass.dispute', $e->id) }}"
              onsubmit="return confirm('Mark as disputed? The patient will be notified.')">
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
      <div style="min-width:160px;">
        <form method="POST" action="{{ route('admin.break-glass.close', $e->id) }}">
          @csrf
          <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;">Close investigation</button>
        </form>
      </div>
      @endif
    </div>
  </div>
</div>
@empty
<div class="card">
  <div class="empty-state">
    <div class="empty-icon">✅</div>
    <h3>No events found</h3>
    <p>
      @if(request('status'))
        No {{ request('status') }} break-glass events.
      @else
        No break-glass events have been recorded yet.
      @endif
    </p>
  </div>
</div>
@endforelse

@if($events->hasPages())
<div style="margin-top:16px;">{{ $events->links() }}</div>
@endif
@endsection
