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
        Schema::table('leads', function (Blueprint $table) {
            //
            $table->string('property_type')->nullable()->after('budget_max');
            $table->string('property_build')->nullable()->after('property_type');
            $table->string('timeframe')->nullable()->after('property_build');
            $table->string('pref_langugae')->nullable()->after('timeframe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            //
            $table->dropColumn(['property_type','property_build','timeframe','pref_langugae']);
        });
    }
};
