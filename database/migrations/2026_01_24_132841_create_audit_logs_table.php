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
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_role', 50)->nullable();
            $table->string('action', 100)->notNull();
            $table->string('table_name', 50)->notNull();
            $table->unsignedBigInteger('record_id')->notNull();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('action_time')->useCurrent();
            $table->text('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->text('description')->nullable();

            // Indexes for performance
            $table->index(['table_name', 'record_id']);
            $table->index(['user_id', 'action_time']);
            $table->index('action');
            $table->index('action_time');
            $table->index('user_role');

            // Fulltext index for search (if using MySQL)
            if (config('database.default') === 'mysql') {
                $table->fulltext(['action', 'table_name', 'description']);
            }
        });

        // Create separate table for audit log metadata if needed
        Schema::create('audit_log_metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_log_id')->constrained('audit_logs')->onDelete('cascade');
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->timestamps();

            $table->index(['audit_log_id', 'key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_log_metadata');
        Schema::dropIfExists('audit_logs');
    }
};
