<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no', 20)->unique();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->timestamp('receipt_date')->useCurrent();
            $table->string('amount_words', 255);
            $table->timestamp('printed_at')->nullable();
            $table->foreignId('printed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Indexes for performance
            $table->index('receipt_no');
            $table->index(['receipt_date', 'patient_id']);
            $table->index('printed_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('receipts');
    }
};
