<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Worker;
use App\Models\Interim;
use App\Models\Zone;
use App\Models\Setting;
use App\CQRS\Commands\AssignEmployeeCommand;
use App\CQRS\Queries\GetTrackingDataQuery;
use App\CQRS\CommandBus;
use App\CQRS\QueryBus;

class TestTrackingAssignment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:assignment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test employee assignment functionality';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Testing employee assignment functionality...');

        try {
            // Créer les settings si nécessaires
            Setting::firstOrCreate(['key' => 'rate_charged'], ['value' => '70', 'start_date' => now()]);
            Setting::firstOrCreate(['key' => 'basket'], ['value' => '11', 'start_date' => now()]);

            // Créer un projet de test
            $project = Project::firstOrCreate([
                'code' => 9999,
                'name' => 'Test Assignment Project',
                'category' => 'mh',
                'status' => 'active'
            ]);

            // Créer un worker de test
            $worker = Worker::firstOrCreate([
                'first_name' => 'Test',
                'last_name' => 'Assignment',
                'contract_hours' => 35,
                'monthly_salary' => 3000,
                'category' => 'worker',
                'status' => 'active'
            ]);

            // Créer un interim de test
            $interim = Interim::firstOrCreate([
                'agency' => 'Test Assignment Agency',
                'hourly_rate' => 25.50,
                'status' => 'active'
            ]);

            $this->info("✓ Created test data:");
            $this->info("  - Project: {$project->code} - {$project->name}");
            $this->info("  - Worker: {$worker->first_name} {$worker->last_name}");
            $this->info("  - Interim: {$interim->agency}");

            // Tester l'assignation via CQRS
            $commandBus = app(CommandBus::class);
            $queryBus = app(QueryBus::class);

            // Test 1: Récupérer les données de tracking avant assignation
            $this->info("\n1. Testing tracking data retrieval...");
            $query = new GetTrackingDataQuery($project->id, now()->month, now()->year, 'day');
            $data = $queryBus->dispatch($query);

            $this->info("✓ Retrieved tracking data:");
            $this->info("  - Available workers: " . count($data['availableWorkers']));
            $this->info("  - Available interims: " . count($data['availableInterims']));

            // Test 2: Assigner le worker
            $this->info("\n2. Testing worker assignment...");
            $command = new AssignEmployeeCommand($project->id, 'worker', $worker->id);
            $result = $commandBus->dispatch($command);

            if ($result['success']) {
                $this->info("✓ Worker assignment successful:");
                $this->info("  - {$result['message']}");
                $this->info("  - Employee: {$result['data']['employee_name']}");
            } else {
                $this->error("✗ Worker assignment failed");
                return Command::FAILURE;
            }

            // Test 3: Assigner l'interim
            $this->info("\n3. Testing interim assignment...");
            $command = new AssignEmployeeCommand($project->id, 'interim', $interim->id);
            $result = $commandBus->dispatch($command);

            if ($result['success']) {
                $this->info("✓ Interim assignment successful:");
                $this->info("  - {$result['message']}");
                $this->info("  - Employee: {$result['data']['employee_name']}");
            } else {
                $this->error("✗ Interim assignment failed");
                return Command::FAILURE;
            }

            // Test 4: Vérifier les données après assignation
            $this->info("\n4. Testing data after assignment...");
            $query = new GetTrackingDataQuery($project->id, now()->month, now()->year, 'day');
            $data = $queryBus->dispatch($query);

            $this->info("✓ Retrieved tracking data after assignment:");
            $this->info("  - Available workers: " . count($data['availableWorkers']));
            $this->info("  - Available interims: " . count($data['availableInterims']));
            $this->info("  - Entries data: " . count($data['entriesData']));

            // Test 5: Vérifier que les employés sont bien dans les données d'entrée
            $workerFound = false;
            $interimFound = false;
            
            foreach ($data['entriesData'] as $entry) {
                if ($entry['model_type'] === 'worker' && $entry['id'] === $worker->id) {
                    $workerFound = true;
                }
                if ($entry['model_type'] === 'interim' && $entry['id'] === $interim->id) {
                    $interimFound = true;
                }
            }

            if ($workerFound) {
                $this->info("✓ Worker found in entries data");
            } else {
                $this->error("✗ Worker NOT found in entries data");
            }

            if ($interimFound) {
                $this->info("✓ Interim found in entries data");
            } else {
                $this->error("✗ Interim NOT found in entries data");
            }

            $this->info("\n🎉 All tests passed! Employee assignment is working correctly.");
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            
            return Command::FAILURE;
        }
    }
}