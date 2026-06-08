<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id', 'med_id', 'dob', 'gender', 'blood_type', 'genotype',
        'address', 'city', 'state', 'emergency_contact_name',
        'emergency_contact_phone', 'nin_verified',
    ];

    protected function casts(): array
    {
        return ['dob' => 'date', 'nin_verified' => 'boolean'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function allergies() { return $this->hasMany(Allergy::class); }
    public function conditions() { return $this->hasMany(Condition::class); }
    public function encounters() { return $this->hasMany(Encounter::class); }
    public function prescriptions() { return $this->hasMany(Prescription::class); }
    public function vaccinations() { return $this->hasMany(Vaccination::class); }
    public function labResults() { return $this->hasMany(LabResult::class); }
    public function dentalRecords() { return $this->hasMany(DentalRecord::class); }
    public function eyeCareRecords() { return $this->hasMany(EyeCareRecord::class); }
    public function procedures() { return $this->hasMany(Procedure::class); }
    public function hospitalizations() { return $this->hasMany(Hospitalization::class); }
    public function referrals() { return $this->hasMany(Referral::class); }
    public function consents() { return $this->hasMany(Consent::class); }
    public function auditLogs() { return $this->hasMany(AuditLog::class); }
    public function breakGlassEvents() { return $this->hasMany(BreakGlassEvent::class); }
    public function dependents() { return $this->hasMany(Dependent::class, 'guardian_patient_id'); }

    public function getAgeAttribute(): int
    {
        return $this->dob ? $this->dob->age : 0;
    }

    // Use the already-loaded collection when available; fall back to a query only when not loaded.
    public function getActiveAllergiesAttribute()
    {
        if ($this->relationLoaded('allergies')) {
            return $this->allergies->where('is_active', true)->values();
        }
        return $this->allergies()->where('is_active', true)->get();
    }

    public function getActiveConditionsAttribute()
    {
        if ($this->relationLoaded('conditions')) {
            return $this->conditions->whereIn('status', ['active', 'chronic'])->values();
        }
        return $this->conditions()->whereIn('status', ['active', 'chronic'])->get();
    }

    public function getActivePrescriptionsAttribute()
    {
        if ($this->relationLoaded('prescriptions')) {
            return $this->prescriptions->where('status', 'active')->values();
        }
        return $this->prescriptions()->where('status', 'active')->get();
    }
}
