<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessCode extends Model
{
    protected $fillable = ['patient_id', 'code', 'expires_at', 'used_at', 'used_by', 'sms_sent'];

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
