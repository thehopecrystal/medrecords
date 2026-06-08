<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grantee_id')->constrained('users')->cascadeOnDelete();
            $table->enum('scope', ['full', 'encounter', 'prescription', 'lab', 'dental', 'eye_care', 'vaccination', 'dispense', 'custom']);
            $table->json('scope_details')->nullable();
            $table->enum('status', ['pending', 'active', 'revoked', 'expired'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consents');
    }
};
