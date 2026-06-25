<?php

namespace App\Models;

use App\Auth\SendsEmailVerificationOtp;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SendsEmailVerificationOtp;

    protected $fillable = ['name', 'email', 'phone', 'nin', 'password', 'avatar', 'last_login_at', 'two_factor_enabled',
                           'email_verification_code', 'email_verification_expires_at', 'email_verification_attempts'];

    protected $hidden = ['password', 'remember_token', 'otp_code', 'otp_expires_at', 'nin'];

    protected function casts(): array
    {
        return [
            'email_verified_at'              => 'datetime',
            'email_verification_expires_at'  => 'datetime',
            'last_login_at'                  => 'datetime',
            'otp_expires_at'                 => 'datetime',
            'is_active'                      => 'boolean',
            'two_factor_enabled'             => 'boolean',
            'email_verification_attempts'    => 'integer',
            'password'                       => 'hashed',
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
