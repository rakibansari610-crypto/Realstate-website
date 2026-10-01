<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->index();
            $table->unsignedBigInteger('developer_id')->index();
            $table->string('name');
            $table->string('slug')->nullable()->index();
            $table->string('rera_no')->nullable();
            $table->string('city')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['planning','launched','under_construction','ready_to_move','completed'])->default('planning');
            $table->date('launch_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->decimal('min_price', 14, 2)->nullable();
            $table->decimal('max_price', 14, 2)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('cover_image_path')->nullable();   // Storage public disk
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['org_id','name']);
            // $table->foreign('developer_id')->references('id')->on('developers')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('projects'); }
};