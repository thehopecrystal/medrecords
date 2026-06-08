<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['outpatient', 'inpatient', 'emergency', 'telemedicine', 'dental', 'optical', 'lab']);
            $table->string('chief_complaint')->nullable();
            $table->text('history')->nullable();
            $table->string('diagnosis')->nullable();
            $table->string('icd10_code')->nullable();
            $table->string('vitals')->nullable();
            $table->text('assessment_plan')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'signed', 'amended'])->default('draft');
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encounters');
    }
};
