<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    protected $fillable = ['patient_id', 'encounter_id', 'provider_id', 'name', 'cpt_code', 'operative_notes', 'complications', 'performed_at'];

    protected function casts(): array
    {
        return ['performed_at' => 'date'];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function encounter() { return $this->belongsTo(Encounter::class); }
    public function provider() { return $this->belongsTo(User::class, 'provider_id'); }
}
