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
use Illuminate\Support\Facades\DB;

class StopsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        DB::table('stops')->insert([
            ['stop_name' => 'Zestafoni', 'latitude' => 42.11, 'longitude' => 43.05],
            ['stop_name' => 'Khashuri', 'latitude' => 41.99, 'longitude' => 43.60],
        ]);
    }

}