<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dental_chairs', function (Blueprint $table) {
            $table->id(); // id int [pk, increment]
            $table->string('name'); // name varchar
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available'); // status enum
            $table->timestamps();

            // Index for better performance
            $table->index('status');
        });

        // Insert default chairs
        $this->seedDefaultChairs();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dental_chairs');
    }

    /**
     * Seed default dental chairs.
     */
    private function seedDefaultChairs(): void
    {
        $chairs = [
            ['name' => 'Chair 1', 'status' => 'available'],
            ['name' => 'Chair 2', 'status' => 'available'],
            ['name' => 'Chair 3', 'status' => 'available'],
            ['name' => 'Chair 4', 'status' => 'available'],
        ];

        foreach ($chairs as $chair) {
            DB::table('dental_chairs')->insert($chair);
        }
    }
};
