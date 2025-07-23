<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Project;

class UserController extends Controller
{
    /**
     * Get projects for authenticated user
     */
    public function projects(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // For admin/super-admin: return all active projects
        if ($user->hasRole(['admin', 'super-admin'])) {
            $projects = Project::with(['zone'])
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        } 
        // For other roles: return assigned projects
        else {
            $projects = $user->projects()
                ->with(['zone'])
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $projects
        ]);
    }
}