<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Providers can exist independently before being assigned to a facility.
        Schema::table('providers', function (Blueprint $table) {
            $table->dropForeign(['facility_id']);
            $table->unsignedBigInteger('facility_id')->nullable()->change();
            $table->foreign('facility_id')->references('id')->on('facilities')->nullOnDelete();
        });

        // Each facility is owned by a facility_admin user account.
        Schema::table('facilities', function (Blueprint $table) {
            $table->foreignId('owner_user_id')->nullable()->after('id')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropForeign(['owner_user_id']);
            $table->dropColumn('owner_user_id');
        });

        Schema::table('providers', function (Blueprint $table) {
            $table->dropForeign(['facility_id']);
            $table->unsignedBigInteger('facility_id')->nullable(false)->change();
            $table->foreign('facility_id')->references('id')->on('facilities')->cascadeOnDelete();
        });
    }
};
