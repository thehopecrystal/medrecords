<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospitalization extends Model
{
    protected $fillable = ['patient_id', 'facility_id', 'admitting_diagnosis', 'discharge_diagnosis', 'admitted_at', 'discharged_at', 'discharge_summary'];

    protected function casts(): array
    {
        return ['admitted_at' => 'date', 'discharged_at' => 'date'];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function facility() { return $this->belongsTo(Facility::class); }
}
