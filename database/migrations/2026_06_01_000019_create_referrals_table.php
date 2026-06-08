<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referring_provider_id')->constrained('users')->cascadeOnDelete();
            $table->string('referred_to');
            $table->string('specialty');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'accepted', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
