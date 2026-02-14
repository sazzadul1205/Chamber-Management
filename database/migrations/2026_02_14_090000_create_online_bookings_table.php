<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 120);
            $table->string('email', 120);
            $table->string('phone', 30);
            $table->date('preferred_date');
            $table->string('preferred_time', 30);
            $table->string('service', 120);
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'converted'])->default('pending');
            $table->string('source', 40)->default('website');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('preferred_date');
            $table->index('status');
            $table->index('email');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_bookings');
    }
};
