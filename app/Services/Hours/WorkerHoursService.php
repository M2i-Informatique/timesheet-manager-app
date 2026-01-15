<?php

namespace App\Services\Hours;

use App\Models\Worker;
use App\Models\Interim;
use Illuminate\Support\Facades\Log;

class WorkerHoursService
{
    /**
     * Récupère les heures des travailleurs avec des filtres optionnels.
     *
     * Filtres disponibles :
     *   - id: ID du travailleur (facultatif)
     *   - timesheetCategory: Catégorie de la feuille de temps (day/night) (facultatif)
     *   - startDate: Date de début (facultatif)
     *   - endDate: Date de fin (facultatif)
     *   - projectCategory: Catégorie du projet (mh/go) (facultatif)
     *
     * @param string|null $id
     * @param string|null $timesheetCategory
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $projectCategory
     * @param string|null $category
     * @return array
     */
    public function getWorkerHours(?string $id, ?string $timesheetCategory, ?string $startDate, ?string $endDate, ?string $projectCategory = null, ?string $category = null): array
    {
        Log::info("getWorkerHours - Start with filters: ID={$id}, TimesheetCategory={$timesheetCategory}, StartDate={$startDate}, EndDate={$endDate}, ProjectCategory={$projectCategory}");

        $query = Worker::query();

        if ($id) {
            $query->where('id', $id);
        }

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
            // Log::info("Processing Worker ID: {$worker->id}, Name: {$worker->first_name} {$worker->last_name}");
            
            $workerHours   = 0.0;
            $timesheetData = [];

            $timesheetsQuery = $worker->timesheets();

            if ($startDate && $endDate) {
                $timesheetsQuery->whereBetween('date', [$startDate, $endDate]);
            } elseif ($startDate) {
                $timesheetsQuery->where('date', '>=', $startDate);
            } elseif ($endDate) {
                $timesheetsQuery->where('date', '<=', $endDate);
            }

            // Filtrer par catégorie de feuille de temps (day/night)
            if ($timesheetCategory) {
                $timesheetsQuery->where('category', $timesheetCategory);
            }
            
            // FILTRE AJOUTÉ : Filtrer par catégorie de projet (mh/go)
            if ($projectCategory) {
                $timesheetsQuery->whereHas('project', function ($q) use ($projectCategory) {
                    $q->where('category', $projectCategory);
                });
            }

            $timesheets = $timesheetsQuery->get();

            // Log::info("  Found " . $timesheets->count() . " timesheets for worker");

            foreach ($timesheets as $timesheet) {
                $workerHours += $timesheet->hours;
                $timesheetData[] = [
                    'date'     => $timesheet->date->format('Y-m-d'),
                    'category' => $timesheet->category,
                    'hours'    => $timesheet->hours,
                ];
            }

            if ($workerHours > 0) {
                $results[] = [
                    'id'          => $worker->id,
                    'first_name'  => $worker->first_name,
                    'last_name'   => $worker->last_name,
                    'category'    => $worker->category, // Catégorie du worker (Ouvrier/ETAM)
                    'total_hours' => $workerHours,
                    'timesheets'  => $timesheetData,
                ];
            }
        }

        // Récupérer les intérimaires si la catégorie est vide ou 'interim'
        // Si le filtre est 'dubocq', 'worker' ou 'etam', on n'affiche pas les interims
        if (!$category || $category === 'interim') {
            $interimQuery = Interim::query();
            
            // Appliquer les mêmes filtres de timesheets/projets si possible
            // Note: Interim a une relation 'timesheets' morphMany comme Worker
            
            if ($timesheetCategory) {
                $interimQuery->whereHas('timesheets', function ($q) use ($timesheetCategory) {
                    $q->where('category', $timesheetCategory);
                });
            }

            if ($projectCategory) {
                $interimQuery->whereHas('timesheets.project', function ($q) use ($projectCategory) {
                    $q->where('category', $projectCategory);
                });
            }

            $interims = $interimQuery->get();

            foreach ($interims as $interim) {
                $interimHours = 0.0;
                $timesheetData = [];

                $timesheetsQuery = $interim->timesheets();

                if ($startDate && $endDate) {
                    $timesheetsQuery->whereBetween('date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $timesheetsQuery->where('date', '>=', $startDate);
                } elseif ($endDate) {
                    $timesheetsQuery->where('date', '<=', $endDate);
                }

                if ($timesheetCategory) {
                    $timesheetsQuery->where('category', $timesheetCategory);
                }
                
                if ($projectCategory) {
                    $timesheetsQuery->whereHas('project', function ($q) use ($projectCategory) {
                        $q->where('category', $projectCategory);
                    });
                }

                $timesheets = $timesheetsQuery->get();

                foreach ($timesheets as $timesheet) {
                    $interimHours += $timesheet->hours;
                    $timesheetData[] = [
                        'date'     => $timesheet->date->format('Y-m-d'),
                        'category' => $timesheet->category,
                        'hours'    => $timesheet->hours,
                    ];
                }

                if ($interimHours > 0) {
                    $results[] = [
                        'id'          => $interim->id,
                        'first_name'  => 'Intérim',
                        'last_name'   => $interim->agency,
                        'category'    => 'interim',
                        'total_hours' => $interimHours,
                        'timesheets'  => $timesheetData,
                    ];
                }
            }
        }

        Log::info("getWorkerHours - Returning " . count($results) . " workers");

        return $results;
    }
}
