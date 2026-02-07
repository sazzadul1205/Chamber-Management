<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->integer('installment_number');
            $table->string('description', 255)->nullable();
            $table->date('due_date');
            $table->decimal('amount_due', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->boolean('late_fee_applied')->default(false);
            $table->decimal('late_fee_amount', 10, 2)->default(0);
            $table->date('reminder_sent_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');

            // Indexes
            $table->index(['invoice_id', 'installment_number']);
            $table->index('due_date');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_installments');
    }
};
