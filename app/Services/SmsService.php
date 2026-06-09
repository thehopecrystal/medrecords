<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(string $phone, string $message): bool
    {
        if (SiteSetting::get('sms_enabled', '1') !== '1') {
            Log::info("SMS suppressed (disabled in settings). To: {$phone}");
            return true;
        }

        $sid  = SiteSetting::get('twilio_account_sid');
        $from = SiteSetting::get('twilio_from_number');
        $encryptedToken = SiteSetting::get('twilio_auth_token');

        if (!$sid || !$from || !$encryptedToken) {
            Log::info("Twilio not configured. SMS to {$phone}: {$message}");
            return false;
        }

        try {
            $token = Crypt::decryptString($encryptedToken);
        } catch (\Throwable) {
            // Token saved before encryption was applied — use as-is
            $token = $encryptedToken;
        }

        $to = $this->toE164($phone);

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To'   => $to,
                    'Body' => $message,
                ]);

            if ($response->failed()) {
                Log::error("Twilio SMS failed to {$to}: " . $response->body());
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("Twilio SMS exception: " . $e->getMessage());
            return false;
        }
    }

    public function sendAccessCode(string $phone, string $code, string $medId): bool
    {
        $message = "MedRecords access code: {$code}. Valid 10 mins. Your MedID: {$medId}. Show this code to your provider at reception.";
        return $this->send($phone, $message);
    }

    /** Convert Nigerian national format (08XXXXXXXXX) to E.164 (+234XXXXXXXXX). */
    private function toE164(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '234')) {
            return '+' . $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '+234' . substr($digits, 1);
        }

        return '+' . $digits;
    }
}
