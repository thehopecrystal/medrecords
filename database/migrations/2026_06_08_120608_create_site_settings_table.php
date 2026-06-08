<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        $defaults = [
            'site_name'        => 'MedRecords',
            'site_tagline'     => 'Your health record, always with you',
            'site_logo_url'    => '',
            'footer_about'     => 'MedRecords is Nigeria\'s patient-owned electronic health record platform. Secure, consent-driven, accessible anywhere.',
            'hero_headline'    => 'Your health record,<br>always with you',
            'hero_subtext'     => 'MedRecords gives every Nigerian a secure, patient-owned electronic health record — accessible anywhere, shared only with your consent.',
            'hero_cta_primary' => 'Get started free',
            'hero_cta_secondary' => 'Learn more',
            'contact_phone_1'  => '+234 (0) 800 MEDRECORDS',
            'contact_phone_2'  => '',
            'contact_email'    => 'hello@medrecords.ng',
            'contact_address'  => '14 Adeola Odeku Street',
            'contact_city'     => 'Victoria Island',
            'contact_state'    => 'Lagos',
            'social_facebook'  => '',
            'social_twitter'   => '',
            'social_instagram' => '',
            'social_linkedin'  => '',
            'social_youtube'   => '',
            'smtp_host'        => '',
            'smtp_port'        => '587',
            'smtp_encryption'  => 'tls',
            'smtp_username'    => '',
            'smtp_password'    => '',
            'smtp_from_email'  => 'noreply@medrecords.ng',
            'smtp_from_name'   => 'MedRecords',
        ];

        $now = now();
        foreach ($defaults as $key => $value) {
            DB::table('site_settings')->insert([
                'key'        => $key,
                'value'      => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
