<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->index();

            // core lead
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('alt_phone', 40)->nullable();
            $table->string('source')->nullable(); // Portal / Referral / Walk-in
            $table->enum('status', ['new','contacted','site_visit','booked','lost'])->default('new')->index();

            // lifecycle
            $table->unsignedBigInteger('created_by')->nullable()->index(); // who created this lead
            $table->unsignedBigInteger('assigned_to')->nullable()->index(); // who owns it right now (admin can assign)
            $table->timestamp('follow_up_at')->nullable();

            // optional project linkage (only when materialised)
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->unsignedBigInteger('project_unit_id')->nullable()->index();
            $table->boolean('is_customer')->default(false);
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->timestamp('materialized_at')->nullable();

            // preferences
            $table->string('preferred_bhk', 20)->nullable();
            $table->decimal('budget_min', 14, 2)->nullable();
            $table->decimal('budget_max', 14, 2)->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // helpful unique to reduce accidental duplicates; not bulletproof for NULLs
            $table->unique(['org_id','name','phone','email'],'leads_org_name_phone_email_unique');
        });
    }

    public function down(): void {
        Schema::dropIfExists('leads');
    }
};