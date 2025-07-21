<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Worker;
use App\Models\Zone;
use App\Models\Setting;
use App\Models\TimeSheetable;
use App\Services\Costs\CostsCalculator;
use App\Services\TrackingService;
use App\Services\WorkerSalaryService;
use App\ValueObjects\HourlyRate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MigrationSafetyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test critique : Vérifier que WorkerSalaryService produit les mêmes résultats
     * que les accesseurs actuels du modèle Worker
     */
    public function test_worker_salary_service_matches_model_accessors()
    {
        // Setup
        Setting::create(['key' => 'rate_charged', 'value' => '70']);
        
        $worker = Worker::create([
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        // Méthode actuelle (modèle)
        $oldHourlyRate = $worker->hourly_rate;
        $oldChargedRate = $worker->hourly_rate_charged;

        // Nouvelle méthode (service)
        $salaryService = new WorkerSalaryService(new \App\Services\Config\BusinessConfigService());
        $newHourlyRate = $salaryService->calculateHourlyRate($worker);
        $newChargedRate = $salaryService->calculateChargedRateFromSettings($worker);

        // Vérification stricte
        $this->assertEqualsWithDelta($oldHourlyRate, $newHourlyRate, 0.001);
        $this->assertEqualsWithDelta($oldChargedRate, $newChargedRate, 0.001);
    }

    /**
     * Test critique : Vérifier que HourlyRate Value Object 
     * produit les mêmes résultats que les calculs actuels
     */
    public function test_hourly_rate_value_object_matches_current_logic()
    {
        Setting::create(['key' => 'rate_charged', 'value' => '70']);
        
        $worker = Worker::create([
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        // Calcul actuel
        $currentBase = $worker->hourly_rate;
        $currentCharged = $worker->hourly_rate_charged;

        // Nouveau Value Object
        $hourlyRate = HourlyRate::fromWorker($worker, 70);

        $this->assertNotNull($hourlyRate);
        $this->assertEqualsWithDelta($currentBase, $hourlyRate->base, 0.001);
        $this->assertEqualsWithDelta($currentCharged, $hourlyRate->charged, 0.001);
        $this->assertEquals(70, $hourlyRate->chargeRate);
    }

    /**
     * Test critique : Vérifier que TrackingService produit les mêmes données
     * que la logique actuelle du contrôleur
     */
    public function test_tracking_service_matches_controller_logic()
    {
        // Setup complet
        Setting::create(['key' => 'rate_charged', 'value' => '70']);
        Setting::create(['key' => 'basket', 'value' => '11']);
        
        $zone = Zone::create(['name' => 'Zone 1', 'rate' => 10]);
        $project = Project::create([
            'code' => 1001,
            'name' => 'Test Project',
            'category' => 'mh',
            'status' => 'active',
            'zone_id' => $zone->id
        ]);
        
        $worker = Worker::create([
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);
        
        $project->workers()->attach($worker->id);
        
        // Créer quelques timesheets
        TimeSheetable::create([
            'project_id' => $project->id,
            'timesheetable_id' => $worker->id,
            'timesheetable_type' => Worker::class,
            'date' => '2025-01-01',
            'category' => 'day',
            'hours' => 8
        ]);

        // Simuler la logique actuelle du contrôleur
        $currentLogicData = $this->simulateCurrentControllerLogic($project, 1, 2025);
        
        // Nouvelle logique via service
        $trackingService = new TrackingService(
            app(\App\Repositories\ProjectRepositoryInterface::class),
            app(\App\Services\Costs\CostCalculatorInterface::class)
        );
        
        $newServiceData = $trackingService->getTrackingData([
            'project_id' => $project->id,
            'month' => 1,
            'year' => 2025,
            'category' => 'day'
        ]);

        // Comparaisons critiques
        $this->assertEquals(
            $currentLogicData['totalHoursCurrentMonth'],
            $newServiceData['totalHoursCurrentMonth'],
            'Total heures ne correspond pas'
        );
        
        $this->assertEqualsWithDelta(
            $currentLogicData['costWorkerTotal'],
            $newServiceData['costWorkerTotal'],
            0.01,
            'Coût total ne correspond pas'
        );
        
        $this->assertCount(
            count($currentLogicData['entriesData']),
            $newServiceData['entriesData'],
            'Nombre d\'entrées ne correspond pas'
        );
    }

    /**
     * Simuler la logique actuelle du contrôleur (version simplifiée)
     */
    private function simulateCurrentControllerLogic($project, $month, $year)
    {
        // Reproduire la logique actuelle ligne par ligne
        $workers = $project->workers()->where('status', 'active')->get();
        
        $totalWorkerHours = 0;
        foreach ($workers as $w) {
            $daySum = TimeSheetable::where('project_id', $project->id)
                ->where('timesheetable_id', $w->id)
                ->where('timesheetable_type', Worker::class)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('category', 'day')
                ->sum('hours');
                
            $totalWorkerHours += $daySum;
        }
        
        $calculator = app(\App\Services\Costs\CostsCalculator::class);
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = \Carbon\Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');
        $costData = $calculator->calculateTotalCostForProject($project, $startDate, $endDate);
        
        return [
            'totalHoursCurrentMonth' => $totalWorkerHours,
            'costWorkerTotal' => $costData['cost'],
            'entriesData' => $workers->map(function($w) {
                return [
                    'id' => $w->id,
                    'model_type' => 'worker',
                    'full_name' => $w->first_name . ' ' . $w->last_name
                ];
            })->toArray()
        ];
    }

    /**
     * Test de performance : vérifier que les nouveaux services
     * ne sont pas plus lents que l'ancien code
     */
    public function test_performance_regression()
    {
        // Setup avec beaucoup de données
        Setting::create(['key' => 'rate_charged', 'value' => '70']);
        Setting::create(['key' => 'basket', 'value' => '11']);
        
        $project = Project::create([
            'code' => 1001,
            'name' => 'Big Project',
            'category' => 'mh',
            'status' => 'active'
        ]);
        
        // Créer 50 workers avec timesheets
        for ($i = 0; $i < 50; $i++) {
            $worker = Worker::create([
                'first_name' => "Worker$i",
                'last_name' => "Test$i",
                'contract_hours' => 35,
                'monthly_salary' => 3000,
                'category' => 'worker',
                'status' => 'active'
            ]);
            
            $project->workers()->attach($worker->id);
            
            // 10 timesheets par worker
            for ($day = 1; $day <= 10; $day++) {
                TimeSheetable::create([
                    'project_id' => $project->id,
                    'timesheetable_id' => $worker->id,
                    'timesheetable_type' => Worker::class,
                    'date' => "2025-01-$day",
                    'category' => 'day',
                    'hours' => 8
                ]);
            }
        }
        
        // Mesurer performance ancien code
        $start = microtime(true);
        $oldData = $this->simulateCurrentControllerLogic($project, 1, 2025);
        $oldTime = microtime(true) - $start;
        
        // Mesurer performance nouveau code
        $trackingService = new TrackingService(
            app(\App\Repositories\ProjectRepositoryInterface::class),
            app(\App\Services\Costs\CostCalculatorInterface::class)
        );
        
        $start = microtime(true);
        $newData = $trackingService->getTrackingData([
            'project_id' => $project->id,
            'month' => 1,
            'year' => 2025,
            'category' => 'day'
        ]);
        $newTime = microtime(true) - $start;
        
        // Le nouveau code ne doit pas être plus de 20% plus lent
        $this->assertLessThan(
            $oldTime * 1.2,
            $newTime,
            "Nouveau code trop lent: {$newTime}s vs {$oldTime}s"
        );
        
        // Les résultats doivent être identiques
        $this->assertEquals($oldData['totalHoursCurrentMonth'], $newData['totalHoursCurrentMonth']);
    }
}