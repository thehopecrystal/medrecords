<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SettingsController extends Controller
{
    private array $keys = [
        'site_name', 'site_tagline', 'site_logo_url', 'footer_about',
        'hero_headline', 'hero_subtext', 'hero_cta_primary', 'hero_cta_secondary',
        'contact_phone_1', 'contact_phone_2', 'contact_email',
        'contact_address', 'contact_city', 'contact_state',
        'social_facebook', 'social_twitter', 'social_instagram',
        'social_linkedin', 'social_youtube',
        'smtp_host', 'smtp_port', 'smtp_encryption',
        'smtp_username', 'smtp_from_email', 'smtp_from_name',
    ];

    public function index()
    {
        SiteSetting::loadAll();
        $settings = [];
        foreach ($this->keys as $k) {
            $settings[$k] = SiteSetting::get($k);
        }
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'       => 'required|string|max:80',
            'contact_email'   => 'nullable|email',
            'smtp_port'       => 'nullable|integer',
            'smtp_encryption' => 'nullable|in:tls,ssl,none',
        ]);

        foreach ($this->keys as $key) {
            SiteSetting::set($key, $request->input($key, ''));
        }

        // Password handled separately — only update if filled; stored encrypted
        if ($request->filled('smtp_password')) {
            SiteSetting::set('smtp_password', Crypt::encryptString($request->smtp_password));
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
}
