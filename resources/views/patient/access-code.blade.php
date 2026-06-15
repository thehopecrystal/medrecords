@extends('layouts.app')
@section('title', 'Share at Reception')
@section('topbar-title', 'Share Record')

@section('content')
<div class="page-header text-center">
  <h1>Share at Reception</h1>
  <p>Show this code to your provider for in-person access</p>
</div>

@if(isset($accessCode) && $accessCode->isValid())
  <div class="access-code-box" id="code-display" data-expiry="{{ $accessCode->expires_at->toISOString() }}">
    <div class="code-label">One-time access code</div>
    <div class="code">{{ substr($accessCode->decrypted_code, 0, 3) }}&nbsp;{{ substr($accessCode->decrypted_code, 3) }}</div>
    <div class="timer" id="timer">Valid for 10:00</div>
  </div>

  <p class="access-hint">
    Show this to your provider or read it out loud. Works without internet.
  </p>

  <div class="access-actions">
    <form method="POST" action="{{ route('patient.access-code.sms', $accessCode->id) }}">
      @csrf
      <button type="submit" class="btn btn-secondary btn-full">
        📲 Send code to my phone (SMS){{ $accessCode->sms_sent ? ' ✓ Sent' : '' }}
      </button>
    </form>
    <form method="POST" action="{{ route('patient.access-code.generate') }}">
      @csrf
      <button type="submit" class="btn btn-secondary btn-full">Generate new code</button>
    </form>
  </div>
@else
  <div class="access-generate">
    <form method="POST" action="{{ route('patient.access-code.generate') }}">
      @csrf
      <button type="submit" class="btn btn-primary" style="font-size:15px;padding:12px 28px;">Generate access code</button>
    </form>
    <p>Code expires after 10 minutes. Works offline.</p>
    <div class="access-tip-box">
      <strong class="text-navy-link">Tip:</strong> Show the code on screen or read it to your provider.
      You can also give them your MedID: <strong>{{ auth()->user()->patient->med_id }}</strong>
    </div>
  </div>
@endif
@endsection

@push('scripts')
@vite('resources/js/pages/access-code.js')
@endpush
