<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('provider_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('cpt_code')->nullable();
            $table->text('operative_notes')->nullable();
            $table->text('complications')->nullable();
            $table->date('performed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
