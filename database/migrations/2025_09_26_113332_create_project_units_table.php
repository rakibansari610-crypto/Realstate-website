<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('project_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->index();
            $table->unsignedBigInteger('project_id')->index();
            $table->string('unit_no');
            $table->string('tower')->nullable();
            $table->string('floor')->nullable();
            $table->string('bhk')->nullable();                 // ex: 1BHK/2BHK/3BHK
            $table->decimal('carpet_area', 10, 2)->nullable();
            $table->decimal('builtup_area', 10, 2)->nullable();
            $table->decimal('super_area', 10, 2)->nullable();
            $table->decimal('price', 14, 2)->nullable();
            $table->enum('status', ['available','blocked','booked','sold'])->default('available');
            $table->string('facing')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['org_id','project_id','unit_no']);
        });
    }
    public function down(): void { Schema::dropIfExists('project_units'); }
};
