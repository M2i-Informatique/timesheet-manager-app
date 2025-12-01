<?php

namespace App\Services\Hours;

use App\Models\Project;
use App\Models\Worker;
use App\Models\Interim;
use Illuminate\Support\Facades\Log;

class ProjectHoursService
{
    /**
     * Récupère les heures des projets avec des filtres optionnels.
     *
     * Filtres disponibles :
     *   - id: ID du projet (facultatif)
     *   - category: Catégorie du projet (facultatif)
     *   - startDate: Date de début (facultatif)
     *   - endDate: Date de fin (facultatif)
     *
     * @param string|null $id
     * @param string|null $category
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getProjectHours(?string $id, ?string $category, ?string $startDate, ?string $endDate): array
    {
        $query = Project::query();

        if ($id) {
            $query->where('id', $id);
        }

        if ($category) {
            $query->where('category', $category);
        }

        $projects = $query->get();
        $results  = [];

        foreach ($projects as $project) {
            $projectTotalHours = 0.0;
            $workerTotalHours = 0.0;
            $interimTotalHours = 0.0;
            $workersData = [];
            $interimsData = [];

            // Récupérer les travailleurs et leurs feuilles de temps
            $workers = $project->workers()->with(['timesheets' => function ($query) use ($project, $startDate, $endDate) {
                $query->where('project_id', $project->id);
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->where('date', '>=', $startDate);
                } elseif ($endDate) {
                    $query->where('date', '<=', $endDate);
                }
            }])->get();

            // Récupérer les interims et leurs feuilles de temps
            $interims = $project->interims()->with(['timesheets' => function ($query) use ($project, $startDate, $endDate) {
                $query->where('project_id', $project->id);
                if ($startDate && $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                } elseif ($startDate) {
                    $query->where('date', '>=', $startDate);
                } elseif ($endDate) {
                    $query->where('date', '<=', $endDate);
                }
            }])->get();

            // Traiter les données des travailleurs
            foreach ($workers as $worker) {
                $workerHours = 0.0;
                $timesheetData = [];

                foreach ($worker->timesheets as $timesheet) {
                    $workerHours += $timesheet->hours;
                    $timesheetData[] = [
                        'date'     => $timesheet->date->format('Y-m-d'),
                        'category' => $timesheet->category,
                        'hours'    => $timesheet->hours,
                    ];
                }

                if ($workerHours > 0) {
                    $workersData[] = [
                        'id'          => $worker->id,
                        'first_name'  => $worker->first_name,
                        'last_name'   => $worker->last_name,
                        'category'    => $worker->category,
                        'total_hours' => $workerHours,
                        'timesheets'  => $timesheetData,
                    ];
                    $workerTotalHours += $workerHours;
                }
            }

            // Traiter les données des interims
            foreach ($interims as $interim) {
                $interimHours = 0.0;
                $timesheetData = [];

                foreach ($interim->timesheets as $timesheet) {
                    $interimHours += $timesheet->hours;
                    $timesheetData[] = [
                        'date'     => $timesheet->date->format('Y-m-d'),
                        'category' => $timesheet->category,
                        'hours'    => $timesheet->hours,
                    ];
                }

                if ($interimHours > 0) {
                    $interimsData[] = [
                        'id'          => $interim->id,
                        'agency'      => $interim->agency,
                        'hourly_rate' => $interim->hourly_rate,
                        'total_hours' => $interimHours,
                        'timesheets'  => $timesheetData,
                    ];
                    $interimTotalHours += $interimHours;
                }
            }

            $projectTotalHours = $workerTotalHours + $interimTotalHours;

            $results[] = [
                'id'                => $project->id,
                'code'              => $project->code,
                'category'          => $project->category,
                'name'              => $project->name,
                'total_hours'       => $projectTotalHours,
                'worker_hours'      => $workerTotalHours,
                'interim_hours'     => $interimTotalHours,
                'workers'           => $workersData,
                'interims'          => $interimsData,
            ];
        }

        return $results;
    }
}
