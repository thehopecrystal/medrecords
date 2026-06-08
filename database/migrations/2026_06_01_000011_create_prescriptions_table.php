<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('rx_id')->unique();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('prescribed_by')->constrained('users')->cascadeOnDelete();
            $table->string('drug_name');
            $table->string('dose');
            $table->string('frequency');
            $table->integer('duration_days')->nullable();
            $table->string('diagnosis_context')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('status', ['active', 'dispensed', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
