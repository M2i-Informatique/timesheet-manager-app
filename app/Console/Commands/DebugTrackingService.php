<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Setting;
use App\CQRS\Queries\GetTrackingDataQuery;
use App\CQRS\QueryBus;

class DebugTrackingService extends Command
{
    protected $signature = 'debug:tracking';
    protected $description = 'Debug tracking service specifically';

    public function handle(): int
    {
        try {
            $this->info('1. Testing basic service resolution...');
            
            // Test du QueryBus
            $queryBus = app(\App\CQRS\QueryBus::class);
            $this->info('✓ QueryBus resolved');

            // Créer settings si nécessaire
            Setting::firstOrCreate(['key' => 'rate_charged'], ['value' => '70', 'start_date' => now()]);
            Setting::firstOrCreate(['key' => 'basket'], ['value' => '11', 'start_date' => now()]);
            $this->info('✓ Settings created');

            // Trouver ou créer un projet
            $project = Project::first();
            if (!$project) {
                $project = Project::create([
                    'code' => 999,
                    'name' => 'Debug Project',
                    'category' => 'mh',
                    'status' => 'active'
                ]);
            }
            $this->info("✓ Using project: {$project->code} - {$project->name}");

            // Test de la query
            $this->info('2. Testing GetTrackingDataQuery...');
            $query = new GetTrackingDataQuery($project->id, now()->month, now()->year, 'day');
            $this->info('✓ Query created');

            // Test du dispatch
            $this->info('3. Testing query dispatch...');
            $data = $queryBus->dispatch($query);
            $this->info('✓ Query dispatched successfully');

            // Vérifier les données
            $this->info('4. Checking returned data...');
            $this->info('✓ Data keys: ' . implode(', ', array_keys($data)));
            
            if (isset($data['availableWorkers'])) {
                $this->info("✓ Available workers: " . count($data['availableWorkers']));
            } else {
                $this->error("✗ availableWorkers key missing");
            }

            if (isset($data['availableInterims'])) {
                $this->info("✓ Available interims: " . count($data['availableInterims']));
            } else {
                $this->error("✗ availableInterims key missing");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            $this->error("File: {$e->getFile()}:{$e->getLine()}");
            $this->error("Stack trace:");
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}