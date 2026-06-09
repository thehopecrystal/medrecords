<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AccessCode extends Model
{
    protected $fillable = ['patient_id', 'code', 'expires_at', 'used_at', 'used_by', 'sms_sent'];

    /**
     * Return the plaintext code, decrypting if the stored value is an encrypted string.
     * Falls back to returning the raw value for any legacy plaintext rows.
     */
    public function getDecryptedCodeAttribute(): string
    {
        try {
            return Crypt::decryptString($this->code);
        } catch (\Throwable) {
            return $this->code;
        }
    }

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'used_at' => 'datetime'];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function usedBy() { return $this->belongsTo(User::class, 'used_by'); }

    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }
}
