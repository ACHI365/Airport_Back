<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Route;


class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Route::create([
            'start_stop_id' => 1,
            'end_stop_id' => 2,
            'distance_km' => 230,
            'price' => 20,
        ]);

        Route::create([
            'start_stop_id' => 2,
            'end_stop_id' => 1,
            'distance_km' => 230,
            'price' => 30,
        ]);
    }

}
