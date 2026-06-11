@extends('layouts.app')
@section('title', 'Account Pending Verification')
@section('topbar-title', 'Account Pending Verification')

@section('content')
<div style="max-width:540px;margin:60px auto;text-align:center;">
  <div style="width:72px;height:72px;border-radius:20px;background:linear-gradient(135deg,#1E3A5F,#2C5F2D);display:flex;align-items:center;justify-content:center;font-size:34px;margin:0 auto 24px;">🔍</div>
  <h1 style="font-size:26px;font-weight:800;color:var(--navy);margin-bottom:12px;">Credential verification pending</h1>
  <p style="color:var(--grey-500);font-size:15px;line-height:1.7;margin-bottom:32px;">
    Your MDCN medical licence is currently being reviewed by the facility administrator.
    You will receive an email notification once verification is complete and you can access patient records.
  </p>

  <div style="background:#F8FAFC;border:1.5px solid #E2E8F0;border-radius:14px;padding:24px 28px;text-align:left;margin-bottom:32px;">
    <p style="font-size:13px;font-weight:700;color:var(--grey-600);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:14px;">What happens next?</p>
    <ul style="margin:0;padding-left:20px;color:var(--grey-600);font-size:14px;line-height:2;">
      <li>Your MDCN Folio number is checked against the MDCN register</li>
      <li>Your facility administrator reviews and approves your account</li>
      <li>You receive an email when your account is approved</li>
    </ul>
  </div>

  <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
    <a href="{{ route('provider.dashboard') }}"
       style="padding:11px 24px;background:var(--navy);color:#fff;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;">
      Back to dashboard
    </a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"
              style="padding:11px 24px;background:#F1F5F9;color:var(--grey-700);border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
        Sign out
      </button>
    </form>
  </div>
</div>
@endsection
