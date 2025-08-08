<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alert_settings', function (Blueprint $table) {
            $table->id();
            $table->float('pm25_threshold')->default(25);
            $table->float('pm10_threshold')->default(50);
            $table->float('co_threshold')->default(10);
            $table->float('no2_threshold')->default(40);
            $table->boolean('enable_alert')->default(true);
            $table->string('alert_message')->default('Stay indoors! Air is unhealthy.');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_settings');
    }
};
