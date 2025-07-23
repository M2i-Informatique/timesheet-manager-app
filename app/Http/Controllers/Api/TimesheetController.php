<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TimesheetExport;

class TimesheetController extends Controller
{
    /**
     * Get timesheet data for a project/month/year
     */
    public function show(Request $request, Project $project, int $year, int $month): JsonResponse
    {
        $user = $request->user();
        
        // Check if user has access to this project
        if (!$user->hasRole(['admin', 'super-admin']) && !$user->projects->contains($project)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès non autorisé à ce projet.'
            ], 403);
        }

        // Get days in month
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        // Generate daily entries structure
        $dailyEntries = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            
            $dailyEntries[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->locale('fr')->isoFormat('dddd'),
                'is_weekend' => $date->isWeekend(),
                'entries' => [
                    'day' => ['hours' => 0, 'description' => ''],
                    'night' => ['hours' => 0, 'description' => '']
                ],
                'total_hours' => 0
            ];
        }

        // Here you would fetch actual timesheet data from database
        // For now, return empty structure

        $monthlyData = [
            'month' => $month,
            'year' => $year,
            'project_id' => $project->id,
            'project' => $project->load(['zone']),
            'daily_entries' => $dailyEntries,
            'total_hours' => 0,
            'total_days' => 0
        ];

        return response()->json([
            'status' => 'success',
            'data' => $monthlyData
        ]);
    }

    /**
     * Store a newly created timesheet entry
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0|max:24',
            'category' => 'required|in:day,night',
            'description' => 'nullable|string|max:500'
        ]);

        // Here you would create the timesheet entry
        // For now, return success response

        return response()->json([
            'status' => 'success',
            'message' => 'Entrée sauvegardée avec succès',
            'data' => [
                'id' => rand(1, 1000),
                'project_id' => $request->project_id,
                'date' => $request->date,
                'hours' => $request->hours,
                'category' => $request->category,
                'description' => $request->description,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Update the specified timesheet entry
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'hours' => 'required|numeric|min:0|max:24',
            'description' => 'nullable|string|max:500'
        ]);

        // Here you would update the timesheet entry
        // For now, return success response

        return response()->json([
            'status' => 'success',
            'message' => 'Entrée mise à jour avec succès',
            'data' => [
                'id' => $id,
                'hours' => $request->hours,
                'description' => $request->description,
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Remove the specified timesheet entry
     */
    public function destroy(int $id): JsonResponse
    {
        // Here you would delete the timesheet entry
        // For now, return success response

        return response()->json([
            'status' => 'success',
            'message' => 'Entrée supprimée avec succès'
        ]);
    }

    /**
     * Export timesheet to Excel
     */
    public function export(Request $request, Project $project, int $year, int $month): Response
    {
        $user = $request->user();
        
        // Check if user has access to this project
        if (!$user->hasRole(['admin', 'super-admin']) && !$user->projects->contains($project)) {
            abort(403, 'Accès non autorisé à ce projet.');
        }

        // Here you would use your existing TimesheetExport class
        // For now, return a simple response
        
        $filename = "timesheet_{$project->code}_{$year}_{$month}.xlsx";
        
        return response()->json([
            'status' => 'success',
            'message' => 'Export en cours de préparation',
            'filename' => $filename
        ]);
    }
}