<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // audit_logs.actor_id can now reference either users OR admins — drop the FK,
        // keep as a plain integer. The application layer resolves the correct model.
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['actor_id']);
        });

        // break_glass_events.reviewed_by is set by an admin — drop the FK to users.
        Schema::table('break_glass_events', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
        });

        // notifications.user_id can belong to any authenticated user (user or admin).
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // facilities.owner_user_id now references the admins table.
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropForeign(['owner_user_id']);
            $table->foreign('owner_user_id')->references('id')->on('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropForeign(['owner_user_id']);
            $table->foreign('owner_user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('break_glass_events', function (Blueprint $table) {
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('actor_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
