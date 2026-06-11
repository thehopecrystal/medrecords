<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'rx_id', 'patient_id', 'encounter_id', 'prescribed_by',
        'drug_name', 'dose', 'frequency', 'duration_days',
        'diagnosis_context', 'instructions', 'status',
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function encounter() { return $this->belongsTo(Encounter::class); }
    public function prescribedBy() { return $this->belongsTo(User::class, 'prescribed_by'); }
    public function dispenses() { return $this->hasMany(Dispense::class); }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active'    => 'green',
            'dispensed' => 'navy',
            'expired'   => 'grey',
            'cancelled' => 'red',
            default     => 'grey',
        };
    }
}
