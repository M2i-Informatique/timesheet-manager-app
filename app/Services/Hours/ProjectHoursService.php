<?php

namespace App\Services\Hours;

use App\Models\Project;
use Illuminate\Support\Facades\Log;

class ProjectHoursService
{
    /**
     * Calculate a detailed breakdown of hours for projects.
     *
     * Filters:
     *   - id: Optional project ID
     *   - category: Optional project category
     *   - startDate: Optional start date filter
     *   - endDate: Optional end date filter
     *
     * @param string|null $id
     * @param string|null $category
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getProjectHours(?string $id, ?string $category, ?string $startDate, ?string $endDate): array
    {
        Log::info("getProjectHours - Start with filters: ID={$id}, Category={$category}, StartDate={$startDate}, EndDate={$endDate}");

        $query = Project::query();

        if ($id) {
            $query->where('id', $id);
        }

        if ($category) {
            $query->where('category', $category);
        }

        $projects = $query->get();
        $results  = [];

        Log::info("Found " . $projects->count() . " projects matching filters");

        foreach ($projects as $project) {
            Log::info("Processing Project ID: {$project->id}, Name: {$project->name}");

            $projectHours = 0.0;
            $workersData  = [];

            // Retrieve workers and their timesheets (with date filtering)
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

            Log::info("Found " . $workers->count() . " workers for this project");

            foreach ($workers as $worker) {
                Log::info("  Worker: {$worker->id} - {$worker->first_name} {$worker->last_name}");
                Log::info("  Timesheets count: " . $worker->timesheets->count());

                $workerHours   = 0.0;
                $timesheetData = [];

                foreach ($worker->timesheets as $timesheet) {
                    Log::info("    Timesheet ID: {$timesheet->id}, Project ID: {$timesheet->project_id}, Date: {$timesheet->date}, Category: {$timesheet->category}, Hours: {$timesheet->hours}");

                    $workerHours += $timesheet->hours;
                    $timesheetData[] = [
                        'date'     => $timesheet->date->format('Y-m-d'),
                        'category' => $timesheet->category,
                        'hours'    => $timesheet->hours,
                    ];
                }

                Log::info("  Worker total hours: {$workerHours}");

                if ($workerHours > 0) {
                    $workersData[] = [
                        'id'          => $worker->id,
                        'first_name'  => $worker->first_name,
                        'last_name'   => $worker->last_name,
                        'total_hours' => $workerHours,
                        'timesheets'  => $timesheetData,
                    ];
                    $projectHours += $workerHours;
                }
            }

            Log::info("Project total hours: {$projectHours}");

            $results[] = [
                'id'          => $project->id,
                'code'        => $project->code,
                'category'    => $project->category,
                'name'        => $project->name,
                'total_hours' => $projectHours,
                'workers'     => $workersData,
            ];
        }

        Log::info("getProjectHours - Returning " . count($results) . " projects");

        return $results;
    }
}
