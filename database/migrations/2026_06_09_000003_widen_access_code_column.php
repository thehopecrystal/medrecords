<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_codes', function (Blueprint $table) {
            // Widen to hold a Laravel-encrypted string instead of the raw 6-digit code.
            $table->string('code', 500)->change();
        });
    }

    public function down(): void
    {
        Schema::table('access_codes', function (Blueprint $table) {
            $table->string('code', 6)->change();
        });
    }
};
