<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Drop the 'seat_availability' table
        Schema::dropIfExists('seat_availability');

        // Remove the 'stop_id' column and its foreign key constraint from the 'schedules' table
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['stop_id']);
            $table->dropColumn('stop_id');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('seat_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->foreignId('stop_id')->constrained('stops')->onDelete('cascade');
            $table->integer('available_seats');
            $table->timestamps();
        });

        // Add the 'stop_id' column back to the 'schedules' table
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('stop_id')->nullable();
        });
    }
};
