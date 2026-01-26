<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('installment_id')->nullable()->constrained('payment_installments')->onDelete('set null');
            $table->foreignId('treatment_session_id')->nullable()->constrained('treatment_sessions')->onDelete('set null');
            $table->decimal('allocated_amount', 10, 2);
            $table->timestamp('allocation_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Indexes for performance
            $table->index(['payment_id', 'installment_id']);
            $table->index(['payment_id', 'treatment_session_id']);
            $table->index('allocation_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_allocations');
    }
};
