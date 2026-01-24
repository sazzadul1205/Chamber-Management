<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items');
            $table->integer('opening_stock')->default(0);
            $table->integer('current_stock')->default(0);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->date('last_purchase_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('location', 50)->nullable();
            $table->datetime('last_updated');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();

            // Indexes
            $table->unique('item_id');
            $table->index('current_stock');
            $table->index('expiry_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_stock');
    }
};
