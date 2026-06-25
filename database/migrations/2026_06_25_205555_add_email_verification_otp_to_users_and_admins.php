<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Switches email verification from a signed-link flow to a 6-digit OTP flow.
 *
 * The 2FA login OTP already lives in `otp_code` / `otp_expires_at` on both
 * `users` and `admins`. To avoid a single column holding two different OTPs
 * at the same time (and to keep auditability), the email-verification code
 * gets its own dedicated columns.
 *
 * - email_verification_code : bcrypt hash of the 6-digit code (nullable)
 * - email_verification_expires_at : 10-minute window from issue (nullable)
 * - email_verification_attempts : wrong-code counter; capped at 5 per code
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email_verification_code', 255)->nullable()->after('email_verified_at');
            $table->timestamp('email_verification_expires_at')->nullable()->after('email_verification_code');
            $table->unsignedTinyInteger('email_verification_attempts')->default(0)->after('email_verification_expires_at');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->string('email_verification_code', 255)->nullable()->after('email_verified_at');
            $table->timestamp('email_verification_expires_at')->nullable()->after('email_verification_code');
            $table->unsignedTinyInteger('email_verification_attempts')->default(0)->after('email_verification_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_verification_code', 'email_verification_expires_at', 'email_verification_attempts']);
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['email_verification_code', 'email_verification_expires_at', 'email_verification_attempts']);
        });
    }
};
