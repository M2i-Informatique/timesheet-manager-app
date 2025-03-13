<?php

namespace App\Services\Costs;

use App\Models\Setting;
use App\Models\Project;
use App\Models\Worker;

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

        if ($this->isEtam($worker)) {
            return $salaryCharged + $hourlyBasketCharged;
        }

        $zoneRate = $project->zone ? $project->zone->rate : 0;
        $hourlyZoneCharged = ($zoneRate * $this->rateCharge) / ($worker->contract_hours / 5);

        return $salaryCharged + $hourlyBasketCharged + $hourlyZoneCharged;
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
        return $hourlyDayCost + $worker->hourly_rate_charged;
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
        $timesheets = $worker->timesheets()
            ->where('project_id', $project->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalCost = 0.0;
        foreach ($timesheets as $timesheet) {
            $costPerHour = ($timesheet->category === 'night')
                ? $this->calculateHourlyNightCost($worker, $project)
                : $this->calculateHourlyDayCost($worker, $project);
            $totalCost += $costPerHour * $timesheet->hours;
        }
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
        $totalCost  = 0.0;
        $totalHours = 0.0;
        $totalWorkerHours = 0.0;
        $totalInterimHours = 0.0;

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

        // Récupérer les heures des interims (seulement pour les afficher, pas pour les coûts)
        $interims = $project->interims()
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

        foreach ($workers as $worker) {
            foreach ($worker->timesheets as $timesheet) {
                $costPerHour = ($timesheet->category === 'night')
                    ? $this->calculateHourlyNightCost($worker, $project)
                    : $this->calculateHourlyDayCost($worker, $project);
                $totalCost  += $costPerHour * $timesheet->hours;
                $totalWorkerHours += $timesheet->hours;
            }
        }

        // Calculer juste les heures des interims sans les coûts
        foreach ($interims as $interim) {
            foreach ($interim->timesheets as $timesheet) {
                $totalInterimHours += $timesheet->hours;
            }
        }

        $totalHours = $totalWorkerHours + $totalInterimHours;

        return [
            'cost'  => $totalCost,
            'hours' => $totalHours,
            'worker_hours' => $totalWorkerHours,
            'interim_hours' => $totalInterimHours,
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
        $totalProjectCost = 0.0;
        $totalProjectHours = 0.0;
        $totalWorkerHours = 0.0;
        $totalInterimHours = 0.0;
        $workersData = [];
        $interimsData = [];

        // Récupérer les workers avec leurs timesheets
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

        // Récupérer les interims avec leurs timesheets
        $interims = $project->interims()
            ->has('timesheets')
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

        // Traiter les données des workers
        foreach ($workers as $worker) {
            $workerTotalHours = 0.0;
            $workerTotalCost = 0.0;
            $workerTimesheetDetails = [];

            $hourlyCostCache = [
                'day' => $this->calculateHourlyDayCost($worker, $project),
                'night' => $this->calculateHourlyNightCost($worker, $project),
            ];

            foreach ($worker->timesheets as $timesheet) {
                $hourlyCost = round($hourlyCostCache[$timesheet->category], 2);
                $cost = round($hourlyCost * $timesheet->hours, 2);

                $workerTimesheetDetails[] = [
                    'type' => 'timesheets',
                    'id' => $timesheet->id,
                    'attributes' => [
                        'date' => $timesheet->date->format('Y-m-d'),
                        'category' => $timesheet->category,
                        'hours' => $timesheet->hours,
                        'hourly_cost' => $hourlyCost,
                        'cost' => $cost,
                    ],
                    'hours' => $timesheet->hours,
                    'hourly_cost' => $hourlyCost,
                    'cost' => $cost,
                ];

                $workerTotalHours += $timesheet->hours;
                $workerTotalCost += $cost;
            }

            if ($workerTotalHours > 0) {
                $workersData[] = [
                    'type' => 'workers',
                    'id' => $worker->id,
                    'attributes' => [
                        'first_name' => $worker->first_name,
                        'last_name' => $worker->last_name,
                        'category' => $worker->category,
                        'contract_hours' => $worker->contract_hours,
                        'monthly_salary' => $worker->monthly_salary,
                        'hourly_rate' => $worker->hourly_rate,
                        'hourly_rate_charged' => $worker->hourly_rate_charged,
                    ],
                    'relationships' => [
                        'timesheets' => $workerTimesheetDetails,
                    ],
                    'total_hours' => $workerTotalHours,
                    'total_cost' => $workerTotalCost,
                ];

                $totalWorkerHours += $workerTotalHours;
                $totalProjectCost += $workerTotalCost;
            }
        }

        // Traiter les données des interims (juste pour les heures, pas pour les coûts)
        foreach ($interims as $interim) {
            $interimTotalHours = 0.0;
            $interimTimesheetDetails = [];

            foreach ($interim->timesheets as $timesheet) {
                $interimTimesheetDetails[] = [
                    'type' => 'timesheets',
                    'id' => $timesheet->id,
                    'attributes' => [
                        'date' => $timesheet->date->format('Y-m-d'),
                        'category' => $timesheet->category,
                        'hours' => $timesheet->hours,
                        'hourly_cost' => 0, // Pas de coût pour les interims
                        'cost' => 0,       // Pas de coût pour les interims
                    ],
                    'hours' => $timesheet->hours,
                    'hourly_cost' => 0,
                    'cost' => 0,
                ];

                $interimTotalHours += $timesheet->hours;
            }

            if ($interimTotalHours > 0) {
                $interimsData[] = [
                    'type' => 'interims',
                    'id' => $interim->id,
                    'attributes' => [
                        'agency' => $interim->agency,
                        'hourly_rate' => $interim->hourly_rate,
                    ],
                    'relationships' => [
                        'timesheets' => $interimTimesheetDetails,
                    ],
                    'total_hours' => $interimTotalHours,
                    'total_cost' => 0, // Pas de coût pour les interims
                ];

                $totalInterimHours += $interimTotalHours;
                // Pas d'ajout au coût total du projet
            }
        }

        $totalProjectHours = $totalWorkerHours + $totalInterimHours;

        return [
            'data' => [
                'type' => 'projects',
                'id' => $project->id,
                'attributes' => [
                    'code' => $project->code,
                    'category' => $project->category,
                    'name' => $project->name,
                    'address' => $project->address,
                    'city' => $project->city,
                    'distance' => $project->distance,
                    'status' => $project->status,
                ],
                'relationships' => [
                    'zone' => [
                        'category' => 'zones',
                        'id' => $project->zone ? $project->zone->id : null,
                        'attributes' => [
                            'name' => $project->zone ? $project->zone->name : null,
                            'rate' => $project->zone ? $project->zone->rate : null,
                        ],
                    ],
                    'workers' => $workersData,
                    'interims' => $interimsData,
                ],
                'total_hours' => $totalProjectHours,
                'total_worker_hours' => $totalWorkerHours,
                'total_interim_hours' => $totalInterimHours,
                'total_cost' => $totalProjectCost,
                'start_date' => $startDate,
                'end_date' => $endDate,
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
