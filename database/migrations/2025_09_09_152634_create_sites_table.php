<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sites', function (Blueprint $t) {
            $t->id();
            $t->foreignId('org_id')->constrained('organizations')->cascadeOnDelete();
            $t->string('name');
            $t->string('code')->nullable(); // internal code
            $t->string('timezone')->default('UTC');
            $t->json('address')->nullable(); // {line1, city, state, ...}
            $t->json('settings')->nullable();
            $t->timestamps();
            $t->index(['org_id', 'name']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('sites');
    }
};

