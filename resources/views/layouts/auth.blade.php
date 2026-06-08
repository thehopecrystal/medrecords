<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Sign In') — MedRecords</title>
<style>
:root {
  --navy: #1E3A5F;
  --green: #2C5F2D;
  --grey-50: #F9FAFB;
  --grey-100: #F3F4F6;
  --grey-200: #E5E7EB;
  --grey-500: #6B7280;
  --grey-900: #1A1F26;
  --red: #B23A48;
  --red-tint: #FBEAEC;
  --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 12px rgba(15,23,42,0.05);
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
  background: var(--grey-50);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
}
.auth-card {
  background: #fff;
  border-radius: 16px;
  padding: 40px 36px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 10px 40px rgba(15,23,42,0.1);
}
.auth-logo { text-align: center; margin-bottom: 28px; }
.auth-logo .logo-text { font-size: 26px; font-weight: 800; color: var(--navy); letter-spacing: -0.03em; }
.auth-logo .tagline { font-size: 12px; color: var(--grey-500); margin-top: 4px; }
h2 { font-size: 20px; font-weight: 700; color: var(--grey-900); margin-bottom: 6px; }
.subtitle { font-size: 13px; color: var(--grey-500); margin-bottom: 24px; }
.form-field { margin-bottom: 16px; }
.form-field label { display: block; font-size: 12px; font-weight: 600; color: var(--grey-500); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 5px; }
.form-field input, .form-field select {
  width: 100%;
  background: var(--grey-50);
  border: 1px solid var(--grey-200);
  border-radius: 8px;
  padding: 10px 12px;
  font-size: 14px;
  color: var(--grey-900);
  transition: border-color 0.15s;
}
.form-field input:focus, .form-field select:focus { outline: none; border-color: var(--navy); }
.btn-submit {
  width: 100%;
  background: var(--navy);
  color: #fff;
  border: none;
  border-radius: 10px;
  padding: 12px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  margin-top: 8px;
  transition: opacity 0.15s;
}
.btn-submit:hover { opacity: 0.88; }
.form-footer { text-align: center; margin-top: 18px; font-size: 13px; color: var(--grey-500); }
.form-footer a { color: var(--navy); text-decoration: none; font-weight: 600; }
.alert-error { background: var(--red-tint); color: #7A1F2C; border-left: 3px solid var(--red); border-radius: 8px; padding: 10px 12px; font-size: 13px; margin-bottom: 16px; }
.divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; font-size: 12px; color: var(--grey-500); }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--grey-200); }
.role-links { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 6px; }
.role-link { text-align: center; padding: 8px; border-radius: 8px; border: 1px solid var(--grey-200); font-size: 12px; font-weight: 500; color: var(--grey-700); text-decoration: none; transition: background 0.15s; }
.role-link:hover { background: var(--grey-50); color: var(--navy); }
</style>
</head>
<body>
<div class="auth-card">
  <div class="auth-logo">
    <div class="logo-text">MedRecords</div>
    <div class="tagline">Your health record, always with you</div>
  </div>
  @yield('content')
</div>
</body>
</html>
