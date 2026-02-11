<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('page_configs', function (Blueprint $table) {
            $table->id();
            $table->string('page_name')->default('home');
            $table->json('layout_config')->nullable();
            $table->json('section_settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('page_configs');
    }
};
