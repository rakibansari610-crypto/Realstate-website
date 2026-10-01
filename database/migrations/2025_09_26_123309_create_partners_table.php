<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('org_id')->index();
            $table->unsignedBigInteger('parent_id')->nullable()->index(); // CP hierarchy (parent CP)
            $table->unsignedBigInteger('user_id')->nullable()->index();   // linked login (optional)

            $table->string('type')->index();

            // Personal
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('alt_phone', 40)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 120)->nullable();
            $table->string('state', 120)->nullable();
            $table->string('pincode', 20)->nullable();

            // Documents
            $table->string('pan_no', 20)->nullable();
            $table->string('gst_no', 30)->nullable();
            $table->string('rera_no', 50)->nullable();
            $table->string('pan_path')->nullable();   // /media/partners/<file>
            $table->string('gst_path')->nullable();
            $table->string('rera_path')->nullable();

            // Bank
            $table->string('bank_account_holder')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('ifsc', 20)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('upi_id')->nullable();

            // Commissions (default)
            $table->enum('default_commission_type', ['percent', 'flat'])->nullable();
            $table->decimal('default_commission', 14, 2)->nullable();

            // Status
            $table->boolean('approved')->default(false); // admin approval
            $table->boolean('active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Helpful uniqueness (per org)
            $table->unique(['org_id', 'type', 'email'], 'partners_org_type_email_unique');
            $table->unique(['org_id', 'type', 'phone'], 'partners_org_type_phone_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
