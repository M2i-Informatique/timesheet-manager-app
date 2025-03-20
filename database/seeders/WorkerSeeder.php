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
            ['first_name' => 'Alcindo', 'last_name' => 'BARRETO', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3834.69, 'status' => 'active'],
            ['first_name' => 'Rui', 'last_name' => 'BEITO AMORIN', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2483.62, 'status' => 'active'],
            ['first_name' => 'Lucas', 'last_name' => 'BOUTONNET', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3698.9, 'status' => 'active'],
            ['first_name' => 'Gilson', 'last_name' => 'CABRAL ROBALO', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3143.4, 'status' => 'active'],
            ['first_name' => 'Jorge', 'last_name' => 'CARVALHO', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3017.16, 'status' => 'active'],
            ['first_name' => 'Ahmet', 'last_name' => 'CINGOZ', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2635.96, 'status' => 'active'],
            ['first_name' => 'Julien', 'last_name' => 'COCHAN', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3388.44, 'status' => 'active'],
            ['first_name' => 'Steeve', 'last_name' => 'COLLINS', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2813.16, 'status' => 'active'],
            ['first_name' => 'Faguimba', 'last_name' => 'COULIBALY', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2416.38, 'status' => 'active'],
            ['first_name' => 'Lamourou', 'last_name' => 'COULIBALY', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2488.87, 'status' => 'active'],
            ['first_name' => 'Dominique', 'last_name' => 'COUVEUR', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3017.16, 'status' => 'active'],
            ['first_name' => 'Chirstophe', 'last_name' => 'DA MOURA', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 1966.61, 'status' => 'active'],
            ['first_name' => 'Noberto', 'last_name' => 'DA SILVA', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3953.41, 'status' => 'active'],
            ['first_name' => 'José', 'last_name' => 'DA SILVA CAMPOS', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3327.78, 'status' => 'active'],
            ['first_name' => 'Joaquim', 'last_name' => 'DA SILVA SANTOS', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2633.85, 'status' => 'active'],
            ['first_name' => 'Paulo Jorge', 'last_name' => 'DA SILVA VAZ', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2712.73, 'status' => 'active'],
            ['first_name' => 'Manuel', 'last_name' => 'DE LIMA FERNANDES', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3681.3, 'status' => 'active'],
            ['first_name' => 'Rui', 'last_name' => 'DE OLIVEIRA MANCO', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3614.06, 'status' => 'active'],
            ['first_name' => 'Angelo', 'last_name' => 'DE PINA DIAS', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3008.92, 'status' => 'active'],
            ['first_name' => 'Jean Baptiste', 'last_name' => 'DELAVAUD', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2948.92, 'status' => 'active'],
            ['first_name' => 'Diakou', 'last_name' => 'DEMBELE', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 1966.61, 'status' => 'active'],
            ['first_name' => 'Mama', 'last_name' => 'DIARRA', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 1966.61, 'status' => 'active'],
            ['first_name' => 'Elias', 'last_name' => 'DOS SANTOS', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2985.54, 'status' => 'active'],
            ['first_name' => 'Alvarino', 'last_name' => 'DUARTE DA MOURA', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3122, 'status' => 'active'],
            ['first_name' => 'Nicolas', 'last_name' => 'ESNAULT', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3105.9, 'status' => 'active'],
            ['first_name' => 'Ludovic', 'last_name' => 'FERNANDES', 'category' => 'etam', 'contract_hours' => 37, 'monthly_salary' => 3436, 'status' => 'active'],
            ['first_name' => 'Christophe', 'last_name' => 'FERNANDES', 'category' => 'etam', 'contract_hours' => 37, 'monthly_salary' => 4234.97, 'status' => 'active'],
            ['first_name' => 'Antonio', 'last_name' => 'FERNANDES', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 4351.59, 'status' => 'active'],
            ['first_name' => 'Joao', 'last_name' => 'FLORA ALVES', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3739.09, 'status' => 'active'],
            ['first_name' => 'Christophe', 'last_name' => 'GERMOND', 'category' => 'etam', 'contract_hours' => 37, 'monthly_salary' => 3354.57, 'status' => 'active'],
            ['first_name' => 'Domingos', 'last_name' => 'GOMES', 'category' => 'etam', 'contract_hours' => 37, 'monthly_salary' => 5739.43, 'status' => 'active'],
            ['first_name' => 'José Manuel', 'last_name' => 'GOMES PACHECO', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3507.94, 'status' => 'active'],
            ['first_name' => 'Jorge', 'last_name' => 'GONCALVES', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3463.92, 'status' => 'active'],
            ['first_name' => 'Guillaume', 'last_name' => 'GOURDON', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3478.54, 'status' => 'active'],
            ['first_name' => 'Férit', 'last_name' => 'KAPUSUK', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2800.9, 'status' => 'active'],
            ['first_name' => 'Sergio', 'last_name' => 'LOPES', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2346, 'status' => 'active'],
            ['first_name' => 'Paulo', 'last_name' => 'LOURENCO', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2877.82, 'status' => 'active'],
            ['first_name' => 'Cheikhou', 'last_name' => 'MAGASSA', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2416.38, 'status' => 'active'],
            ['first_name' => 'Carlos', 'last_name' => 'MALHEIRO', 'category' => 'etam', 'contract_hours' => 37, 'monthly_salary' => 3767, 'status' => 'active'],
            ['first_name' => 'Tacettin', 'last_name' => 'MARASLIOGLU', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3370.28, 'status' => 'active'],
            ['first_name' => 'Cyril', 'last_name' => 'MARTINEZ', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3060, 'status' => 'active'],
            ['first_name' => 'Joao', 'last_name' => 'MENDES DE BRITO', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2684, 'status' => 'active'],
            ['first_name' => 'Stéphane', 'last_name' => 'MICHAUT', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2451.42, 'status' => 'active'],
            ['first_name' => 'Théo', 'last_name' => 'NOEL', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 1397.79, 'status' => 'active'],
            ['first_name' => 'Rayyan', 'last_name' => 'OUERFELLI', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 936.04, 'status' => 'active'],
            ['first_name' => 'Ahmet', 'last_name' => 'OZDAMAR', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2696, 'status' => 'active'],
            ['first_name' => 'Joao', 'last_name' => 'PASSEIRA', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3187.5, 'status' => 'active'],
            ['first_name' => 'Antonino', 'last_name' => 'PINTO DA SILVA', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3320.43, 'status' => 'active'],
            ['first_name' => 'Alexandre', 'last_name' => 'POUPRY', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3001.96, 'status' => 'active'],
            ['first_name' => 'David', 'last_name' => 'RODRIGUES', 'category' => 'etam', 'contract_hours' => 37, 'monthly_salary' => 3838.24, 'status' => 'active'],
            ['first_name' => 'Noé', 'last_name' => 'RODRIGUES', 'category' => 'etam', 'contract_hours' => 37, 'monthly_salary' => 3838.24, 'status' => 'active'],
            ['first_name' => 'Eusébio', 'last_name' => 'SEMEDO MOREIRA', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2304.05, 'status' => 'active'],
            ['first_name' => 'Félisberto', 'last_name' => 'SEMEDO CABRAL', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3142, 'status' => 'active'],
            ['first_name' => 'Damien', 'last_name' => 'SEVESTRE', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2399.76, 'status' => 'active'],
            ['first_name' => 'Drissa', 'last_name' => 'SIDIBE', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3033.61, 'status' => 'active'],
            ['first_name' => 'Luis', 'last_name' => 'SIMOES', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3543.2, 'status' => 'active'],
            ['first_name' => 'Abdoulaye', 'last_name' => 'TOURE', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2425.56, 'status' => 'active'],
            ['first_name' => 'Diadie', 'last_name' => 'TRAORE', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 1984.58, 'status' => 'active'],
            ['first_name' => 'Murat', 'last_name' => 'ULUSAN', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2536.74, 'status' => 'active'],
            ['first_name' => 'Isa', 'last_name' => 'UYSAL', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3379.79, 'status' => 'active'],
            ['first_name' => 'Mustafa', 'last_name' => 'UYSAL', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 3274.2, 'status' => 'active'],
            ['first_name' => 'Aytekin', 'last_name' => 'ZENGIN', 'category' => 'worker', 'contract_hours' => 37, 'monthly_salary' => 2855.53, 'status' => 'active'],
        ];

        foreach ($workers as $worker) {
            \App\Models\Worker::create($worker);
        }
    }
}
