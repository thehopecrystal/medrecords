<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'patient_id', 'actor_id', 'action', 'resource_type',
        'resource_id', 'description', 'ip_address',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }

    public static function record(int $patientId, int $actorId, string $action, string $description, ?string $resourceType = null, ?int $resourceId = null): void
    {
        static::create([
            'patient_id'    => $patientId,
            'actor_id'      => $actorId,
            'action'        => $action,
            'resource_type' => $resourceType,
            'resource_id'   => $resourceId,
            'description'   => $description,
            'ip_address'    => request()->ip(),
        ]);
    }
}
