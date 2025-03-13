<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Zone;
use App\Models\Worker;
use App\Models\Interim;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with('zone')->orderBy('code')->paginate(10);
        return view('pages.admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $zones = Zone::orderBy('name')->get();
        $workers = Worker::where('status', 'active')->orderBy('last_name')->get();
        $interims = Interim::where('status', 'active')->orderBy('agency')->get();
        $drivers = User::role('driver')->orderBy('last_name')->get();

        return view('pages.admin.projects.create', compact('zones', 'workers', 'interims', 'drivers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|integer|unique:projects,code',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'distance' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'workers' => 'nullable|array',
            'workers.*' => 'exists:workers,id',
            'interims' => 'nullable|array',
            'interims.*' => 'exists:interims,id',
            'drivers' => 'nullable|array',
            'drivers.*' => 'exists:users,id',
        ]);

        // Créer le projet
        $project = Project::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'distance' => $validated['distance'],
            'status' => $validated['status'],
        ]);

        // Associer les travailleurs au projet
        if (isset($validated['workers'])) {
            $project->workers()->attach($validated['workers']);
        }

        // Associer les intérimaires au projet
        if (isset($validated['interims'])) {
            $project->interims()->attach($validated['interims']);
        }

        // Associer les drivers au projet
        if (isset($validated['drivers'])) {
            $project->drivers()->attach($validated['drivers']);
        }

        return redirect()->route('admin.projects.index')
            ->with('success', 'Projet créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['zone', 'workers', 'interims', 'drivers', 'timesheets' => function ($query) {
            $query->latest('date')->take(10);
        }]);

        return view('pages.admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $zones = Zone::orderBy('name')->get();
        $workers = Worker::where('status', 'active')->orderBy('last_name')->get();
        $interims = Interim::where('status', 'active')->orderBy('agency')->get();
        $drivers = User::role('driver')->orderBy('last_name')->get();

        $assignedWorkers = $project->workers->pluck('id')->toArray();
        $assignedInterims = $project->interims->pluck('id')->toArray();
        $assignedDrivers = $project->drivers->pluck('id')->toArray();

        return view('pages.admin.projects.edit', compact('project', 'zones', 'workers', 'interims', 'drivers', 'assignedWorkers', 'assignedInterims', 'assignedDrivers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'code' => 'required|integer|unique:projects,code,' . $project->id,
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'distance' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'workers' => 'nullable|array',
            'workers.*' => 'exists:workers,id',
            'interims' => 'nullable|array',
            'interims.*' => 'exists:interims,id',
            'drivers' => 'nullable|array',
            'drivers.*' => 'exists:users,id',
        ]);

        // Mettre à jour le projet
        $project->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'distance' => $validated['distance'],
            'status' => $validated['status'],
        ]);

        // Synchroniser les travailleurs
        $project->workers()->sync($validated['workers'] ?? []);

        // Synchroniser les intérimaires
        $project->interims()->sync($validated['interims'] ?? []);

        // Synchroniser les drivers
        $project->drivers()->sync($validated['drivers'] ?? []);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Projet mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // Vérifier si le projet a des pointages
        if ($project->timesheets()->count() > 0) {
            return redirect()->route('admin.projects.index')
                ->with('error', 'Impossible de supprimer ce projet car il a des pointages associés.');
        }

        // Détacher toutes les relations avant la suppression
        $project->workers()->detach();
        $project->interims()->detach();
        $project->drivers()->detach();

        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Projet supprimé avec succès.');
    }
}
