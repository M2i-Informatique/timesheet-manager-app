<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workers = [
            ['first_name' => 'Mounir', 'last_name' => 'AYEB', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2812.46, 'status' => 'active'],
            ['first_name' => 'Christophe', 'last_name' => 'FERNANDES', 'category' => 'etam', 'contract_hours' => 37, 'monthly_salary' => 4234.97, 'status' => 'active']
        ];

        foreach ($workers as $worker) {
            \App\Models\Worker::create($worker);
        }
    }
}
