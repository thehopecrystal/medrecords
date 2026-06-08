<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS. In production, plug in Africa's Talking or Termii here.
     * For now we log the outbound message and return true.
     */
    public function send(string $phone, string $message): bool
    {
        Log::info("SMS to {$phone}: {$message}");
        // TODO: replace with real gateway e.g. Africa's Talking
        // $response = Http::post('https://api.africastalking.com/version1/messaging', [...]);
        return true;
    }

    public function sendAccessCode(string $phone, string $code, string $medId): bool
    {
        $message = "MedRecords access code: {$code}. Valid 10 mins. Your MedID: {$medId}. Show this code to your provider at reception.";
        return $this->send($phone, $message);
    }
}
