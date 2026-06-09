<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
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
        // Payments
        'payment_gateway', 'paystack_public_key', 'flutterwave_public_key',
    ];

    /** Keys stored encrypted — handled separately from $keys */
    private array $encryptedKeys = [
        'smtp_password',
        'twilio_auth_token',
        'paystack_secret_key',
        'flutterwave_secret_key',
    ];

    public function index()
    {
        SiteSetting::loadAll();
        $settings = [];
        foreach (array_merge($this->keys, $this->encryptedKeys) as $k) {
            $settings[$k] = SiteSetting::get($k);
        }
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'        => 'required|string|max:80',
            'contact_email'    => 'nullable|email',
            'smtp_port'        => 'nullable|integer',
            'smtp_encryption'  => 'nullable|in:tls,ssl,none',
            'sms_enabled'      => 'nullable|in:0,1',
            'payment_gateway'  => 'nullable|in:none,paystack,flutterwave',
            'site_logo_file'   => 'nullable|file|mimes:jpeg,jpg,png,gif,svg,webp|max:2048',
            'site_favicon_file'=> 'nullable|file|mimes:ico,png,svg,jpeg,jpg,gif,webp|max:512',
        ]);

        // Logo file upload — takes priority over URL field
        if ($request->hasFile('site_logo_file')) {
            $old = SiteSetting::get('site_logo_path', '');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('site_logo_file')->store('settings', 'public');
            SiteSetting::set('site_logo_path', $path);
            SiteSetting::set('site_logo_url', asset('storage/' . $path));
            // Remove from $keys so the loop below doesn't overwrite with the URL field value
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

        // Plain keys
        foreach ($keys as $key) {
            // Checkbox: if not present in request treat as '0'
            if ($key === 'sms_enabled') {
                SiteSetting::set($key, $request->has('sms_enabled') ? '1' : '0');
            } else {
                SiteSetting::set($key, $request->input($key, ''));
            }
        }

        // Encrypted keys — only update if field was filled
        foreach ($this->encryptedKeys as $key) {
            if ($request->filled($key)) {
                SiteSetting::set($key, Crypt::encryptString($request->input($key)));
            }
        }

        return back()->with('success', 'Settings saved successfully.');
    }

    public function testSmtp(Request $request)
    {
        $request->validate(['test_email' => 'required|email|max:120']);
        $to = $request->test_email;

        try {
            \Illuminate\Support\Facades\Mail::raw(
                'This is a test email from MedRecords SMTP configuration.',
                fn ($msg) => $msg->to($to)->subject('MedRecords SMTP Test')
            );
            return back()->with('success', "Test email sent to {$to}.");
        } catch (\Throwable $e) {
            return back()->with('error', 'SMTP test failed: ' . $e->getMessage());
        }
    }

    public function testSms(Request $request)
    {
        $request->validate(['test_phone' => 'required|string|max:20']);

        $sms = new \App\Services\SmsService();
        $ok  = $sms->send($request->test_phone, 'MedRecords SMS test — your configuration is working correctly.');

        return $ok
            ? back()->with('success', "Test SMS sent to {$request->test_phone}.")
            : back()->with('error', 'SMS test failed. Check your Twilio credentials and phone number format.');
    }
}
