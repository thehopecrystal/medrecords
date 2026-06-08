@extends('layouts.app')
@section('title', 'Share at Reception')
@section('topbar-title', 'Share Record')


@section('content')
<div class="page-header" style="text-align:center;">
  <h1>Share at Reception</h1>
  <p>Show this code to your provider for in-person access</p>
</div>

@if(isset($accessCode) && $accessCode->isValid())
  <div class="access-code-box" id="code-display">
    <div class="code-label">One-time access code</div>
    <div class="code">{{ substr($accessCode->code, 0, 3) }}&nbsp;{{ substr($accessCode->code, 3) }}</div>
    <div class="timer" id="timer">Valid for 10:00</div>
  </div>

  <div style="text-align:center;margin-top:16px;color:var(--grey-500);font-size:13px;max-width:360px;margin-left:auto;margin-right:auto;">
    Show this to your provider or read it out loud. Works without internet.
  </div>

  {{-- SMS & actions --}}
  <div style="max-width:360px;margin:18px auto 0;display:flex;gap:10px;flex-direction:column;">
    <form method="POST" action="{{ route('patient.access-code.sms', $accessCode->id) }}">
      @csrf
      <button type="submit" class="btn btn-secondary" style="width:100%;">
        📲 Send code to my phone (SMS){{ $accessCode->sms_sent ? ' ✓ Sent' : '' }}
      </button>
    </form>
    <form method="POST" action="{{ route('patient.access-code.generate') }}">
      @csrf
      <button type="submit" class="btn btn-secondary" style="width:100%;">Generate new code</button>
    </form>
  </div>

  <script>
    (function() {
      var expiry = new Date('{{ $accessCode->expires_at->toISOString() }}');
      function tick() {
        var diff = Math.max(0, Math.floor((expiry - Date.now()) / 1000));
        var m = Math.floor(diff / 60), s = diff % 60;
        document.getElementById('timer').textContent = 'Valid for ' + m + ':' + String(s).padStart(2,'0');
        if (diff > 0) setTimeout(tick, 1000);
        else document.getElementById('code-display').style.opacity = '0.4';
      }
      tick();
    })();
  </script>
@else
  <div style="text-align:center;padding:32px 0;">
    <form method="POST" action="{{ route('patient.access-code.generate') }}">
      @csrf
      <button type="submit" class="btn btn-primary" style="font-size:15px;padding:12px 28px;">Generate access code</button>
    </form>
    <p style="font-size:12px;color:var(--grey-500);margin-top:12px;">Code expires after 10 minutes. Works offline.</p>
    <div style="max-width:320px;margin:16px auto 0;background:var(--navy-tint);border-radius:10px;padding:12px 14px;font-size:12px;color:var(--grey-700);text-align:left;">
      <strong style="color:var(--navy);">Tip:</strong> Show the code on screen or read it to your provider. You can also give them your MedID: <strong>{{ auth()->user()->patient->med_id }}</strong>
    </div>
  </div>
@endif
@endsection
