<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = ['patient_id', 'referring_provider_id', 'referred_to', 'specialty', 'reason', 'notes', 'status'];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function referringProvider() { return $this->belongsTo(User::class, 'referring_provider_id'); }
}
