<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();

            $table->string('source')->nullable();
            $table->string('source_unit_code')->nullable();
            $table->string('image_type')->nullable();
            $table->string('title')->nullable();
            $table->boolean('is_marketing_image')->default(false);
            $table->text('image_remarks')->nullable();
            $table->text('image_url');
            $table->string('image_hash', 64);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->unique(['unit_id', 'image_hash']);
            $table->index(['source', 'source_unit_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_images');
    }
};
