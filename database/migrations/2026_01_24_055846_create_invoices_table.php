<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 20)->unique();
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('treatment_id')->nullable()->constrained('treatments');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments');
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->enum('payment_plan', ['full', 'installment'])->default('full');
            $table->decimal('advance_amount', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->virtualAs('total_amount - paid_amount');
            $table->string('payment_terms', 100)->nullable();
            $table->enum('status', ['draft', 'sent', 'partial', 'paid', 'cancelled', 'overdue'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            // Indexes
            $table->index(['patient_id', 'invoice_date']);
            $table->index('status');
            $table->index('invoice_date');
            $table->index('due_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
