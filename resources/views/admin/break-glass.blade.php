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
<div class="tab-strip mb-24">
  <a href="{{ route('admin.break-glass') }}"
     class="tab-item {{ !request('status') ? 'active' : '' }}">
    All
    <span class="tab-count">{{ array_sum($counts) }}</span>
  </a>
  <a href="{{ route('admin.break-glass', ['status' => 'active']) }}"
     class="tab-item {{ request('status') === 'active' ? 'active' : '' }}">
    Active
    @if($counts['active'] > 0)<span class="tab-count tab-count-red">{{ $counts['active'] }}</span>@endif
  </a>
  <a href="{{ route('admin.break-glass', ['status' => 'approved']) }}"
     class="tab-item {{ request('status') === 'approved' ? 'active' : '' }}">
    Approved
    @if($counts['approved'] > 0)<span class="tab-count">{{ $counts['approved'] }}</span>@endif
  </a>
  <a href="{{ route('admin.break-glass', ['status' => 'disputed']) }}"
     class="tab-item {{ request('status') === 'disputed' ? 'active' : '' }}">
    Disputed
    @if($counts['disputed'] > 0)<span class="tab-count tab-count-amber">{{ $counts['disputed'] }}</span>@endif
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
<div class="card mb-14{{ $e->status === 'active' ? ' card-active' : '' }}">
  <div class="card-pad">
    <div class="bge-grid">
      {{-- Left: event info --}}
      <div>
        <div class="bge-head">
          <span class="bge-name">{{ $e->patient?->user?->name ?? 'Unknown patient' }}</span>
          <span class="pill {{ $statusPill }}">{{ ucfirst($e->status) }}</span>
        </div>

        <div class="bge-meta">
          <span>🩺 <strong>{{ $e->provider?->name ?? '—' }}</strong></span>
          <span>🕐 {{ $e->created_at->format('d M Y, H:i') }}</span>
          <span>📋 {{ str_replace('_', ' ', ucfirst($e->reason_code ?? '—')) }}</span>
        </div>

        @if($e->justification)
        <div class="bge-justification">{{ $e->justification }}</div>
        @endif

        @if($e->review_notes)
        <div class="bge-review-note">Review note: {{ $e->review_notes }}</div>
        @endif

        @if($e->reviewed_by && $e->reviewed_at)
        <div class="bge-reviewed-by">Reviewed {{ $e->reviewed_at->diffForHumans() }}</div>
        @endif
      </div>

      {{-- Right: actions --}}
      @if(in_array($e->status, ['active', 'under_review']))
      <div class="bge-actions">
        <form method="POST" action="{{ route('admin.break-glass.approve', $e->id) }}">
          @csrf
          <input type="hidden" name="review_notes" value="Reviewed and approved by compliance.">
          <button type="submit" class="btn btn-green btn-sm btn-full">✓ Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.break-glass.dispute', $e->id) }}"
              data-confirm="Mark as disputed? The patient will be notified."
              data-confirm-ok="Dispute">
          @csrf
          <input type="hidden" name="review_notes" value="Under investigation by compliance team.">
          <button type="submit" class="btn btn-danger btn-sm btn-full">⚠ Dispute</button>
        </form>
        <form method="POST" action="{{ route('admin.break-glass.close', $e->id) }}">
          @csrf
          <button type="submit" class="btn btn-secondary btn-sm btn-full">Close</button>
        </form>
      </div>
      @elseif($e->status === 'disputed')
      <div class="bge-actions-col">
        <form method="POST" action="{{ route('admin.break-glass.close', $e->id) }}">
          @csrf
          <button type="submit" class="btn btn-secondary btn-sm btn-full">Close investigation</button>
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
<div class="mt-16">{{ $events->links() }}</div>
@endif
@endsection
