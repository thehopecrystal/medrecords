<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->boolean('mdcn_verified')->default(false)->after('mdcn_folio');
            $table->timestamp('mdcn_verified_at')->nullable()->after('mdcn_verified');
            $table->string('mdcn_status')->default('unverified')->after('mdcn_verified_at');
        });

        Schema::table('access_codes', function (Blueprint $table) {
            $table->boolean('sms_sent')->default(false)->after('expires_at');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn(['mdcn_verified', 'mdcn_verified_at', 'mdcn_status']);
        });
        Schema::table('access_codes', function (Blueprint $table) {
            $table->dropColumn('sms_sent');
        });
        Schema::dropIfExists('notifications');
    }
};
