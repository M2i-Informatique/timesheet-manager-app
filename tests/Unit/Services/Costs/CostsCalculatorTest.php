<?php

namespace Tests\Unit\Services\Costs;

use Tests\TestCase;
use App\Models\Worker;
use App\Models\Project;
use App\Models\Zone;
use App\Models\Setting;
use App\Services\Costs\CostsCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CostsCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private CostsCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = $this->app->make(CostsCalculator::class);
        
        // Créer les settings par défaut
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

    public function test_calculates_hourly_day_cost_for_non_etam_worker()
    {
        // Arrange
        $zone = Zone::create([
            'name' => 'Zone Test',
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
            'category' => 'worker', // non-ETAM
            'status' => 'active'
        ]);

        // Act
        $result = $this->calculator->calculateHourlyDayCost($worker, $project);

        // Assert
        $this->assertIsFloat($result);
        $this->assertGreaterThan(0, $result);
    }

    public function test_calculates_hourly_day_cost_for_etam_worker()
    {
        // Arrange
        $zone = Zone::create([
            'name' => 'Zone Test',
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
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'contract_hours' => 35,
            'monthly_salary' => 4000,
            'category' => 'etam', // ETAM
            'status' => 'active'
        ]);

        // Act
        $result = $this->calculator->calculateHourlyDayCost($worker, $project);

        // Assert
        $this->assertIsFloat($result);
        $this->assertGreaterThan(0, $result);
    }

    public function test_calculates_hourly_night_cost()
    {
        // Arrange
        $project = Project::create([
            'code' => 1001,
            'name' => 'Projet Test',
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

        // Act
        $dayResult = $this->calculator->calculateHourlyDayCost($worker, $project);
        $nightResult = $this->calculator->calculateHourlyNightCost($worker, $project);

        // Assert
        $this->assertIsFloat($nightResult);
        $this->assertGreaterThan($dayResult, $nightResult);
    }

    public function test_identifies_etam_worker()
    {
        // Arrange
        $etamWorker = Worker::create([
            'first_name' => 'ETAM',
            'last_name' => 'Worker',
            'contract_hours' => 35,
            'monthly_salary' => 4000,
            'category' => 'etam',
            'status' => 'active'
        ]);

        $regularWorker = Worker::create([
            'first_name' => 'Regular',
            'last_name' => 'Worker',
            'contract_hours' => 35,
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        // Act & Assert
        $this->assertTrue($this->calculator->isEtam($etamWorker));
        $this->assertFalse($this->calculator->isEtam($regularWorker));
    }

    public function test_returns_zero_for_invalid_worker_data()
    {
        // Arrange
        $project = Project::create([
            'code' => 1001,
            'name' => 'Projet Test',
            'category' => 'mh',
            'status' => 'active'
        ]);
        
        $worker = Worker::create([
            'first_name' => 'Invalid',
            'last_name' => 'Worker',
            'contract_hours' => 0, // Invalid
            'monthly_salary' => 3000,
            'category' => 'worker',
            'status' => 'active'
        ]);

        // Act
        $result = $this->calculator->calculateHourlyDayCost($worker, $project);

        // Assert
        $this->assertEquals(0.0, $result);
    }
}