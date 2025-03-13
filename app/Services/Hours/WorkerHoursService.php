<?php

namespace App\Services\Hours;

use App\Models\Worker;
use Illuminate\Support\Facades\Log;

class WorkerHoursService
{
    /**
     * Calculate a detailed breakdown of hours for workers.
     *
     * Filters:
     *   - id: Optional worker ID
     *   - category: Optional timesheet category filter (e.g. "day" or "night")
     *   - startDate: Optional start date filter
     *   - endDate: Optional end date filter
     *
     * @param string|null $id
     * @param string|null $category
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getWorkerHours(?string $id, ?string $category, ?string $startDate, ?string $endDate): array
    {
        Log::info("getWorkerHours - Start with filters: ID={$id}, Category={$category}, StartDate={$startDate}, EndDate={$endDate}");

        $query = Worker::query();

        if ($id) {
            $query->where('id', $id);
        }

        // Instead of filtering the worker's category,
        // we filter on the timesheet category if provided.
        if ($category) {
            $query->whereHas('timesheets', function ($q) use ($category) {
                $q->where('category', $category);
            });
        }

        $workers = $query->get();
        $results = [];

        Log::info("Found " . $workers->count() . " workers matching filters");

        foreach ($workers as $worker) {
            Log::info("Processing Worker ID: {$worker->id}, Name: {$worker->first_name} {$worker->last_name}");

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

            // Optionally, if you want to also apply the filter on the timesheet category here:
            if ($category) {
                $timesheetsQuery->where('category', $category);
            }

            $timesheets = $timesheetsQuery->get();

            Log::info("  Found " . $timesheets->count() . " timesheets for worker");

            foreach ($timesheets as $timesheet) {
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
                $results[] = [
                    'id'          => $worker->id,
                    'first_name'  => $worker->first_name,
                    'last_name'   => $worker->last_name,
                    'category'    => $worker->category,
                    'total_hours' => $workerHours,
                    'timesheets'  => $timesheetData,
                ];
            }
        }

        Log::info("getWorkerHours - Returning " . count($results) . " workers");

        return $results;
    }
}
