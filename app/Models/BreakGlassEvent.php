<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakGlassEvent extends Model
{
    protected $fillable = [
        'patient_id', 'provider_id', 'reason_code', 'justification',
        'status', 'expires_at', 'reviewed_at', 'reviewed_by', 'review_notes',
    ];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function provider() { return $this->belongsTo(User::class, 'provider_id'); }
    public function reviewedBy() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
