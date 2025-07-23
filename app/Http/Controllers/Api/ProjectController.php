<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Project;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::with(['zone'])
            ->where('status', 'active')
            ->orderBy('name');

        // Pagination
        $perPage = $request->get('per_page', 15);
        $projects = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'data' => $projects->items(),
                'current_page' => $projects->currentPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
                'last_page' => $projects->lastPage(),
                'from' => $projects->firstItem(),
                'to' => $projects->lastItem(),
            ]
        ]);
    }

    /**
     * Display the specified project
     */
    public function show(Project $project): JsonResponse
    {
        $project->load(['zone']);

        return response()->json([
            'status' => 'success',
            'data' => $project
        ]);
    }
}