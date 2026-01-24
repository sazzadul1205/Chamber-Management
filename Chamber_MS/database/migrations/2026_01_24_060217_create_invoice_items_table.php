<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->enum('item_type', ['procedure', 'medicine', 'inventory', 'other']);
            $table->foreignId('item_id')->nullable()->comment('ID of procedure, medicine, or inventory item');
            $table->string('description', 255);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();

            // Indexes
            $table->index(['invoice_id', 'item_type']);
            $table->index('item_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
};
