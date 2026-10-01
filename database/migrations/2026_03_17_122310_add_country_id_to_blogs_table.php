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
        Schema::table('blogs', function (Blueprint $table) {

            // Add column (nullable initially to avoid breaking existing data)
            $table->unsignedBigInteger('country_id')->nullable()->after('id');

            // Add index for performance
            $table->index('country_id');

            // Add foreign key (optional but recommended)
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('set null'); // safer than cascade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {

            // Drop FK first
            $table->dropForeign(['country_id']);

            // Drop index
            $table->dropIndex(['country_id']);

            // Drop column
            $table->dropColumn('country_id');
        });
    }
};
