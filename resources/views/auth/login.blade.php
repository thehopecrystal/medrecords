@extends('layouts.auth')
@section('title', 'Sign In')
@section('content')

<h2>Welcome back</h2>
<p class="subtitle">Sign in with your email address, phone number, or username</p>

@if(session('success'))
  <div class="alert-success">{{ session('success') }}</div>
@endif

@if($errors->has('login'))
  <div class="alert-error">{{ $errors->first('login') }}</div>
@endif

<form method="POST" action="{{ route('login') }}" id="loginForm">
  @csrf
  <div class="form-field">
    <label>Email or phone</label>
    <input type="text" name="login" id="loginInput"
           value="{{ old('login') }}"
           placeholder="Email, phone number, or username"
           autocomplete="username"
           required autofocus>
  </div>
  <div class="form-field">
    <label>Password</label>
    <div class="input-with-action">
      <input type="password" name="password" id="passwordInput"
             placeholder="••••••••"
             autocomplete="current-password"
             required>
      <button type="button" class="input-action" id="pwToggleBtn" title="Show/hide password">
        <span id="eyeIcon">👁</span>
      </button>
    </div>
  </div>
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
    <div style="display:flex;align-items:center;gap:8px;">
      <input type="checkbox" name="remember" id="rememberMe"
             style="width:auto;accent-color:var(--navy);cursor:pointer;">
      <label for="rememberMe" style="font-size:13px;color:var(--grey-500);text-transform:none;letter-spacing:0;font-weight:400;cursor:pointer;margin-bottom:0;">Keep me signed in</label>
    </div>
    <a href="{{ route('password.request') }}" style="font-size:13px;color:var(--navy);font-weight:500;">Forgot password?</a>
  </div>
  <button type="submit" class="btn-submit" id="submitBtn">Sign in</button>
</form>

<div class="form-footer">
  No account? <a href="{{ route('register') }}">Create an account</a>
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
.input-with-action {
  position: relative;
  display: flex;
  align-items: center;
}
.input-with-action input {
  padding-right: 40px;
}
.input-action {
  position: absolute;
  right: 10px;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 15px;
  line-height: 1;
  color: var(--grey-500);
  padding: 2px;
}
.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>

<script nonce="{{ $cspNonce }}">
function togglePassword() {
  const input = document.getElementById('passwordInput');
  const icon  = document.getElementById('eyeIcon');
  if (input.type === 'password') {
    input.type = 'text';
    icon.textContent = '🙈';
  } else {
    input.type = 'password';
    icon.textContent = '👁';
  }
}

document.getElementById('pwToggleBtn').addEventListener('click', togglePassword);

document.getElementById('loginForm').addEventListener('submit', function () {
  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.textContent = 'Signing in…';
});
</script>

@endsection
