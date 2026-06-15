<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('code') — MedRecords</title>
<style nonce="{{ $cspNonce ?? '' }}">
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;background:#F0F4F8;color:#0F172A;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px}
.card{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(15,23,42,.1);padding:48px 40px;max-width:480px;width:100%;text-align:center}
.icon{font-size:52px;margin-bottom:20px}
.code{font-size:72px;font-weight:900;color:#1E3A5F;line-height:1;margin-bottom:8px}
.title{font-size:20px;font-weight:700;color:#1E3A5F;margin-bottom:10px}
.desc{font-size:14px;color:#475569;line-height:1.6;margin-bottom:28px}
.btn{display:inline-flex;align-items:center;gap:6px;padding:10px 22px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;background:#1E3A5F;color:#fff;transition:background .15s}
.btn:hover{background:#163050}
.btn-ghost{background:#F1F5F9;color:#475569;margin-left:8px}
.btn-ghost:hover{background:#E2E8F0}
.divider{width:48px;height:3px;background:linear-gradient(90deg,#1E3A5F,#2C5F2D);border-radius:2px;margin:0 auto 28px}
</style>
</head>
<body>
<div class="card">
  <div class="icon">@yield('icon', '⚠️')</div>
  <div class="code">@yield('code')</div>
  <div class="divider"></div>
  <div class="title">@yield('title')</div>
  <div class="desc">@yield('message')</div>
  <div>
    <button type="button" class="btn btn-ghost" id="errGoBack">← Go back</button>
    <a href="/" class="btn">🏠 Home</a>
  </div>
</div>
<script>document.getElementById('errGoBack').addEventListener('click', function () { history.back(); });</script>
</body>
</html>
