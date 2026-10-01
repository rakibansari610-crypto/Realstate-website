<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->index();
            $table->unsignedBigInteger('lead_id')->unique();
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->unsignedBigInteger('project_unit_id')->nullable()->index();

            // snapshot from lead
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('customers');
    }
};
