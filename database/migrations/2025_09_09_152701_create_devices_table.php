<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('devices', function (Blueprint $t) {
            $t->id();
            $t->foreignId('org_id')->constrained('organizations')->cascadeOnDelete();
            $t->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $t->string('name');
            $t->enum('type', ['KIOSK','SCANNER'])->default('KIOSK');
            $t->string('auth_token')->unique(); // device API token (rotateable)
            $t->string('status')->default('ACTIVE'); // ACTIVE/INACTIVE
            $t->timestamp('last_seen_at')->nullable();
            $t->json('meta')->nullable(); // browser UA, location hints, etc
            $t->timestamps();
            $t->index(['org_id', 'site_id', 'type']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('devices');
    }
};

