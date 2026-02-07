<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('treatment_procedures', function (Blueprint $table) {
            // Make for_treatment_session_id nullable
            $table->decimal('paid_amount', 10, 2)
                ->default(0)
                ->after('cost');
        });
    }

    public function down()
    {
        Schema::table('treatment_procedures', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
        });
    }
};
