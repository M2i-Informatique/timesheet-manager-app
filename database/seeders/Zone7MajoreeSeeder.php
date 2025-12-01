<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Zone7MajoreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('zones')->insert([
            'name' => 'Zone 7 Majorée',
            'min_km' => 60.01,
            'max_km' => null,
            'rate' => 0.17,
            'is_per_km' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
