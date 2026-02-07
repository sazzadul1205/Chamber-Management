<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_code', 20)->unique();
            $table->foreignId('treatment_id')->constrained('treatments')->onDelete('cascade');
            $table->date('prescription_date');
            $table->integer('validity_days')->default(7);
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled', 'filled'])->default('active');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index('prescription_code');
            $table->index('treatment_id');
            $table->index('prescription_date');
            $table->index('status');
        });

        // Insert sample prescription
        DB::table('prescriptions')->insert([
            [
                'prescription_code' => 'RX001',
                'treatment_id' => 1,
                'prescription_date' => now()->format('Y-m-d'),
                'validity_days' => 7,
                'notes' => 'Take after food. Complete full course.',
                'status' => 'active',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('prescriptions');
    }
};
