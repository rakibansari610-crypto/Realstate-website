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
        Schema::table('units', function (Blueprint $table) {
             $table->boolean('is_for_rent')->default(false)->after('is_for_sale');
            $table->decimal('rent_price', 15, 4)->nullable()->after('selling_price');
            $table->string('rent_period')->nullable()->after('rent_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
             $table->dropColumn([
                'is_for_rent',
                'rent_price',
                'rent_period'
            ]);
        });
    }
};
