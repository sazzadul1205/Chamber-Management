<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // created, updated, deleted, viewed, logged_in, etc.
            $table->string('description');
            $table->string('subject_type')->nullable(); // Model class
            $table->unsignedBigInteger('subject_id')->nullable(); // Model ID
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->string('status')->default('success'); // success, failed, warning
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['subject_type', 'subject_id']);
            $table->index('user_id');
            $table->index('action');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};