<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchedulesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('schedules')->insert([
            [
                'bus_id' => 1,
                'route_id' => 1,
                'stop_id' => 1, // Kutaisi
                'arrival_time' => '15:00:00',
                'departure_time' => '15:05:00',
                'date' => '2024-11-30', // Example date
            ],
            [
                'bus_id' => 1,
                'route_id' => 1,
                'stop_id' => 3, // Zestafoni
                'arrival_time' => '15:30:00',
                'departure_time' => '15:35:00',
                'date' => '2024-11-30',
            ],
            [
                'bus_id' => 1,
                'route_id' => 1,
                'stop_id' => 4, // Khashuri
                'arrival_time' => '15:50:00',
                'departure_time' => '15:55:00',
                'date' => '2024-11-30',
            ],
            [
                'bus_id' => 1,
                'route_id' => 1,
                'stop_id' => 2, // Tbilisi
                'arrival_time' => '16:00:00',
                'departure_time' => '16:05:00',
                'date' => '2024-11-30',
            ],
        ]);
    }
}
