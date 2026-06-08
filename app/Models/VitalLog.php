<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VitalLog extends Model
{
    protected $table = 'vitals_logs';

    protected $fillable = [
        'patient_id', 'logged_at',
        'bp_systolic', 'bp_diastolic', 'heart_rate',
        'temperature_c', 'weight_kg', 'glucose_mmol',
        'oxygen_saturation', 'pain_level', 'notes',
    ];

    protected function casts(): array
    {
        return ['logged_at' => 'datetime'];
    }

    public function patient() { return $this->belongsTo(Patient::class); }

    public function getBpAttribute(): ?string
    {
        if ($this->bp_systolic && $this->bp_diastolic) {
            return "{$this->bp_systolic}/{$this->bp_diastolic} mmHg";
        }
        return null;
    }
}
