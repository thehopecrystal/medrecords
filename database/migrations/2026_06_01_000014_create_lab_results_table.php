<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ordered_by')->constrained('users')->cascadeOnDelete();
            $table->string('test_name');
            $table->string('loinc_code')->nullable();
            $table->string('result');
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->enum('flag', ['normal', 'high', 'low', 'critical'])->default('normal');
            $table->date('collected_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};
