<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('project_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->index();
            $table->unsignedBigInteger('project_id')->index();
            $table->string('title');
            $table->enum('type', ['image','pdf','doc','video','link'])->default('image');
            $table->string('file_path')->nullable();   // for uploaded files
            $table->string('url')->nullable();         // for external links / videos
            $table->json('tags')->nullable();
            $table->boolean('is_public')->default(true);
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('project_materials'); }
};