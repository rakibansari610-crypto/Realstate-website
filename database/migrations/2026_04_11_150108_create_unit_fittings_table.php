<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_fittings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();

            $table->string('source')->nullable(); // goyzer
            $table->string('source_fixture_id')->nullable(); // i_d from API
            $table->string('title')->nullable(); // Amenity
            $table->string('name'); // Covered Parking

            $table->timestamps();

            $table->unique(['unit_id', 'source_fixture_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_fittings');
    }
};
