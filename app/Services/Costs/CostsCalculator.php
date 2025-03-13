<?php

namespace App\Services\Costs;

use App\Models\Setting;
use App\Models\Project;
use App\Models\Worker;
use Illuminate\Support\Facades\Log;

class CostsCalculator
{
    protected float $rateCharge;      // General charge rate (e.g. 1.7)
    protected float $baseBasketValue; // Base basket value

    /**
     * CostsCalculator constructor.
     */
    public function __construct()
    {
        $chargePercentage = (float) Setting::getValue('rate_charged', 70);
        $this->rateCharge = 1 + ($chargePercentage / 100);
        $this->baseBasketValue = (float) Setting::getValue('basket', 11);

        Log::info("CostsCalculator initialized with rateCharge: " . $this->rateCharge . ", baseBasketValue: " . $this->baseBasketValue);
    }

    /**
     * Calculate the hourly day cost for a worker on a project.
     *
     * @param Worker  $worker
     * @param Project $project
     * @return float
     */
    public function calculateHourlyDayCost(Worker $worker, Project $project): float
    {
        $monthlyHours = ($worker->contract_hours * 52) / 12;
        if ($monthlyHours <= 0) {
            return 0.0;
        }

        $salaryCharged = $worker->hourly_rate_charged;
        $hourlyBasketCharged = ($this->baseBasketValue * $this->rateCharge) / ($worker->contract_hours / 5);

        Log::info("calculateHourlyDayCost - Worker ID: {$worker->id}, Project ID: {$project->id}");
        Log::info("  Monthly hours: {$monthlyHours}, Salary charged: {$salaryCharged}, Hourly basket charged: {$hourlyBasketCharged}");

        if ($this->isEtam($worker)) {
            $cost = $salaryCharged + $hourlyBasketCharged;
            Log::info("  Worker is ETAM, cost: {$cost}");
            return $cost;
        }

        $zoneRate = $project->zone ? $project->zone->rate : 0;
        $hourlyZoneCharged = ($zoneRate * $this->rateCharge) / ($worker->contract_hours / 5);

        $cost = $salaryCharged + $hourlyBasketCharged + $hourlyZoneCharged;

        Log::info("  Worker is NOT ETAM, Zone rate: {$zoneRate}, Hourly zone charged: {$hourlyZoneCharged}");
        Log::info("  Total hourly day cost: {$cost}");

        return $cost;
    }

    /**
     * Calculate the hourly night cost for a worker on a project.
     *
     * @param Worker  $worker
     * @param Project $project
     * @return float
     */
    public function calculateHourlyNightCost(Worker $worker, Project $project): float
    {
        $hourlyDayCost = $this->calculateHourlyDayCost($worker, $project);
        $cost = $hourlyDayCost + $worker->hourly_rate_charged;

        Log::info("calculateHourlyNightCost - Worker ID: {$worker->id}, Project ID: {$project->id}");
        Log::info("  Hourly day cost: {$hourlyDayCost}, Hourly rate charged: {$worker->hourly_rate_charged}");
        Log::info("  Total hourly night cost: {$cost}");

        return $cost;
    }

    /**
     * Calculate the total cost for a worker on a project over a given period.
     *
     * @param Worker  $worker
     * @param Project $project
     * @param string  $startDate
     * @param string  $endDate
     * @return float
     */
    public function calculateTotalCostForOneWorker(Worker $worker, Project $project, string $startDate, string $endDate): float
    {
        Log::info("calculateTotalCostForOneWorker - Worker ID: {$worker->id}, Project ID: {$project->id}, Date range: {$startDate} to {$endDate}");

        $timesheets = $worker->timesheets()
            ->where('project_id', $project->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        Log::info("  Found " . $timesheets->count() . " timesheets");

        $totalCost = 0.0;
        foreach ($timesheets as $timesheet) {
            $costPerHour = ($timesheet->category === 'night')
                ? $this->calculateHourlyNightCost($worker, $project)
                : $this->calculateHourlyDayCost($worker, $project);

            $cost = $costPerHour * $timesheet->hours;
            $totalCost += $cost;

            Log::info("  Timesheet ID: {$timesheet->id}, Category: {$timesheet->category}, Hours: {$timesheet->hours}, Cost per hour: {$costPerHour}, Cost: {$cost}");
        }

        Log::info("  Total cost for worker: {$totalCost}");
        return $totalCost;
    }

    /**
     * Calculate the total cost (and total hours) for all workers on a project.
     *
     * @param Project     $project
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array ['cost' => float, 'hours' => float]
     */
    public function calculateTotalCostForProject(Project $project, ?string $startDate, ?string $endDate): array
    {
        Log::info("calculateTotalCostForProject - Project ID: {$project->id}, Date range: {$startDate} to {$endDate}");

        $totalCost  = 0.0;
        $totalHours = 0.0;

        $workers = $project->workers()
            ->with(['timesheets' => function ($query) use ($project, $startDate, $endDate) {
                $query->where('project_id', $project->id);
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->where('date', '>=', $startDate);
                } elseif ($endDate) {
                    $query->where('date', '<=', $endDate);
                }
            }])->get();

        Log::info("  Found " . $workers->count() . " workers for project");

        foreach ($workers as $worker) {
            Log::info("  Processing Worker ID: {$worker->id}, Name: {$worker->first_name} {$worker->last_name}");
            Log::info("  Worker has " . $worker->timesheets->count() . " timesheets");

            foreach ($worker->timesheets as $timesheet) {
                Log::info("    Timesheet ID: {$timesheet->id}, Date: {$timesheet->date}, Project ID: {$timesheet->project_id}, Category: {$timesheet->category}, Hours: {$timesheet->hours}");

                $costPerHour = ($timesheet->category === 'night')
                    ? $this->calculateHourlyNightCost($worker, $project)
                    : $this->calculateHourlyDayCost($worker, $project);

                $cost = $costPerHour * $timesheet->hours;
                $totalCost += $cost;
                $totalHours += $timesheet->hours;

                Log::info("    Cost per hour: {$costPerHour}, Cost: {$cost}");
            }
        }

        Log::info("  Total hours: {$totalHours}, Total cost: {$totalCost}");

        return [
            'cost'  => $totalCost,
            'hours' => $totalHours,
        ];
    }

    /**
     * Calculate a detailed breakdown of costs for a project.
     *
     * @param Project     $project
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function calculateDetailedProjectCostForProject(Project $project, ?string $startDate, ?string $endDate): array
    {
        Log::info("calculateDetailedProjectCostForProject - Project ID: {$project->id}, Name: {$project->name}, Date range: {$startDate} to {$endDate}");

        $totalProjectCost  = 0.0;
        $totalProjectHours = 0.0;
        $workersData       = [];

        $workers = $project->workers()
            ->has('timesheets') // Only include workers with timesheets
            ->with(['timesheets' => function ($query) use ($project, $startDate, $endDate) {
                $query->where('project_id', $project->id);
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->where('date', '>=', $startDate);
                } elseif ($endDate) {
                    $query->where('date', '<=', $endDate);
                }
            }])->get();

        Log::info("  Found " . $workers->count() . " workers with timesheets for project");

        foreach ($workers as $worker) {
            Log::info("  Processing Worker ID: {$worker->id}, Name: {$worker->first_name} {$worker->last_name}");
            Log::info("  Worker has " . $worker->timesheets->count() . " timesheets");

            $workerTotalHours      = 0.0;
            $workerTotalCost       = 0.0;
            $workerTimesheetDetails = [];
            $hourlyCostCache = [
                'day'   => $this->calculateHourlyDayCost($worker, $project),
                'night' => $this->calculateHourlyNightCost($worker, $project),
            ];

            Log::info("  Hourly cost cache - Day: {$hourlyCostCache['day']}, Night: {$hourlyCostCache['night']}");

            foreach ($worker->timesheets as $timesheet) {
                Log::info("    Timesheet ID: {$timesheet->id}, Date: {$timesheet->date}, Project ID: {$timesheet->project_id}, Category: {$timesheet->category}, Hours: {$timesheet->hours}");

                $hourlyCost = round($hourlyCostCache[$timesheet->category], 2);
                $cost       = round($hourlyCost * $timesheet->hours, 2);

                Log::info("    Using hourly cost: {$hourlyCost}, Total cost for timesheet: {$cost}");

                $workerTimesheetDetails[] = [
                    'type'       => 'timesheets',
                    'id'         => $timesheet->id,
                    'attributes' => [
                        'date'        => $timesheet->date->format('Y-m-d'),
                        'category'    => $timesheet->category,
                        'hours'       => $timesheet->hours,
                        'hourly_cost' => $hourlyCost,
                        'cost'        => $cost,
                    ],
                    'hours'       => $timesheet->hours,
                    'hourly_cost' => $hourlyCost,
                    'cost'        => $cost,
                ];

                $workerTotalHours += $timesheet->hours;
                $workerTotalCost  += $cost;
            }

            Log::info("  Worker total hours: {$workerTotalHours}, Worker total cost: {$workerTotalCost}");

            $workersData[] = [
                'type'       => 'workers',
                'id'         => $worker->id,
                'attributes' => [
                    'first_name'          => $worker->first_name,
                    'last_name'           => $worker->last_name,
                    'category'            => $worker->category,
                    'contract_hours'      => $worker->contract_hours,
                    'monthly_salary'      => $worker->monthly_salary,
                    'hourly_rate'         => $worker->hourly_rate,
                    'hourly_rate_charged' => $worker->hourly_rate_charged,
                ],
                'relationships' => [
                    'timesheets' => $workerTimesheetDetails,
                ],
                'total_hours' => $workerTotalHours,
                'total_cost'  => $workerTotalCost,
            ];

            $totalProjectHours += $workerTotalHours;
            $totalProjectCost  += $workerTotalCost;
        }

        Log::info("  Project total hours: {$totalProjectHours}, Project total cost: {$totalProjectCost}");

        return [
            'data' => [
                'type'       => 'projects',
                'id'         => $project->id,
                'attributes' => [
                    'code'     => $project->code,
                    'category' => $project->category,
                    'name'     => $project->name,
                    'address'  => $project->address,
                    'city'     => $project->city,
                    'distance' => $project->distance,
                    'status'   => $project->status,
                ],
                'relationships' => [
                    'zone' => [
                        'category'   => 'zones',
                        'id'         => $project->zone ? $project->zone->id : null,
                        'attributes' => [
                            'name' => $project->zone ? $project->zone->name : null,
                            'rate' => $project->zone ? $project->zone->rate : null,
                        ],
                    ],
                    'workers' => $workersData,
                ],
                'total_hours' => $totalProjectHours,
                'total_cost'  => $totalProjectCost,
                'start_date'  => $startDate,
                'end_date'    => $endDate,
            ]
        ];
    }

    /**
     * Check if the worker is of category ETAM.
     *
     * @param Worker $worker
     * @return bool
     */
    public function isEtam(Worker $worker): bool
    {
        return $worker->category === 'etam';
    }
}
