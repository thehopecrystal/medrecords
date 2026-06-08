<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EyeCareRecord extends Model
{
    protected $fillable = [
        'patient_id', 'provider_id', 'facility_id',
        'od_prescription', 'os_prescription', 'visual_acuity_od', 'visual_acuity_os',
        'iop_od', 'iop_os', 'fundus_notes', 'notes', 'exam_date', 'next_exam_due',
    ];

    protected function casts(): array
    {
        return ['exam_date' => 'date', 'next_exam_due' => 'date'];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function provider() { return $this->belongsTo(User::class, 'provider_id'); }
    public function facility() { return $this->belongsTo(Facility::class); }
}
