<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispense extends Model
{
    protected $fillable = [
        'prescription_id', 'pharmacist_id', 'facility_id',
        'quantity', 'batch_lot', 'price', 'payment_method', 'counseling_notes',
    ];

    public function prescription() { return $this->belongsTo(Prescription::class); }
    public function pharmacist() { return $this->belongsTo(User::class, 'pharmacist_id'); }
    public function facility() { return $this->belongsTo(Facility::class); }
}
