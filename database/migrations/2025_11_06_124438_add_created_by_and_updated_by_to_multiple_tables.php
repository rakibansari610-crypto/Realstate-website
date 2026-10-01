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
       Schema::table('developers', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('id');
            $table->integer('updated_by')->nullable()->after('created_by');
        });
         Schema::table('projects', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('id');
            $table->integer('updated_by')->nullable()->after('created_by');
        });
         Schema::table('project_materials', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('id');
            $table->integer('updated_by')->nullable()->after('created_by');
        });
        Schema::table('units', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('id');
            $table->integer('updated_by')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('developers', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('project_materials', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
