<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eye_care_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->string('od_prescription')->nullable();
            $table->string('os_prescription')->nullable();
            $table->string('visual_acuity_od')->nullable();
            $table->string('visual_acuity_os')->nullable();
            $table->string('iop_od')->nullable();
            $table->string('iop_os')->nullable();
            $table->text('fundus_notes')->nullable();
            $table->text('notes')->nullable();
            $table->date('exam_date');
            $table->date('next_exam_due')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eye_care_records');
    }
};
