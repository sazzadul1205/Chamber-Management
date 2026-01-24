<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_no', 20)->unique();
            $table->foreignId('invoice_id')->constrained('invoices');
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('installment_id')->nullable()->constrained('payment_installments');
            $table->boolean('is_advance')->default(false);
            $table->foreignId('for_treatment_session_id')->nullable()->constrained('treatment_sessions');
            $table->datetime('payment_date');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'cheque', 'mobile_banking', 'other'])->default('cash');
            $table->enum('payment_type', ['full', 'partial', 'advance', 'refund'])->default('partial');
            $table->decimal('amount', 10, 2);
            $table->string('reference_no', 50)->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('completed');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');

            // Indexes
            $table->index(['invoice_id', 'payment_date']);
            $table->index(['patient_id', 'payment_date']);
            $table->index('payment_method');
            $table->index('status');
            $table->index('payment_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
