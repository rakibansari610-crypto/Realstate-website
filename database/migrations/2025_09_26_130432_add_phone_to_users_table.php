<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 40)->nullable()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');

            // Unique within org (compatible with your existing org+email unique)
            $table->unique(['org_id','phone'], 'users_org_id_phone_unique');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_org_id_phone_unique');
            $table->dropColumn(['phone','phone_verified_at']);
        });
    }
};
