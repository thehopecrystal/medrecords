<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Widen otp_code to hold a bcrypt hash (60 chars) instead of just the raw 6-digit code.
            $table->string('otp_code', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('otp_code', 8)->nullable()->change();
        });
    }
};
