<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('buses')->insert([
            [
                'bus_name' => 'City Express', // Correct column name
                'capacity' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bus_name' => 'Mountain Rider', // Correct column name
                'capacity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
