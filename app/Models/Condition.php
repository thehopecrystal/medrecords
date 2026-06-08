<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    protected $fillable = ['patient_id', 'recorded_by', 'name', 'icd10_code', 'status', 'diagnosed_at', 'notes'];

    protected function casts(): array
    {
        return ['diagnosed_at' => 'date'];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}
