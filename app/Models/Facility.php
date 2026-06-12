<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = ['owner_user_id', 'name', 'type', 'license_no', 'address', 'city', 'state', 'phone', 'email', 'is_active'];

    public function owner()    { return $this->belongsTo(\App\Models\Admin::class, 'owner_user_id'); }
    public function providers() { return $this->hasMany(Provider::class); }
    public function encounters() { return $this->hasMany(Encounter::class); }
    public function dispenses() { return $this->hasMany(Dispense::class); }
}
