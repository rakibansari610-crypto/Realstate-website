<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('developers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->index();
            $table->string('name');
            $table->string('slug')->nullable();  
            $table->string('contact_person_name')->nullable();         
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();            // storage path (public disk)
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('country')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['org_id', 'name']);                 // unique per org
            // $table->foreign('org_id')->references('id')->on('organizations')->cascadeOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('developers');
    }
};
