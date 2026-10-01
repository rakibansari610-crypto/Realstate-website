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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('payment_plan')->nullable()->after('sub_category_id');
            $table->string('project_type')->nullable()->after('payment_plan');
            $table->string('area_size')->nullable()->after('project_type');
            $table->string('handover_payment_plan')->nullable()->after('area_size');
            $table->text('building_description')->nullable()->after('handover_payment_plan');
            $table->string('handover')->nullable()->after('building_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([ 'payment_plan', 'project_type', 'area_size',
                'handover_payment_plan', 'building_description', 'handover',
            ]);
        });
    }
};
