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
        Schema::create('lead_task_reminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_task_id'); // Foreign key to lead_tasks
            $table->string('reminder_type')->nullable(); // Email, SMS, etc.
            $table->string('recipient_type')->nullable(); // Agent, Customer, etc.
            $table->unsignedBigInteger('recipient_id')->nullable(); // Optional: agent/user/customer id
            $table->integer('minutes_before')->nullable(); // Minutes before the task
            $table->boolean('sent')->default(false); // Whether the reminder has been sent
            $table->timestamps();

            $table->foreign('lead_task_id')->references('id')->on('lead_tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_task_reminders');
    }
};
