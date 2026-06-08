<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dependents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('dependent_patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('relationship');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dependents');
    }
};
