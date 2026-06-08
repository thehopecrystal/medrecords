<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DentalRecord extends Model
{
    protected $fillable = ['patient_id', 'provider_id', 'facility_id', 'tooth_chart', 'procedure', 'notes', 'visit_date', 'next_cleaning_due'];

    protected function casts(): array
    {
        return [
            'tooth_chart'       => 'array',
            'visit_date'        => 'date',
            'next_cleaning_due' => 'date',
        ];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function provider() { return $this->belongsTo(User::class, 'provider_id'); }
    public function facility() { return $this->belongsTo(Facility::class); }
}
