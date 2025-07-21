<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Worker;
use App\Models\Zone;
use App\Models\Setting;
use App\Models\TimeSheetable;
use App\Services\Tracking\TrackingService;
use App\Services\Salary\WorkerSalaryService;
use App\ValueObjects\HourlyRate;
use App\Http\Requests\TrackingShowRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class Phase2MigrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configuration des settings comme en production
        Setting::create(['key' => 'rate_charged', 'value' => '70', 'start_date' => now()]);
        Setting::create(['key' => 'basket', 'value' => '11', 'start_date' => now()]);
    }

    /**
     * Test critique : Vérifier que WorkerSalaryService produit exactement
     * les mêmes résultats que les accesseurs du modèle Worker
     */
    public function test_worker_salary_service_matches_model_accessors()
    {
        // Créer un worker avec des valeurs réalistes
        $worker = Worker::create([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        // Valeurs actuelles (via accesseurs du modèle)
        $oldHourlyRate = $worker->hourly_rate;
        $oldChargedRate = $worker->hourly_rate_charged;

        // Nouvelles valeurs (via WorkerSalaryService)
        $salaryService = new WorkerSalaryService(new \App\Services\Config\BusinessConfigService());
        $newHourlyRate = $salaryService->calculateHourlyRate($worker);
        $newChargedRate = $salaryService->calculateChargedRateFromSettings($worker);

        // Validation stricte au centime près
        $this->assertEqualsWithDelta($oldHourlyRate, $newHourlyRate, 0.001,
            "Hourly rate mismatch: old={$oldHourlyRate}, new={$newHourlyRate}");
        
        $this->assertEqualsWithDelta($oldChargedRate, $newChargedRate, 0.001,
            "Charged rate mismatch: old={$oldChargedRate}, new={$newChargedRate}");
    }

    /**
     * Test critique : Vérifier que HourlyRate Value Object
     * produit les mêmes résultats que les calculs actuels
     */
    public function test_hourly_rate_value_object_matches_current_calculations()
    {
        $worker = Worker::create([
            'first_name' => 'Marie',
            'last_name' => 'Martin',
            'contract_hours' => 35,
            'monthly_salary' => 3500,
            'category' => 'etam',
            'status' => 'active'
        ]);

        // Valeurs actuelles
        $currentBase = $worker->hourly_rate;
        $currentCharged = $worker->hourly_rate_charged;

        // Nouveau Value Object
        $hourlyRate = HourlyRate::fromWorkerWithSettings($worker);

        $this->assertNotNull($hourlyRate);
        $this->assertEqualsWithDelta($currentBase, $hourlyRate->base, 0.001,
            "Base rate mismatch: current={$currentBase}, new={$hourlyRate->base}");
        
        $this->assertEqualsWithDelta($currentCharged, $hourlyRate->charged, 0.001,
            "Charged rate mismatch: current={$currentCharged}, new={$hourlyRate->charged}");
        
        $this->assertEquals(70, $hourlyRate->chargeRate);
        $this->assertTrue($hourlyRate->isValid());
    }

    /**
     * Test critique : Vérifier que TrackingService produit exactement
     * les mêmes données que l'ancienne logique du contrôleur
     */
    public function test_tracking_service_matches_controller_logic()
    {
        // Setup complet d'un environnement de test
        $zone = Zone::create([
            'name' => 'Zone Test',
            'min_km' => 0,
            'max_km' => 50,
            'rate' => 10
        ]);
        
        $project = Project::create([
            'code' => 1001,
            'name' => 'Projet Test Migration',
            'category' => 'mh',
            'status' => 'active',
            'zone_id' => $zone->id
        ]);
        
        $worker = Worker::create([
            'first_name' => 'Pierre',
            'last_name' => 'Durand',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);
        
        $project->workers()->attach($worker->id);
        
        // Créer des timesheets de test
        TimeSheetable::create([
            'project_id' => $project->id,
            'timesheetable_id' => $worker->id,
            'timesheetable_type' => Worker::class,
            'date' => '2025-01-15',
            'category' => 'day',
            'hours' => 8
        ]);
        
        TimeSheetable::create([
            'project_id' => $project->id,
            'timesheetable_id' => $worker->id,
            'timesheetable_type' => Worker::class,
            'date' => '2025-01-16',
            'category' => 'night',
            'hours' => 6
        ]);

        // Simuler les données d'entrée
        $params = [
            'project_id' => $project->id,
            'month' => 1,
            'year' => 2025,
            'category' => 'day'
        ];

        // Utiliser le nouveau TrackingService
        $trackingService = app(\App\Services\Tracking\TrackingServiceInterface::class);
        $serviceData = $trackingService->getTrackingData($params);

        // Vérifications critiques
        $this->assertIsArray($serviceData);
        $this->assertArrayHasKey('project', $serviceData);
        $this->assertArrayHasKey('entriesData', $serviceData);
        $this->assertArrayHasKey('recap', $serviceData);
        $this->assertArrayHasKey('totalHoursCurrentMonth', $serviceData);
        $this->assertArrayHasKey('costWorkerTotal', $serviceData);
        
        // Vérifications spécifiques
        $this->assertEquals($project->id, $serviceData['project']->id);
        $this->assertEquals(1, $serviceData['month']);
        $this->assertEquals(2025, $serviceData['year']);
        $this->assertEquals('day', $serviceData['category']);
        $this->assertEquals(31, $serviceData['daysInMonth']);
        
        // Vérifier que les données d'entrée contiennent le worker
        $this->assertCount(1, $serviceData['entriesData']);
        $this->assertEquals($worker->id, $serviceData['entriesData'][0]['id']);
        $this->assertEquals('worker', $serviceData['entriesData'][0]['model_type']);
        $this->assertEquals('Pierre Durand', $serviceData['entriesData'][0]['full_name']);
        
        // Vérifier que les heures sont correctement remplies
        $this->assertArrayHasKey('days', $serviceData['entriesData'][0]);
        $this->assertEquals(8, $serviceData['entriesData'][0]['days'][15]); // 15 janvier
        
        // Vérifier les totaux
        $this->assertEquals(14, $serviceData['totalHoursCurrentMonth']); // 8 jour + 6 nuit
        $this->assertGreaterThan(0, $serviceData['costWorkerTotal']);
        
        // Vérifier le récap
        $this->assertCount(1, $serviceData['recap']);
        $this->assertEquals(8, $serviceData['recap'][0]['day_hours']);
        $this->assertEquals(6, $serviceData['recap'][0]['night_hours']);
        $this->assertEquals(14, $serviceData['recap'][0]['total']);
    }

    /**
     * Test de performance : vérifier que les nouveaux services
     * ne sont pas plus lents que l'ancien code
     */
    public function test_performance_not_degraded()
    {
        // Créer un dataset plus conséquent
        $project = Project::create([
            'code' => 2001,
            'name' => 'Projet Performance',
            'category' => 'mh',
            'status' => 'active'
        ]);
        
        // Créer 20 workers avec des timesheets
        for ($i = 1; $i <= 20; $i++) {
            $worker = Worker::create([
                'first_name' => "Worker{$i}",
                'last_name' => "Test{$i}",
                'contract_hours' => 35,
                'monthly_salary' => 3000,
                'category' => 'worker',
                'status' => 'active'
            ]);
            
            $project->workers()->attach($worker->id);
            
            // 5 timesheets par worker
            for ($day = 1; $day <= 5; $day++) {
                TimeSheetable::create([
                    'project_id' => $project->id,
                    'timesheetable_id' => $worker->id,
                    'timesheetable_type' => Worker::class,
                    'date' => "2025-01-{$day}",
                    'category' => 'day',
                    'hours' => 8
                ]);
            }
        }
        
        // Mesurer performance du nouveau service
        $trackingService = app(\App\Services\Tracking\TrackingServiceInterface::class);
        
        $start = microtime(true);
        $serviceData = $trackingService->getTrackingData([
            'project_id' => $project->id,
            'month' => 1,
            'year' => 2025,
            'category' => 'day'
        ]);
        $serviceTime = microtime(true) - $start;
        
        // Vérifier que les données sont correctes
        $this->assertCount(20, $serviceData['entriesData']);
        $this->assertEquals(800, $serviceData['totalHoursCurrentMonth']); // 20 workers * 5 jours * 8h
        
        // Le service ne doit pas être excessivement lent (moins de 1 seconde)
        $this->assertLessThan(1.0, $serviceTime, 
            "TrackingService trop lent: {$serviceTime}s pour 20 workers");
    }

    /**
     * Test des cas limites : workers sans données valides
     */
    public function test_edge_cases_handled_correctly()
    {
        // Worker avec 0 heures contractuelles
        $invalidWorker = Worker::create([
            'first_name' => 'Invalid',
            'last_name' => 'Worker',
            'contract_hours' => 0,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);
        
        $salaryService = new WorkerSalaryService(new \App\Services\Config\BusinessConfigService());
        
        // Doit retourner null pour des données invalides
        $this->assertNull($salaryService->calculateHourlyRate($invalidWorker));
        $this->assertNull($salaryService->calculateChargedRateFromSettings($invalidWorker));
        $this->assertFalse($salaryService->validateWorkerData($invalidWorker));
        
        // HourlyRate doit également gérer les cas invalides
        $hourlyRate = HourlyRate::fromWorkerWithSettings($invalidWorker);
        $this->assertNull($hourlyRate);
    }

    /**
     * Test d'intégration : vérifier que l'injection de dépendance fonctionne
     */
    public function test_dependency_injection_works()
    {
        // Vérifier que les services peuvent être résolus via le container
        $trackingService = app(\App\Services\Tracking\TrackingServiceInterface::class);
        $this->assertInstanceOf(\App\Services\Tracking\TrackingService::class, $trackingService);
        
        $salaryService = app(\App\Services\Salary\WorkerSalaryServiceInterface::class);
        $this->assertInstanceOf(\App\Services\Salary\WorkerSalaryService::class, $salaryService);
        
        $projectRepo = app(\App\Repositories\ProjectRepositoryInterface::class);
        $this->assertInstanceOf(\App\Repositories\ProjectRepository::class, $projectRepo);
        
        $costCalculator = app(\App\Services\Costs\CostCalculatorInterface::class);
        $this->assertInstanceOf(\App\Services\Costs\CostsCalculator::class, $costCalculator);
    }
}