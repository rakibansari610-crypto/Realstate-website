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
       Schema::create('lead_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->string('task_type')->nullable(); // Notes, Viewing, Call, etc.
            $table->text('note')->nullable();
            $table->string('unit_ref')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->integer('schedule_follow_up_days')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('lead_activity')->nullable(); 
            $table->string('lead_status')->nullable();
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_tasks');
    }
};
