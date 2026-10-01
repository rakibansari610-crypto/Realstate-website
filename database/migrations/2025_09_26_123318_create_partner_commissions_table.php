<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('partner_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->index();
            $table->unsignedBigInteger('partner_id')->index();
            $table->unsignedBigInteger('project_id')->index();

            $table->enum('type', ['percent', 'flat']);
            $table->decimal('value', 14, 2);          // % or flat amount
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->unique(['org_id', 'partner_id', 'project_id'], 'partner_comm_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_commissions');
    }
};
