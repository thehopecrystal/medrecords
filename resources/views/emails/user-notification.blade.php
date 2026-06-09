<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $notifTitle }}</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f0f4f8;padding:48px 20px;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.08);">

          {{-- Header --}}
          <tr>
            <td style="background:#1e3a5f;padding:28px 36px 24px;">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td>
                    <span style="color:#fff;font-size:22px;font-weight:700;letter-spacing:-0.3px;">MedRecords</span>
                    <br>
                    <span style="color:rgba(255,255,255,0.65);font-size:13px;">Your health record, always with you</span>
                  </td>
                  <td align="right">
                    <span style="background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.85);font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;letter-spacing:0.5px;text-transform:uppercase;">Notification</span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Body --}}
          <tr>
            <td style="padding:36px 36px 28px;">
              <h1 style="margin:0 0 14px;color:#1a2637;font-size:21px;font-weight:700;line-height:1.3;">
                {{ $notifTitle }}
              </h1>
              <div style="color:#4a5568;font-size:15px;line-height:1.7;margin-bottom:28px;">
                {!! nl2br(e($notifBody)) !!}
              </div>
              <table cellpadding="0" cellspacing="0">
                <tr>
                  <td style="border-radius:8px;background:#1e3a5f;">
                    <a href="{{ url('/login') }}"
                       style="display:inline-block;padding:13px 28px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;letter-spacing:0.2px;">
                      View in MedRecords &rarr;
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Divider --}}
          <tr>
            <td style="padding:0 36px;">
              <div style="border-top:1px solid #e8edf2;"></div>
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="padding:22px 36px 28px;">
              <p style="margin:0;color:#9aa5b4;font-size:12px;line-height:1.6;">
                You are receiving this because you have an active MedRecords account.
                If you believe you received this in error, please ignore this email or contact
                <a href="mailto:support@medrecords.ng" style="color:#1e3a5f;text-decoration:none;">support@medrecords.ng</a>.
              </p>
              <p style="margin:10px 0 0;color:#c4cdd6;font-size:11px;">
                &copy; {{ date('Y') }} MedRecords &mdash; Nigeria
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
