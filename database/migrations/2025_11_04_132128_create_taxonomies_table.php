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
        Schema::create('taxonomies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('module'); // e.g., 'product', 'post'
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('priority')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('parent_id')->nullable()->constrained('taxonomies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxonomies');
    }
};
