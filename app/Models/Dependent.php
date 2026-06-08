<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dependent extends Model
{
    protected $fillable = ['guardian_patient_id', 'dependent_patient_id', 'relationship'];

    public function guardian() { return $this->belongsTo(Patient::class, 'guardian_patient_id'); }
    public function dependent() { return $this->belongsTo(Patient::class, 'dependent_patient_id'); }
}
