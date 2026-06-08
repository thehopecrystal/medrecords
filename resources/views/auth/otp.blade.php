@extends('layouts.auth')
@section('title', 'Two-Factor Verification')

@section('content')
<div style="max-width:380px;margin:0 auto;">
  <div style="text-align:center;margin-bottom:32px;">
    <div style="width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#1E3A5F,#2C5F2D);display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 16px;">🔐</div>
    <h1 style="font-size:24px;font-weight:800;color:#0F172A;margin-bottom:8px;">Check your phone</h1>
    <p style="color:#64748B;font-size:14px;line-height:1.6;">We sent a 6-digit code to your registered phone number. Enter it below to continue.</p>
  </div>

  @if($errors->any())
    <div style="background:#FBEAEC;border-left:3px solid #B23A48;padding:12px 16px;border-radius:8px;color:#7A1F2C;font-size:13px;margin-bottom:20px;">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('auth.otp') }}">
    @csrf
    <div style="margin-bottom:20px;">
      <label style="display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#475569;margin-bottom:6px;">Verification Code</label>
      <input type="text" name="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
        autofocus autocomplete="one-time-code"
        style="width:100%;padding:14px;border:1.5px solid #E2E8F0;border-radius:10px;font-size:28px;font-weight:800;letter-spacing:0.25em;text-align:center;background:#FAFBFC;color:#0F172A;"
        placeholder="000000" value="{{ old('otp') }}">
      <div style="font-size:12px;color:#94A3B8;margin-top:6px;text-align:center;">Code is valid for 10 minutes</div>
    </div>

    <button type="submit" style="width:100%;padding:13px;background:#1E3A5F;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;">
      Verify &amp; Sign In
    </button>
  </form>

  <div style="margin-top:24px;text-align:center;">
    <a href="{{ route('login') }}" style="font-size:13px;color:#64748B;">← Use a different account</a>
  </div>
</div>
@endsection
