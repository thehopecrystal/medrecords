<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /** Plain-text setting keys stored as-is */
    private array $keys = [
        'site_name', 'site_tagline', 'site_logo_url', 'site_favicon_url', 'footer_about',
        'hero_headline', 'hero_subtext', 'hero_cta_primary', 'hero_cta_secondary',
        'contact_phone_1', 'contact_phone_2', 'contact_email',
        'contact_address', 'contact_city', 'contact_state',
        'social_facebook', 'social_twitter', 'social_instagram',
        'social_linkedin', 'social_youtube',
        'smtp_host', 'smtp_port', 'smtp_encryption',
        'smtp_username', 'smtp_from_email', 'smtp_from_name',
        // SMS
        'sms_enabled', 'twilio_account_sid', 'twilio_from_number',
        // OTP / 2FA channels
        'otp_sms_enabled', 'otp_email_enabled',
        // Payments
        'payment_gateway', 'paystack_public_key', 'flutterwave_public_key',
    ];

    /** Keys stored encrypted - handled separately from $keys */
    private array $encryptedKeys = [
        'smtp_password',
        'twilio_auth_token',
        'paystack_secret_key',
        'flutterwave_secret_key',
    ];

    public function index(): JsonResponse
    {
        SiteSetting::loadAll();
        $settings = [];
        foreach ($this->keys as $k) {
            $settings[$k] = SiteSetting::get($k);
        }
        // Never send decrypted secrets to the client - show a placeholder if a value is stored.
        foreach ($this->encryptedKeys as $k) {
            $settings[$k] = SiteSetting::get($k) ? '********' : '';
        }
        return response()->json(['settings' => $settings]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'site_name'         => 'sometimes|required|string|max:80',
            'contact_email'     => 'sometimes|nullable|email|max:200',
            'smtp_port'         => 'sometimes|nullable|integer|min:1|max:65535',
            'smtp_encryption'   => 'sometimes|nullable|in:tls,ssl,none',
            'sms_enabled'       => 'sometimes|nullable|in:0,1',
            'otp_sms_enabled'   => 'sometimes|nullable|in:0,1',
            'otp_email_enabled' => 'sometimes|nullable|in:0,1',
            'payment_gateway'   => 'sometimes|nullable|in:none,paystack,flutterwave',
            'site_logo_file'    => 'nullable|file|mimes:jpeg,jpg,png,gif,svg,webp|max:2048',
            'site_favicon_file' => 'nullable|file|mimes:ico,png,svg,jpeg,jpg,gif,webp|max:512',
            // Social URLs must use http(s) - blocks javascript: and data: URI injection.
            'social_facebook'   => 'sometimes|nullable|string|max:500|regex:/^https?:\\/\\//i',
            'social_twitter'    => 'sometimes|nullable|string|max:500|regex:/^https?:\\/\\//i',
            'social_instagram'  => 'sometimes|nullable|string|max:500|regex:/^https?:\\/\\//i',
            'social_linkedin'   => 'sometimes|nullable|string|max:500|regex:/^https?:\\/\\//i',
            'social_youtube'    => 'sometimes|nullable|string|max:500|regex:/^https?:\\/\\//i',
            'site_logo_url'     => 'sometimes|nullable|string|max:500|regex:/^https?:\\/\\//i',
            'site_favicon_url'  => 'sometimes|nullable|string|max:500|regex:/^https?:\\/\\//i',
        ]);

        // Logo file upload - takes priority over URL field
        if ($request->hasFile('site_logo_file')) {
            $old = SiteSetting::get('site_logo_path', '');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('site_logo_file')->store('settings', 'public');
            SiteSetting::set('site_logo_path', $path);
            SiteSetting::set('site_logo_url', asset('storage/' . $path));
            $keys = array_diff($this->keys, ['site_logo_url']);
        } else {
            $keys = $this->keys;
        }

        // Favicon file upload
        if ($request->hasFile('site_favicon_file')) {
            $old = SiteSetting::get('site_favicon_path', '');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('site_favicon_file')->store('settings', 'public');
            SiteSetting::set('site_favicon_path', $path);
            SiteSetting::set('site_favicon_url', asset('storage/' . $path));
            $keys = array_diff($keys, ['site_favicon_url']);
        }

        // Plain keys - only update keys present in this request (supports per-section forms)
        $checkboxKeys = ['sms_enabled', 'otp_sms_enabled', 'otp_email_enabled'];
        foreach ($keys as $key) {
            if (!$request->exists($key)) continue;
            if (in_array($key, $checkboxKeys)) {
                SiteSetting::set($key, $request->input($key) === '1' ? '1' : '0');
            } else {
                SiteSetting::set($key, $request->input($key, ''));
            }
        }

        // Encrypted keys - only update if field was filled
        foreach ($this->encryptedKeys as $key) {
            if ($request->filled($key)) {
                SiteSetting::set($key, Crypt::encryptString($request->input($key)));
            }
        }

        return response()->json(['message' => 'Settings saved.']);
    }

    public function testSmtp(Request $request): JsonResponse
    {
        $request->validate(['test_email' => 'required|email|max:120']);
        $to = $request->test_email;

        try {
            \Illuminate\Support\Facades\Mail::raw(
                'This is a test email from MedRecords SMTP configuration.',
                fn ($msg) => $msg->to($to)->subject('MedRecords SMTP Test')
            );
            return response()->json(['message' => "Test email sent to {$to}."]);
        } catch (\Throwable $e) {
            // Log the real error privately - never expose SMTP credentials or server details.
            \Illuminate\Support\Facades\Log::error('SMTP test failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'SMTP test failed. Check your SMTP settings and try again.'], 503);
        }
    }

    public function testSms(Request $request): JsonResponse
    {
        $request->validate(['test_phone' => 'required|string|regex:/^\+[1-9]\d{7,14}$/|max:20']);

        $sms = new \App\Services\SmsService();
        $ok  = $sms->send($request->test_phone, 'MedRecords SMS test - your configuration is working correctly.');

        return $ok
            ? response()->json(['message' => "Test SMS sent to {$request->test_phone}."])
            : response()->json(['message' => 'SMS test failed. Check your Twilio credentials and phone number format.'], 503);
    }
}
