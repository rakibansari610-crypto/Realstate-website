<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            // Add new fields after permit_number 
            $table->string('plan_type', 50)->after('permit_number')->nullable();
            $table->string('street', 50)->after('plan_type')->nullable();
            $table->string('unit_number', 50)->after('street')->nullable();
            $table->string('owner', 50)->after('unit_number')->nullable();
            $table->string('listing_agent', 50)->after('owner')->nullable();
            $table->string('furnished', 50)->after('listing_agent')->nullable();
            $table->string('size', 50)->after('furnished')->nullable();
            $table->string('view', 50)->after('size')->nullable();
            $table->string('parking_spaces', 50)->after('view')->nullable();
            $table->string('parking_number', 50)->after('parking_spaces')->nullable();
            $table->string('parking_type', 50)->after('parking_number')->nullable();
            $table->string('unit_fitting', 50)->after('parking_type')->nullable();
           
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['plan_type','street','unit_number','owner','listing_agent','furnished','size','view','parking_spaces','parking_number','parking_type','unit_fitting']);
        });
    }
};
