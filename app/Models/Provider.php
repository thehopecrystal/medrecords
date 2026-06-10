<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['user_id', 'facility_id', 'specialty', 'license_no', 'mdcn_folio', 'is_active',
                          'mdcn_verified', 'mdcn_verified_at', 'mdcn_status'];

    public function user() { return $this->belongsTo(User::class); }
    public function facility() { return $this->belongsTo(Facility::class); }
    public function encounters() { return $this->hasMany(Encounter::class, 'provider_id', 'user_id'); }
    public function prescriptions() { return $this->hasMany(Prescription::class, 'prescribed_by', 'user_id'); }
}
