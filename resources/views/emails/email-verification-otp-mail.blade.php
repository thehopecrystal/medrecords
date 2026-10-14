{{-- Standalone OTP email template — must not depend on the session or auth(). --}}
<!DOCTYPE html>
<html>
<body style="margin:0;padding:24px;background:#F8FAFC;font-family:Arial,Helvetica,sans-serif;">
  <div style="max-width:480px;margin:0 auto;background:#ffffff;border:1px solid #E2E8F0;border-radius:12px;padding:32px;">
    <h1 style="font-size:20px;font-weight:800;color:#0F172A;margin:0 0 8px;">Verify your email</h1>
    <p style="color:#475569;font-size:14px;line-height:1.6;margin:0 0 24px;">
      Hi {{ $name !== '' ? $name : 'there' }}, use the 6-digit code below to verify your MedRecords account.
    </p>
    <div style="font-size:32px;font-weight:800;letter-spacing:0.3em;text-align:center;color:#1E3A5F;background:#F1F5F9;border-radius:10px;padding:16px 0;margin-bottom:24px;">{{ $code }}</div>
    <p style="color:#94A3B8;font-size:12px;margin:0;">
      This code expires in 10 minutes. If you didn't request it, you can safely ignore this email.
    </p>
  </div>
</body>
</html>
