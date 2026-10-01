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
        Schema::create('lead_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('offered_amount', 15, 2)->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'closed'])->default('pending');
            $table->enum('offer_type', ['accepted', 'rejected'])->nullable();
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_offers');
    }
};
