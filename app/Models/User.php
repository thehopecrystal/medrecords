<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'nin', 'role', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isPatient(): bool { return $this->role === 'patient'; }
    public function isProvider(): bool { return $this->role === 'provider'; }
    public function isPharmacist(): bool { return $this->role === 'pharmacist'; }
    public function isFacilityAdmin(): bool { return $this->role === 'facility_admin'; }
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }

    public function patient() { return $this->hasOne(Patient::class); }
    public function provider() { return $this->hasOne(Provider::class); }
    public function consentsGranted() { return $this->hasMany(Consent::class, 'grantee_id'); }
}
