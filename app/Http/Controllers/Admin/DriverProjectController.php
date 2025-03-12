<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;

class DriverProjectController extends Controller
{
    /**
     * Afficher la page d'attribution des projets aux drivers.
     */
    public function index()
    {
        $drivers = User::role('driver')->with('projects')->get();
        $projects = Project::where('status', 'active')->get();

        return view('pages.admin.driver-projects.index', compact('drivers', 'projects'));
    }

    /**
     * Afficher la page pour attribuer des projets à un driver spécifique.
     */
    public function edit(User $driver)
    {
        // Vérifier que l'utilisateur est bien un driver
        if (!$driver->hasRole('driver')) {
            return redirect()->route('admin.driver-projects.index')
                ->with('error', 'Cet utilisateur n\'est pas un driver.');
        }

        $projects = Project::where('status', 'active')->get();
        $assignedProjects = $driver->projects->pluck('id')->toArray();

        return view('pages.admin.driver-projects.edit', compact('driver', 'projects', 'assignedProjects'));
    }

    /**
     * Mettre à jour les projets attribués à un driver.
     */
    public function update(Request $request, User $driver)
    {
        // Vérifier que l'utilisateur est bien un driver
        if (!$driver->hasRole('driver')) {
            return redirect()->route('admin.driver-projects.index')
                ->with('error', 'Cet utilisateur n\'est pas un driver.');
        }

        $validated = $request->validate([
            'projects' => 'nullable|array',
            'projects.*' => 'exists:projects,id',
        ]);

        // Synchroniser les projets
        $driver->projects()->sync($validated['projects'] ?? []);

        return redirect()->route('admin.driver-projects.index')
            ->with('success', 'Projets attribués avec succès.');
    }
}
