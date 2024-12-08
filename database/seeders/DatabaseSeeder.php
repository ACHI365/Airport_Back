<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\SeatAvailability;
use App\Models\Stop;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);
        Stop::create(['stop_name' => 'Kutaisi', 'latitude' => 42.267, 'longitude' => 42.717]);
        Stop::create(['stop_name' => 'Tbilisi', 'latitude' => 41.715, 'longitude' => 44.827]);

        Route::create(['start_stop_id' => 1, 'end_stop_id' => 2, 'distance_km' => 230, 'price' => 20]);
        Route::create(['start_stop_id' => 2, 'end_stop_id' => 1, 'distance_km' => 230, 'price' => 30]);
        Bus::create(['bus_name' => 'Bus 1', 'capacity' => 50]);

        Schedule::create([
            'bus_id' => 1,
            'route_id' => 1,
            'stop_id' => 1,
            'arrival_time' => '15:00:00',
            'departure_time' => '15:05:00'
        ]);

        SeatAvailability::create(['schedule_id' => 1, 'stop_id' => 1, 'available_seats' => 50]);
    }

}
