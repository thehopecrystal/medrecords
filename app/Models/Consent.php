<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consent extends Model
{
    protected $fillable = [
        'patient_id', 'grantee_id', 'scope', 'scope_details',
        'status', 'expires_at', 'granted_at', 'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'scope_details' => 'array',
            'expires_at'    => 'datetime',
            'granted_at'    => 'datetime',
            'revoked_at'    => 'datetime',
        ];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function grantee() { return $this->belongsTo(User::class, 'grantee_id'); }

    public function isActive(): bool
    {
        return $this->status === 'active' && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function getScopeLabelAttribute(): string
    {
        return match($this->scope) {
            'full'         => 'Full access',
            'encounter'    => 'Encounters only',
            'prescription' => 'Prescriptions',
            'lab'          => 'Lab results',
            'dental'       => 'Dental records',
            'eye_care'     => 'Eye care',
            'vaccination'  => 'Vaccinations',
            'dispense'     => 'Dispense only',
            'custom'       => 'Custom scope',
            default        => ucfirst($this->scope),
        };
    }
}
