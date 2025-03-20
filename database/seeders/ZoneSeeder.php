<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            ['name' => 'Zone 1b', 'min_km' => 0, 'max_km' => 0, 'rate' => 1.75],
            ['name' => 'Zone 2', 'min_km' => 0.1, 'max_km' => 10, 'rate' => 2.5],
            ['name' => 'Zone 3', 'min_km' => 10.1, 'max_km' => 20, 'rate' => 3.75],
            ['name' => 'Zone 4', 'min_km' => 20.1, 'max_km' => 30, 'rate' => 4.5],
            ['name' => 'Zone 5', 'min_km' => 30.1, 'max_km' => 40, 'rate' => 5.5],
            ['name' => 'Zone 6', 'min_km' => 40.1, 'max_km' => 50, 'rate' => 7.3],
            ['name' => 'Zone 7', 'min_km' => 50.1, 'max_km' => null, 'rate' => 8.3],
        ];

        foreach ($zones as $zone) {
            \App\Models\Zone::create($zone);
        }
    }
}
