<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    protected $fillable = ['patient_id', 'recorded_by', 'allergen', 'severity', 'reaction', 'confirmed_at', 'is_active'];

    protected function casts(): array
    {
        return ['confirmed_at' => 'date', 'is_active' => 'boolean'];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'severe', 'life_threatening' => 'red',
            'moderate' => 'amber',
            default    => 'navy',
        };
    }
}
