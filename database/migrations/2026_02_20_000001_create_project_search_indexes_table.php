<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_search_indexes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->unique();

            // Core project fields
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('city')->nullable();
            $table->string('location')->nullable(); // community/area
            $table->string('status')->nullable();
            $table->decimal('min_price', 14, 2)->nullable();
            $table->decimal('max_price', 14, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->date('completion_date')->nullable();

            // A) Payment plan signals
            $table->boolean('has_post_handover')->default(false);
            $table->decimal('booking_percent_min', 6, 2)->nullable();
            $table->unsignedInteger('months_after_handover')->nullable();
            $table->string('plan_ratio')->nullable(); // e.g. 60/40
            $table->text('payment_plan_summary')->nullable();

            // B) Location/nearby summary
            $table->text('map_points_summary')->nullable();

            // Semantic text (optional; enables vector search later)
            $table->longText('search_text')->nullable();

            $table->timestamp('indexed_at')->nullable();
            $table->timestamps();

            $table->index(['city']);
            $table->index(['location']);
            $table->index(['status']);
            $table->index(['min_price']);
            $table->index(['max_price']);
            $table->index(['has_post_handover']);
            $table->index(['booking_percent_min']);
            $table->index(['months_after_handover']);
            $table->index(['plan_ratio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_search_indexes');
    }
};
