<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabResult extends Model
{
    protected $fillable = [
        'patient_id', 'encounter_id', 'ordered_by', 'test_name', 'loinc_code',
        'result', 'unit', 'reference_range', 'flag', 'collected_at', 'notes',
    ];

    protected function casts(): array
    {
        return ['collected_at' => 'date'];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function encounter() { return $this->belongsTo(Encounter::class); }
    public function orderedBy() { return $this->belongsTo(User::class, 'ordered_by'); }

    public function getFlagColorAttribute(): string
    {
        return match($this->flag) {
            'high', 'critical' => 'red',
            'low'              => 'amber',
            default            => 'green',
        };
    }
}
