<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefillRequest extends Model
{
    protected $fillable = [
        'prescription_id', 'patient_id', 'provider_id',
        'reason', 'status', 'provider_notes', 'actioned_at',
    ];

    protected function casts(): array
    {
        return ['actioned_at' => 'datetime'];
    }

    public function prescription() { return $this->belongsTo(Prescription::class); }
    public function patient()      { return $this->belongsTo(Patient::class); }
    public function provider()     { return $this->belongsTo(User::class, 'provider_id'); }

    public function isPending():  bool { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isDenied():   bool { return $this->status === 'denied'; }

    public function scopePending($query) { return $query->where('status', 'pending'); }
}
