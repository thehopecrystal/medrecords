@extends('layouts.auth')
@section('title', 'Verify Your Email')

@section('content')
<div style="max-width:420px;margin:0 auto;">
  <div style="text-align:center;margin-bottom:32px;">
    <div style="width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#1E3A5F,#2C5F2D);display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 16px;">✉️</div>
    <h1 style="font-size:24px;font-weight:800;color:#0F172A;margin-bottom:8px;">Verify your email</h1>
    <p style="color:#64748B;font-size:14px;line-height:1.6;">
      We sent a verification link to <strong style="color:#0F172A;">{{ auth()->user()->email }}</strong>.
      Click the link in that email to activate your account.
    </p>
  </div>

  @if(session('success'))
    <div style="background:#EAFAF1;border-left:3px solid #2C5F2D;padding:12px 16px;border-radius:8px;color:#1A3A1F;font-size:13px;margin-bottom:20px;">
      {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div style="background:#FBEAEC;border-left:3px solid #B23A48;padding:12px 16px;border-radius:8px;color:#7A1F2C;font-size:13px;margin-bottom:20px;">
      {{ $errors->first() }}
    </div>
  @endif

  <div style="background:#F8FAFC;border:1.5px solid #E2E8F0;border-radius:12px;padding:20px;margin-bottom:24px;">
    <p style="font-size:13px;color:#475569;margin:0 0 12px;">Didn't receive the email? Check your spam folder, or click below to resend.</p>
    <form method="POST" action="{{ route('verification.send') }}">
      @csrf
      <button type="submit" style="width:100%;padding:11px;background:#1E3A5F;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
        Resend verification email
      </button>
    </form>
  </div>

  <div style="text-align:center;">
    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
      @csrf
      <button type="submit" style="background:none;border:none;font-size:13px;color:#64748B;cursor:pointer;text-decoration:underline;">
        Sign out and use a different account
      </button>
    </form>
  </div>
</div>
@endsection
