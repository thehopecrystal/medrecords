<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_results', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('notes');
            $table->boolean('patient_uploaded')->default(false)->after('file_path');
            $table->foreignId('ordered_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lab_results', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'patient_uploaded']);
        });
    }
};
