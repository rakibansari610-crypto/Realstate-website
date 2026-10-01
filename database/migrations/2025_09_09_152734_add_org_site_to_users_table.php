<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->foreignId('org_id')->nullable()->after('id')->constrained('organizations')->nullOnDelete();
            $t->foreignId('site_id')->nullable()->after('org_id')->constrained('sites')->nullOnDelete();
            // Make email unique per org (drop global unique if exists)
            try {
                $t->dropUnique('users_email_unique');
            } catch (\Throwable $e) {
            }
            $t->unique(['org_id', 'email']);
            $t->index(['org_id', 'site_id']);
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropUnique(['org_id', 'email']);
            $t->dropConstrainedForeignId('site_id');
            $t->dropConstrainedForeignId('org_id');
            // You can add back global unique if your app relied on it
            // $t->unique('email');
        });
    }
};
