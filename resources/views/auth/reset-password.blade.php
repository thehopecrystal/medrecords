@extends('layouts.auth')
@section('title', 'Set New Password')
@section('content')

<h2>Set new password</h2>
<p class="subtitle">Choose a strong password for your account.</p>

@if($errors->any())
  <div class="alert-error">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('password.update') }}">
  @csrf
  <input type="hidden" name="token" value="{{ $token }}">
  <input type="hidden" name="email" value="{{ $email }}">

  <div class="form-field">
    <label>Email address</label>
    <input type="email" value="{{ $email }}" disabled style="background:var(--grey-100);color:var(--grey-500);">
  </div>
  <div class="form-field">
    <label>New password</label>
    <input type="password" name="password"
           placeholder="Min. 8 characters"
           autocomplete="new-password"
           required>
    @error('password')<div class="form-error" style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
  </div>
  <div class="form-field">
    <label>Confirm new password</label>
    <input type="password" name="password_confirmation"
           placeholder="Repeat password"
           autocomplete="new-password"
           required>
  </div>
  <button type="submit" class="btn-submit">Reset password</button>
</form>

<div class="form-footer">
  <a href="{{ route('login') }}">← Back to sign in</a>
</div>

<style nonce="{{ $cspNonce }}">
.alert-error {
  background: #FBEAEC;
  color: #7B1D28;
  border-left: 3px solid var(--red);
  border-radius: 8px;
  padding: 10px 12px;
  font-size: 13px;
  margin-bottom: 16px;
}
</style>

@endsection
