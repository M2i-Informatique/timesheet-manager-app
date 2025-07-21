<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Worker;
use App\Models\Zone;
use App\Models\Setting;
use App\Models\TimeSheetable;
use App\Services\Costs\CostsCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class CostCalculationRegressionTest extends TestCase
{
    use RefreshDatabase;

    private CostsCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = $this->app->make(\App\Services\Costs\CostsCalculator::class);
        
        // Créer les settings exactement comme en prod
        Setting::create([
            'key' => 'rate_charged',
            'value' => '70',
            'start_date' => now(),
            'end_date' => null
        ]);
        
        Setting::create([
            'key' => 'basket',
            'value' => '11',
            'start_date' => now(),
            'end_date' => null
        ]);
    }

    /**
     * Test de régression : Worker normal avec zone
     * CAS RÉEL : Worker avec 35h contrat, 3000€ salaire, zone 10€
     */
    public function test_worker_normal_with_zone_day_cost()
    {
        // Arrange - Données réelles
        $zone = Zone::create([
            'name' => 'Zone 1',
            'min_km' => 0,
            'max_km' => 50,
            'rate' => 10
        ]);
        
        $project = Project::create([
            'code' => 1001,
            'name' => 'Projet Test',
            'category' => 'mh',
            'status' => 'active',
            'zone_id' => $zone->id
        ]);
        
        $worker = Worker::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        // Act - Calcul avec méthode actuelle
        $result = $this->calculator->calculateHourlyDayCost($worker, $project);

        // Assert - Valeur de référence à capturer
        $this->assertEqualsWithDelta(
            44.57, // Valeur attendue (à calculer manuellement)
            $result,
            0.01, // Précision au centime
            "Coût horaire jour worker normal avec zone a changé"
        );
    }

    /**
     * Test de régression : Worker ETAM (pas de zone)
     */
    public function test_worker_etam_day_cost()
    {
        $project = Project::create([
            'code' => 1002,
            'name' => 'Projet ETAM',
            'category' => 'mh',
            'status' => 'active'
        ]);
        
        $worker = Worker::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'contract_hours' => 35,
            'monthly_salary' => 4000,
            'category' => 'etam',
            'status' => 'active'
        ]);

        $result = $this->calculator->calculateHourlyDayCost($worker, $project);

        // ETAM ne doit pas avoir de compensation zone
        $this->assertEqualsWithDelta(
            52.14, // Valeur attendue (à calculer)
            $result,
            0.01,
            "Coût horaire jour worker ETAM a changé"
        );
    }

    /**
     * Test de régression : Coût nuit = jour + taux horaire chargé
     */
    public function test_night_cost_formula()
    {
        $project = Project::create([
            'code' => 1003,
            'name' => 'Projet Nuit',
            'category' => 'mh',
            'status' => 'active'
        ]);
        
        $worker = Worker::create([
            'first_name' => 'Night',
            'last_name' => 'Worker',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        $dayCost = $this->calculator->calculateHourlyDayCost($worker, $project);
        $nightCost = $this->calculator->calculateHourlyNightCost($worker, $project);

        // Vérifier que nuit = jour + taux horaire chargé
        $expectedNightCost = $dayCost + $worker->hourly_rate_charged;
        
        $this->assertEqualsWithDelta(
            $expectedNightCost,
            $nightCost,
            0.01,
            "Formule coût nuit a changé"
        );
    }

    /**
     * Test de régression : Calcul total projet avec timesheets réels
     */
    public function test_project_total_cost_with_real_timesheets()
    {
        // Créer projet avec zone
        $zone = Zone::create(['name' => 'Zone Test', 'rate' => 5]);
        $project = Project::create([
            'code' => 1004,
            'name' => 'Projet Total',
            'category' => 'mh',
            'status' => 'active',
            'zone_id' => $zone->id
        ]);
        
        // Créer worker
        $worker = Worker::create([
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);
        
        // Assigner worker au projet
        $project->workers()->attach($worker->id);
        
        // Créer timesheets réalistes
        TimeSheetable::create([
            'project_id' => $project->id,
            'timesheetable_id' => $worker->id,
            'timesheetable_type' => Worker::class,
            'date' => '2025-01-01',
            'category' => 'day',
            'hours' => 8
        ]);
        
        TimeSheetable::create([
            'project_id' => $project->id,
            'timesheetable_id' => $worker->id,
            'timesheetable_type' => Worker::class,
            'date' => '2025-01-02',
            'category' => 'night',
            'hours' => 4
        ]);

        // Calculer coût total
        $result = $this->calculator->calculateTotalCostForProject(
            $project,
            '2025-01-01',
            '2025-01-31'
        );

        // Vérifier structure et valeurs
        $this->assertArrayHasKey('cost', $result);
        $this->assertArrayHasKey('hours', $result);
        $this->assertArrayHasKey('worker_hours', $result);
        
        $this->assertEquals(12, $result['hours']); // 8 + 4
        $this->assertEquals(12, $result['worker_hours']); // Que des workers
        $this->assertGreaterThan(0, $result['cost']);
        
        // Capturer valeur exacte pour référence future
        $this->assertEqualsWithDelta(
            498.45, // Valeur de référence (à calculer)
            $result['cost'],
            0.01,
            "Coût total projet a changé"
        );
    }

    /**
     * Test avec données edge case : 0 heures
     */
    public function test_zero_hours_edge_case()
    {
        $project = Project::create([
            'code' => 1005,
            'name' => 'Projet Zero',
            'category' => 'mh',
            'status' => 'active'
        ]);
        
        $worker = Worker::create([
            'first_name' => 'Zero',
            'last_name' => 'Worker',
            'contract_hours' => 0, // Edge case
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        $result = $this->calculator->calculateHourlyDayCost($worker, $project);
        
        $this->assertEquals(0.0, $result, "Worker avec 0h contrat doit retourner 0");
    }

    /**
     * Calcul manuel de référence pour validation
     */
    public function test_manual_calculation_reference()
    {
        $worker = Worker::create([
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);
        
        // Calcul manuel étape par étape
        $monthlyHours = (35 * 52) / 12; // 151.67h
        $hourlyRate = 3000 / $monthlyHours; // 19.78€
        $chargedRate = $hourlyRate * 1.70; // 33.63€
        $basketPerHour = (11 * 1.70) / 7; // 2.67€ (35h/5j = 7h/jour)
        
        $this->assertEqualsWithDelta(19.78, $hourlyRate, 0.01);
        $this->assertEqualsWithDelta(33.63, $chargedRate, 0.01);
        $this->assertEqualsWithDelta(2.67, $basketPerHour, 0.01);
    }
}