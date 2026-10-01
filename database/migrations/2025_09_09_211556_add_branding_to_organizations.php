<?php

// database/migrations/2025_01_01_000000_add_branding_to_organizations.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('organizations', function (Blueprint $table) {
            $table->json('theme')->nullable();                    // JSON for colors, toggles, text
            $table->string('logo_path')->nullable();              // storage path for header logo
            $table->string('login_bg_path')->nullable();          // storage path for login background
            $table->string('favicon_path')->nullable();           // storage path for favicon
        });
    }
    public function down(): void {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['theme','logo_path','login_bg_path','favicon_path']);
        });
    }
};
