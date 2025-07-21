<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Costs\CostCalculatorInterface;
use App\Services\Costs\CostsCalculator;
use App\Services\Salary\WorkerSalaryServiceInterface;
use App\Services\Salary\WorkerSalaryService;
use App\Services\Config\BusinessConfigService;
use App\Services\Cache\CacheService;
use App\Services\Monitoring\MetricsService;
use App\CQRS\CommandBus;
use App\CQRS\QueryBus;
use App\Models\Worker;
use App\Models\Project;
use App\Models\Zone;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DependencyInjectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer les settings nécessaires
        Setting::create(['key' => 'rate_charged', 'value' => '70', 'start_date' => now()]);
        Setting::create(['key' => 'basket', 'value' => '11', 'start_date' => now()]);
    }

    /**
     * Test que tous les services peuvent être résolus via l'injection de dépendance
     */
    public function test_all_services_can_be_resolved_from_container()
    {
        // Services principaux
        $costCalculator = $this->app->make(CostCalculatorInterface::class);
        $this->assertInstanceOf(CostsCalculator::class, $costCalculator);

        $salaryService = $this->app->make(WorkerSalaryServiceInterface::class);
        $this->assertInstanceOf(WorkerSalaryService::class, $salaryService);

        $configService = $this->app->make(BusinessConfigService::class);
        $this->assertInstanceOf(BusinessConfigService::class, $configService);

        $cacheService = $this->app->make(CacheService::class);
        $this->assertInstanceOf(CacheService::class, $cacheService);

        $metricsService = $this->app->make(MetricsService::class);
        $this->assertInstanceOf(MetricsService::class, $metricsService);

        // Bus CQRS
        $commandBus = $this->app->make(CommandBus::class);
        $this->assertInstanceOf(CommandBus::class, $commandBus);

        $queryBus = $this->app->make(QueryBus::class);
        $this->assertInstanceOf(QueryBus::class, $queryBus);
    }

    /**
     * Test que CostsCalculator fonctionne correctement avec l'injection de dépendance
     */
    public function test_costs_calculator_works_with_dependency_injection()
    {
        // Créer un worker de test
        $worker = Worker::create([
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        // Créer une zone et un projet
        $zone = Zone::create([
            'name' => 'Test Zone',
            'min_km' => 0,
            'max_km' => 50,
            'rate' => 10
        ]);

        $project = Project::create([
            'code' => 1001,
            'name' => 'Test Project',
            'category' => 'mh',
            'status' => 'active',
            'zone_id' => $zone->id
        ]);

        // Utiliser l'injection de dépendance
        $calculator = $this->app->make(CostCalculatorInterface::class);
        
        // Tester les calculs
        $dayHourly = $calculator->calculateHourlyDayCost($worker, $project);
        $nightHourly = $calculator->calculateHourlyNightCost($worker, $project);
        
        $this->assertIsFloat($dayHourly);
        $this->assertIsFloat($nightHourly);
        $this->assertGreaterThan(0, $dayHourly);
        $this->assertGreaterThan($dayHourly, $nightHourly);
    }

    /**
     * Test que WorkerSalaryService fonctionne avec BusinessConfigService
     */
    public function test_worker_salary_service_uses_business_config()
    {
        $worker = Worker::create([
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        $salaryService = $this->app->make(WorkerSalaryServiceInterface::class);
        $configService = $this->app->make(BusinessConfigService::class);
        
        // Vérifier que la configuration est utilisée
        $weeksPerYear = $configService->getWeeksPerYear();
        $this->assertEquals(52, $weeksPerYear);
        
        // Vérifier que le service utilise bien la configuration
        $hourlyRate = $salaryService->calculateHourlyRate($worker);
        $this->assertIsFloat($hourlyRate);
        $this->assertGreaterThan(0, $hourlyRate);
        
        // Calculer manuellement pour vérifier
        $expectedRate = $worker->monthly_salary / ($worker->contract_hours * ($weeksPerYear / 12));
        $this->assertEqualsWithDelta($expectedRate, $hourlyRate, 0.001);
    }

    /**
     * Test que les singletons sont bien partagés
     */
    public function test_singletons_are_shared()
    {
        $configService1 = $this->app->make(BusinessConfigService::class);
        $configService2 = $this->app->make(BusinessConfigService::class);
        
        $this->assertSame($configService1, $configService2);
        
        $cacheService1 = $this->app->make(CacheService::class);
        $cacheService2 = $this->app->make(CacheService::class);
        
        $this->assertSame($cacheService1, $cacheService2);
        
        $metricsService1 = $this->app->make(MetricsService::class);
        $metricsService2 = $this->app->make(MetricsService::class);
        
        $this->assertSame($metricsService1, $metricsService2);
    }

    /**
     * Test que les interfaces sont correctement liées
     */
    public function test_interfaces_are_properly_bound()
    {
        $costCalculator = $this->app->make(CostCalculatorInterface::class);
        $this->assertInstanceOf(CostsCalculator::class, $costCalculator);
        
        $salaryService = $this->app->make(WorkerSalaryServiceInterface::class);
        $this->assertInstanceOf(WorkerSalaryService::class, $salaryService);
        
        // Vérifier que le CostsCalculator reçoit bien ses dépendances
        $reflection = new \ReflectionClass($costCalculator);
        $property = $reflection->getProperty('salaryService');
        $property->setAccessible(true);
        $injectedService = $property->getValue($costCalculator);
        
        $this->assertInstanceOf(WorkerSalaryService::class, $injectedService);
    }
}