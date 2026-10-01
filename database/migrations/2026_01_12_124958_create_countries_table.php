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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();

            $table->string('country_name');
            $table->string('country_currency')->nullable();
            $table->string('country_lang')->nullable();
            $table->string('country_code')->nullable();
            $table->string('country_iso')->nullable();
            $table->string('country_isd')->nullable();
            $table->boolean('country_status')->default(1);
            $table->boolean('country_default')->default(0);
            $table->integer('country_priority')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
