<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interims = [
            ['agency' => 'Manpower', 'hourly_rate' => 12.50, 'status' => 'active'],
            ['agency' => 'Adecco', 'hourly_rate' => 13.50, 'status' => 'active'],
        ];

        foreach ($interims as $interim) {
            \App\Models\Interim::create($interim);
        }
    }
}
