<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('slug', 80)->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('billing_period', ['free', 'monthly', 'yearly', 'one-time'])->default('monthly');
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('badge', 40)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('plans')->insert([
            ['name'=>'Free','slug'=>'free','description'=>'Perfect for individuals starting their health record journey.','price'=>0,'billing_period'=>'free','features'=>json_encode(['Unlimited health records','Access code generation','1 dependent record','Basic consent management','30-day audit log']),'is_active'=>1,'is_featured'=>0,'badge'=>null,'sort_order'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Basic','slug'=>'basic','description'=>'For families needing more features and dependents.','price'=>1500,'billing_period'=>'monthly','features'=>json_encode(['Everything in Free','Up to 5 dependents','SMS access code delivery','Full audit history','NHIA eligibility checks','Priority consent approvals']),'is_active'=>1,'is_featured'=>0,'badge'=>null,'sort_order'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Premium','slug'=>'premium','description'=>'Full access for the best care coordination.','price'=>3500,'billing_period'=>'monthly','features'=>json_encode(['Everything in Basic','Unlimited dependents','Family health dashboard','Prescription refill reminders','Lab result trends','Emergency contact alerts','Priority support']),'is_active'=>1,'is_featured'=>1,'badge'=>'Most Popular','sort_order'=>3,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Enterprise','slug'=>'enterprise','description'=>'For hospitals, clinics, and health organisations.','price'=>0,'billing_period'=>'one-time','features'=>json_encode(['Everything in Premium','Unlimited provider accounts','Custom NHIA integration','MDCN bulk verification','Dedicated account manager','Custom SLA & uptime','API access','White-label option']),'is_active'=>1,'is_featured'=>0,'badge'=>'Contact us','sort_order'=>4,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
