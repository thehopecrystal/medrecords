<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccination extends Model
{
    protected $fillable = ['patient_id', 'administered_by', 'vaccine_name', 'batch_no', 'administered_at', 'next_due', 'site', 'notes'];

    protected function casts(): array
    {
        return ['administered_at' => 'date', 'next_due' => 'date'];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function administeredBy() { return $this->belongsTo(User::class, 'administered_by'); }
}
