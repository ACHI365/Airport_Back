<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('stop_id')->constrained('stops')->onDelete('cascade');
            $table->time('arrival_time');
            $table->time('departure_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
