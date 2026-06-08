@extends('layouts.app')
@section('title','Notifications')
@section('topbar-title','Notifications')

@section('content')
<div class="page-header-row">
  <div><h1>Notifications</h1><p>All your in-app alerts and updates.</p></div>
</div>

<div class="card">
  <div class="card-body" style="padding:0;">
    @forelse($notifications as $notif)
    @php
      $icons = [
        'consent_expiry'    => '⏰',
        'appointment_request' => '📅',
        'appointment_confirmed'=> '✅',
        'refill_request'    => '💊',
        'refill_approved'   => '✅',
        'refill_denied'     => '❌',
        'break_glass'       => '⚡',
      ];
      $icon = $icons[$notif->type] ?? '🔔';
    @endphp
    <div style="display:flex;align-items:flex-start;gap:14px;padding:16px 22px;border-bottom:1px solid var(--border);{{ $notif->isUnread() ? 'background:#F0F7FF;' : '' }}">
      <div style="width:38px;height:38px;border-radius:50%;background:var(--navy-lt);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">{{ $icon }}</div>
      <div style="flex:1;min-width:0;">
        <div style="font-size:14px;font-weight:{{ $notif->isUnread() ? '700' : '600' }};color:var(--text);">{{ $notif->title }}</div>
        <div style="font-size:13px;color:var(--text-2);margin-top:3px;line-height:1.5;">{{ $notif->body }}</div>
        <div style="font-size:11px;color:var(--text-3);margin-top:6px;">{{ $notif->created_at->diffForHumans() }}</div>
      </div>
      <div style="display:flex;gap:6px;flex-shrink:0;align-items:center;">
        @if($notif->isUnread())<span class="pill navy" style="font-size:10px;">New</span>@endif
        <form method="POST" action="{{ route('patient.notifications.destroy', $notif->id) }}">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-ghost btn-sm" title="Delete">✕</button>
        </form>
      </div>
    </div>
    @empty
    <div class="empty-state" style="padding:56px 24px;">
      <div class="empty-icon">🔔</div>
      <h3>All caught up</h3>
      <p>No notifications yet. Important alerts about your health records will appear here.</p>
    </div>
    @endforelse
  </div>
  @if($notifications->hasPages())
  <div style="padding:14px 22px;border-top:1px solid var(--border);">{{ $notifications->links() }}</div>
  @endif
</div>
@endsection
