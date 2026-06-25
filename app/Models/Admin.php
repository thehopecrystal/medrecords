<?php

namespace App\Models;

use App\Auth\SendsEmailVerificationOtp;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SendsEmailVerificationOtp;

    protected $table = 'admins';

    protected $fillable = ['name', 'email', 'phone', 'password', 'avatar', 'last_login_at', 'two_factor_enabled',
                           'email_verification_code', 'email_verification_expires_at', 'email_verification_attempts'];

    protected $hidden = ['password', 'remember_token', 'otp_code', 'otp_expires_at'];

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

    public function isFacilityAdmin(): bool { return $this->role === 'facility_admin'; }
    public function isSuperAdmin(): bool    { return $this->role === 'super_admin'; }

    public function managedFacility(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Facility::class, 'owner_user_id');
    }
}
