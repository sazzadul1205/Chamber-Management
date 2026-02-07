<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->string('reminder_type'); // sms, email, push, in_app
            $table->text('message');
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->timestamp('scheduled_at');
            $table->timestamp('sent_at')->nullable();
            $table->json('meta')->nullable(); // for storing response data, errors, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_reminders');
    }
};
