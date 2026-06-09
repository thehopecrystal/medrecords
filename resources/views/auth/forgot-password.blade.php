@extends('layouts.auth')
@section('title', 'Forgot Password')
@section('content')

<h2>Reset your password</h2>
<p class="subtitle">Enter your email and we'll send you a reset link.</p>

@if(session('success'))
  <div class="alert-success">{{ session('success') }}</div>
@endif

@if($errors->has('email'))
  <div class="alert-error">{{ $errors->first('email') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
  @csrf
  <div class="form-field">
    <label>Email address</label>
    <input type="email" name="email" value="{{ old('email') }}"
           placeholder="you@example.com"
           autocomplete="email"
           required autofocus>
  </div>
  <button type="submit" class="btn-submit">Send reset link</button>
</form>

<div class="form-footer">
  <a href="{{ route('login') }}">← Back to sign in</a>
</div>

<style nonce="{{ $cspNonce }}">
.alert-success {
  background: #EDF7ED;
  color: #1B5E20;
  border-left: 3px solid var(--green);
  border-radius: 8px;
  padding: 10px 12px;
  font-size: 13px;
  margin-bottom: 16px;
}
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
