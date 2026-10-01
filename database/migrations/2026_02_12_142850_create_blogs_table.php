<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            $table->string('author_name')->nullable(); 
            $table->string('category')->nullable();

            $table->string('featured_image')->nullable();
            $table->string('detailed_image')->nullable();

            $table->text('short_description')->nullable();  
            $table->longText('content');  

            $table->date('publish_date')->nullable();

            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_active')->nullable();
            $table->integer('update_by')->nullable();
            $table->integer('sort_order')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
