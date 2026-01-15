<?php

namespace App\Services\Costs;

use App\Models\Worker;
use App\Models\Interim;
use Illuminate\Support\Facades\Log;

class WorkerCostsService extends CostsCalculator
{
    /**
     * Récupère les coûts des travailleurs avec des filtres optionnels.
     *
     * Filtres disponibles :
     *   - id: ID du travailleur (facultatif)
     *   - category: Catégorie du travailleur (facultatif)
     *   - startDate: Date de début (facultatif)
     *   - endDate: Date de fin (facultatif)
     *
     * Le coût de chaque travailleur est calculé sur l'ensemble des feuilles de temps. Le coût de chaque feuille de temps est
     * calculé en fonction de son projet associé.
     *
     * @param string|null $id
     * @param string|null $category
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getWorkerCosts(?string $id, ?string $category, ?string $startDate, ?string $endDate): array
    {
        Log::info("getWorkerCosts - Start with filters: ID={$id}, Category={$category}, StartDate={$startDate}, EndDate={$endDate}");

        $query = Worker::query();

        if ($id) {
            $query->where('id', $id);
        }

        // Filtrer par catégorie si fourni (et différent de 'interim')
        if ($category && $category !== 'interim') {
            if ($category === 'dubocq') {
                $query->whereIn('category', ['worker', 'etam']);
            } else {
                $query->where('category', $category);
            }
        }

        // Si la catégorie est spécifiquement 'interim', on ne récupère pas les workers
        $workers = ($category === 'interim') ? collect([]) : $query->get();
        $results = [];

        Log::info("Found " . $workers->count() . " workers matching filters");

        foreach ($workers as $worker) {
            Log::info("Processing Worker ID: {$worker->id}, Name: {$worker->first_name} {$worker->last_name}");

            $workerTotalCost = 0.0;
            $workerTimesheetDetails = [];

            // Filtrer les feuilles de temps du travailleur par date si des filtres sont fournis
            $timesheetsQuery = $worker->timesheets();

            if ($startDate && $endDate) {
                $timesheetsQuery->whereBetween('date', [$startDate, $endDate]);
            } elseif ($startDate) {
                $timesheetsQuery->where('date', '>=', $startDate);
            } elseif ($endDate) {
                $timesheetsQuery->where('date', '<=', $endDate);
            }

            $timesheets = $timesheetsQuery->get();

            Log::info("  Found " . $timesheets->count() . " timesheets for worker");

            foreach ($timesheets as $timesheet) {
                Log::info("    Timesheet ID: {$timesheet->id}, Project ID: {$timesheet->project_id}, Date: {$timesheet->date}, Category: {$timesheet->category}, Hours: {$timesheet->hours}");

                // Récupérer le projet associé à la feuille de temps.
                // Assurez-vous que le modèle Timesheet a une relation "project".
                $project = $timesheet->project;
                if (!$project) {
                    Log::warning("    No project found for timesheet ID: {$timesheet->id}");
                    continue;
                }

                Log::info("    Project ID: {$project->id}, Name: {$project->name}");

                $costPerHour = ($timesheet->category === 'night')
                    ? $this->calculateHourlyNightCost($worker, $project)
                    : $this->calculateHourlyDayCost($worker, $project);

                $cost = $costPerHour * $timesheet->hours;
                $workerTotalCost += $cost;

                Log::info("    Using " . ($timesheet->category === 'night' ? 'night' : 'day') . " calculation method, Cost per hour: {$costPerHour}, Cost: {$cost}");

                $workerTimesheetDetails[] = [
                    'date'        => $timesheet->date->format('Y-m-d'),
                    'category'    => $timesheet->category,
                    'hours'       => $timesheet->hours,
                    'hourly_cost' => $costPerHour,
                    'cost'        => $cost,
                ];
            }

            Log::info("  Worker total cost: {$workerTotalCost}");

            if ($workerTotalCost > 0) {
                $results[] = [
                    'id'           => $worker->id,
                    'first_name'   => $worker->first_name,
                    'last_name'    => $worker->last_name,
                    'category'     => $worker->category,
                    'total_cost'   => $workerTotalCost,
                    'timesheets'   => $workerTimesheetDetails,
                ];
            }
        }

        // Récupérer les intérimaires si la catégorie est vide ou 'interim'
        // Pas de filtre coût/heure complexe pour eux, juste taux * heures
        if (!$category || $category === 'interim') {
            $interimQuery = Interim::query();
            // Pas de filtre timesheetCategory ou projectCategory ici dans la signature actuelle de getWorkerCosts
            
            $interims = $interimQuery->get();

            foreach ($interims as $interim) {
                $interimTotalCost = 0.0;
                $timesheetDetails = [];
                $hourlyRate = $interim->hourly_rate; // Taux fixe pour l'intérim

                $timesheetsQuery = $interim->timesheets();

                if ($startDate && $endDate) {
                    $timesheetsQuery->whereBetween('date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $timesheetsQuery->where('date', '>=', $startDate);
                } elseif ($endDate) {
                    $timesheetsQuery->where('date', '<=', $endDate);
                }

                $timesheets = $timesheetsQuery->get();
                
                foreach ($timesheets as $timesheet) {
                    // Pour les intérims, on ne distingue pas jour/nuit pour le taux, c'est le taux horaire fixe
                    // Mais on garde la info category pour l'affichage
                    
                    $cost = $hourlyRate * $timesheet->hours;
                    $interimTotalCost += $cost;

                    $timesheetDetails[] = [
                        'date'        => $timesheet->date->format('Y-m-d'),
                        'category'    => $timesheet->category,
                        'hours'       => $timesheet->hours,
                        'hourly_cost' => $hourlyRate,
                        'cost'        => $cost,
                    ];
                }

                if ($interimTotalCost > 0) {
                     $results[] = [
                        'id'           => $interim->id,
                        'first_name'   => 'Intérim',
                        'last_name'    => $interim->agency,
                        'category'     => 'interim',
                        'total_cost'   => $interimTotalCost,
                        'timesheets'   => $timesheetDetails,
                    ];
                }
            }
        }

        Log::info("getWorkerCosts - Returning " . count($results) . " workers");

        Log::info("Results Summary:");
        foreach ($results as $index => $worker) {
            Log::info("  Worker {$index}: ID={$worker['id']}, Name={$worker['first_name']} {$worker['last_name']}, Cost={$worker['total_cost']}");
        }

        return $results;
    }
}
