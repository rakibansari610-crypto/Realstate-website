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
            $table->string('meta_title')->nullable()->after('source');
            $table->string('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('pincode')->nullable()->after('location');
            $table->string('locality')->nullable()->after('pincode');
            $table->string('draft')->nullable()->after('locality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords','locality','pincode','draft']); 
        });
    }
};
