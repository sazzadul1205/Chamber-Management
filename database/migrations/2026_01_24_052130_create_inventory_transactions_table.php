<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 20)->unique();
            $table->foreignId('item_id')->constrained('inventory_items');
            $table->enum('transaction_type', ['purchase', 'adjustment', 'consumption', 'return', 'transfer_in', 'transfer_out']);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->foreignId('reference_id')->nullable()->comment('ID of related record (purchase, adjustment, etc.)');
            $table->string('reference_type', 50)->nullable()->comment('Type of related record');
            $table->string('reference_no', 100)->nullable()->comment('External reference number');
            $table->text('notes')->nullable();
            $table->datetime('transaction_date');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index(['item_id', 'transaction_date']);
            $table->index('transaction_type');
            $table->index(['reference_id', 'reference_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
