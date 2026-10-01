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
        Schema::table('seo_metas', function (Blueprint $table) {
            $table->string('og_title')->nullable()->after('meta_keywords');
            $table->text('og_description')->nullable()->after('og_title');
            $table->string('og_image')->nullable()->after('og_description');
            $table->string('og_image_alt')->nullable()->after('og_image');

            $table->string('twitter_title')->nullable()->after('og_image_alt');
            $table->text('twitter_description')->nullable()->after('twitter_title');
            $table->string('twitter_image')->nullable()->after('twitter_description');
            $table->string('twitter_image_alt')->nullable()->after('twitter_image');
            $table->text('schema_json')->nullable()->after('twitter_image_alt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_metas', function (Blueprint $table) {
            $table->dropColumn([
                'og_title', 'og_description', 'og_image', 'og_image_alt',
                'twitter_title', 'twitter_description', 'twitter_image', 'twitter_image_alt',
                'schema_json'
            ]);
        });
    }
};
