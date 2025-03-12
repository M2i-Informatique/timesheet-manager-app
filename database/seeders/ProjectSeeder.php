<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $project = [
            [
                'code' => 119018,
                'category' => 'go',
                'name' => 'Gardes Suisses',
                'address' => 'Place d\'armes',
                'city' => 'VERSAILLES',
                'distance' => 45.3,
                'status' => 'active',
            ],
            [
                'code' => 223014,
                'category' => 'mh',
                'name' => 'Le Stephenson',
                'address' => '1 rue Stephenson',
                'city' => 'MONTIGNY-LE-BRETONNEUX',
                'distance' => 45.9,
                'status' => 'active',
            ],
            [
                'code' => 124008,
                'category' => 'go',
                'name' => 'Rénovation du pigeonnier',
                'address' => 'Grande rue',
                'city' => 'MAROLLES EN HUREPOIX',
                'distance' => 2.9,
                'status' => 'active',
            ]
        ];

        foreach ($project as $project) {
            \App\Models\Project::create($project);
        }
    }
}
