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

class ReferenceCalculationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test pour capturer les valeurs de référence actuelles
     * Ce test nous donne les valeurs exactes à préserver
     */
    public function test_capture_reference_values()
    {
        // Setup identique à la production
        Setting::create(['key' => 'rate_charged', 'value' => '70', 'start_date' => now()]);
        Setting::create(['key' => 'basket', 'value' => '11', 'start_date' => now()]);
        
        $zone = Zone::create([
            'name' => 'Zone Test',
            'min_km' => 0,
            'max_km' => 50,
            'rate' => 10
        ]);
        
        $project = Project::create([
            'code' => 1001,
            'name' => 'Projet Reference',
            'category' => 'mh',
            'status' => 'active',
            'zone_id' => $zone->id
        ]);
        
        $worker = Worker::create([
            'first_name' => 'Reference',
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
        
        TimeSheetable::create([
            'project_id' => $project->id,
            'timesheetable_id' => $worker->id,
            'timesheetable_type' => Worker::class,
            'date' => '2025-01-02',
            'category' => 'night',
            'hours' => 4
        ]);
        
        $calculator = $this->app->make(\App\Services\Costs\CostsCalculator::class);
        
        // Capturer les valeurs actuelles
        $hourlyDayCost = $calculator->calculateHourlyDayCost($worker, $project);
        $hourlyNightCost = $calculator->calculateHourlyNightCost($worker, $project);
        
        $projectCosts = $calculator->calculateTotalCostForProject(
            $project,
            '2025-01-01',
            '2025-01-31'
        );
        
        // Afficher les valeurs pour les intégrer dans les tests
        echo "\n=== VALEURS DE RÉFÉRENCE À PRÉSERVER ===\n";
        echo "Worker hourly_rate: " . $worker->hourly_rate . "\n";
        echo "Worker hourly_rate_charged: " . $worker->hourly_rate_charged . "\n";
        echo "Hourly day cost: " . $hourlyDayCost . "\n";
        echo "Hourly night cost: " . $hourlyNightCost . "\n";
        echo "Project total cost: " . $projectCosts['cost'] . "\n";
        echo "Project total hours: " . $projectCosts['hours'] . "\n";
        echo "=======================================\n";
        
        // Tests pour valider les calculs actuels
        $this->assertGreaterThan(0, $hourlyDayCost);
        $this->assertGreaterThan($hourlyDayCost, $hourlyNightCost);
        $this->assertGreaterThan(0, $projectCosts['cost']);
        $this->assertEquals(12, $projectCosts['hours']); // 8 + 4
        
        // Ces valeurs seront nos références pour les tests de régression
        $this->assertTrue(true, "Valeurs de référence capturées avec succès");
    }
}