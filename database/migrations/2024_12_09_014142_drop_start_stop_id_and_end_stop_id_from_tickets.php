<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropStartStopIdAndEndStopIdFromTickets extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['start_stop_id']);
            $table->dropForeign(['end_stop_id']);

            // Drop the columns
            $table->dropColumn(['start_stop_id', 'end_stop_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Add the columns back
            $table->foreignId('start_stop_id')->constrained('stops')->onDelete('cascade');
            $table->foreignId('end_stop_id')->constrained('stops')->onDelete('cascade');
        });
    }
}
