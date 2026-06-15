@extends('layouts.app')
@section('title','Notifications')
@section('topbar-title','Notifications')

@section('content')
<div class="page-header-row">
  <div><h1>Notifications</h1><p>All your in-app alerts and updates.</p></div>
</div>

<div class="card">
  @forelse($notifications as $notif)
  @php
    $icons = [
      'consent_expiry'         => '⏰',
      'appointment_request'    => '📅',
      'appointment_confirmed'  => '✅',
      'refill_request'         => '💊',
      'refill_approved'        => '✅',
      'refill_denied'          => '❌',
      'break_glass'            => '⚡',
    ];
    $icon = $icons[$notif->type] ?? '🔔';
  @endphp
  <div class="notif-row {{ $notif->isUnread() ? 'notif-row--unread' : '' }}">
    <div class="notif-icon">{{ $icon }}</div>
    <div class="notif-content">
      <div class="notif-title {{ $notif->isUnread() ? 'unread' : '' }}">{{ $notif->title }}</div>
      <div class="notif-text">{{ $notif->body }}</div>
      <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
    </div>
    <div class="notif-meta">
      @if($notif->isUnread())<span class="pill-xs navy">New</span>@endif
      <form method="POST" action="{{ route('patient.notifications.destroy', $notif->id) }}">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-ghost btn-sm" title="Delete">✕</button>
      </form>
    </div>
  </div>
  @empty
  <div class="empty-state">
    <div class="empty-icon">🔔</div>
    <h3>All caught up</h3>
    <p>No notifications yet. Important alerts about your health records will appear here.</p>
  </div>
  @endforelse
  @if($notifications->hasPages())
  <div class="pager-row">{{ $notifications->links() }}</div>
  @endif
</div>
@endsection
