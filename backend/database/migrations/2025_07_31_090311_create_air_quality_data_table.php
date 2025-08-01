<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('air_quality_data', function (Blueprint $table) {
            $table->id();
            $table->string('location_id')->index();
            $table->string('location');
            $table->string('city')->nullable();
            $table->string('country');
            $table->string('parameter');
            $table->decimal('value', 10, 2);
            $table->string('unit');
            $table->timestamp('last_updated');
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('air_quality_data');
    }
};