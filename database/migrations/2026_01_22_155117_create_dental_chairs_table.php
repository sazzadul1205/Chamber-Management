<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dental_chairs', function (Blueprint $table) {
            $table->id();
            $table->string('chair_code', 20)->unique();
            $table->string('name', 50);
            $table->string('location', 100)->nullable();
            $table->enum('status', [
                'available',
                'occupied',
                'maintenance',
                'cleaning',
                'out_of_service'
            ])->default('available');
            $table->datetime('last_used')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Insert default dental chairs
        DB::table('dental_chairs')->insert([
            [
                'chair_code' => 'CHAIR-01',
                'name' => 'Chair 1',
                'location' => 'Room A - Left side',
                'status' => 'available',
                'last_used' => null,
                'notes' => 'Primary chair, newly serviced',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'chair_code' => 'CHAIR-02',
                'name' => 'Chair 2',
                'location' => 'Room A - Right side',
                'status' => 'available',
                'last_used' => null,
                'notes' => 'Secondary chair',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'chair_code' => 'CHAIR-03',
                'name' => 'Chair 3',
                'location' => 'Room B',
                'status' => 'available',
                'last_used' => null,
                'notes' => 'Pediatric chair',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'chair_code' => 'CHAIR-04',
                'name' => 'Chair 4',
                'location' => 'Room C',
                'status' => 'available',
                'last_used' => null,
                'notes' => 'Orthodontic chair',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'chair_code' => 'CHAIR-05',
                'name' => 'Chair 5',
                'location' => 'Room D',
                'status' => 'maintenance',
                'last_used' => now()->subDays(2),
                'notes' => 'Under maintenance - hydraulic issue',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'chair_code' => 'CHAIR-06',
                'name' => 'Chair 6',
                'location' => 'Emergency Room',
                'status' => 'available',
                'last_used' => null,
                'notes' => 'Emergency/extra chair',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('dental_chairs');
    }
};
