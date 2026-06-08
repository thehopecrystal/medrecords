<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encounter extends Model
{
    protected $fillable = [
        'patient_id', 'provider_id', 'facility_id', 'type',
        'chief_complaint', 'history', 'diagnosis', 'icd10_code',
        'vitals', 'assessment_plan', 'notes', 'status', 'signed_at',
    ];

    protected function casts(): array
    {
        return ['signed_at' => 'datetime'];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function provider() { return $this->belongsTo(User::class, 'provider_id'); }
    public function facility() { return $this->belongsTo(Facility::class); }
    public function prescriptions() { return $this->hasMany(Prescription::class); }
    public function labResults() { return $this->hasMany(LabResult::class); }
    public function procedures() { return $this->hasMany(Procedure::class); }
}
