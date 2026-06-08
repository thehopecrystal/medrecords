@extends('layouts.auth')
@section('title', 'Sign In')
@section('content')

<h2>Welcome back</h2>
<p class="subtitle">Sign in with your email address or phone number</p>

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
           placeholder="adaeze@gmail.com or 08045678901"
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
      <button type="button" class="input-action" onclick="togglePassword()" title="Show/hide password">
        <span id="eyeIcon">👁</span>
      </button>
    </div>
  </div>
  <div class="form-field" style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
    <input type="checkbox" name="remember" id="rememberMe"
           style="width:auto;accent-color:var(--navy);cursor:pointer;">
    <label for="rememberMe" style="font-size:13px;color:var(--grey-500);text-transform:none;letter-spacing:0;font-weight:400;cursor:pointer;margin-bottom:0;">Keep me signed in</label>
  </div>
  <button type="submit" class="btn-submit" id="submitBtn">Sign in</button>
</form>

<div class="divider">quick demo access</div>

<div class="role-links">
  <span style="font-size:11px;color:var(--grey-500);text-align:center;grid-column:1/-1;">
    All demo passwords are <strong>password</strong>
  </span>
  <button type="button" class="role-link" onclick="fillDemo('adaeze@gmail.com')">🧑‍🤝‍🧑 Patient</button>
  <button type="button" class="role-link" onclick="fillDemo('tunde@reddington.ng')">🩺 Doctor</button>
  <button type="button" class="role-link" onclick="fillDemo('okafor@healthplus.ng')">💊 Pharmacist</button>
  <button type="button" class="role-link" onclick="fillDemo('admin@medrecords.ng')">🛡 Admin</button>
</div>

<div class="form-footer">
  No account? <a href="{{ route('register') }}">Register as patient</a>
</div>

<style>
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
.role-link {
  text-align: center;
  padding: 9px 8px;
  border-radius: 8px;
  border: 1px solid var(--grey-200);
  font-size: 12px;
  font-weight: 500;
  color: var(--grey-700, #374151);
  background: #fff;
  cursor: pointer;
  transition: background 0.15s, border-color 0.15s;
  font-family: inherit;
}
.role-link:hover {
  background: var(--grey-50);
  border-color: var(--navy);
  color: var(--navy);
}
.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>

<script>
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

function fillDemo(email) {
  document.getElementById('loginInput').value = email;
  document.getElementById('passwordInput').value = 'password';
  document.getElementById('loginInput').focus();
}

document.getElementById('loginForm').addEventListener('submit', function () {
  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.textContent = 'Signing in…';
});
</script>

@endsection
