<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('administered_by')->constrained('users')->cascadeOnDelete();
            $table->string('vaccine_name');
            $table->string('batch_no')->nullable();
            $table->date('administered_at');
            $table->date('next_due')->nullable();
            $table->string('site')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccinations');
    }
};
