<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interim;
use App\Models\Project;
use Illuminate\Http\Request;

class InterimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $interims = Interim::orderBy('agency')->paginate(10);
        return view('pages.admin.interims.index', compact('interims'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('status', 'active')->orderBy('name')->get();
        return view('pages.admin.interims.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agency' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'projects' => 'nullable|array',
            'projects.*' => 'exists:projects,id',
        ]);

        $interim = Interim::create([
            'agency' => $validated['agency'],
            'hourly_rate' => $validated['hourly_rate'],
            'status' => $validated['status'],
        ]);

        // Associer les projets sélectionnés à l'intérimaire
        if (isset($validated['projects'])) {
            $interim->projects()->attach($validated['projects']);
        }

        return redirect()->route('admin.interims.index')
            ->with('success', 'Intérimaire créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Interim $interim)
    {
        return view('pages.admin.interims.show', compact('interim'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Interim $interim)
    {
        $projects = Project::where('status', 'active')->orderBy('name')->get();
        $assignedProjects = $interim->projects->pluck('id')->toArray();

        return view('pages.admin.interims.edit', compact('interim', 'projects', 'assignedProjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Interim $interim)
    {
        $validated = $request->validate([
            'agency' => 'required|string|max:255',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'projects' => 'nullable|array',
            'projects.*' => 'exists:projects,id',
        ]);

        $interim->update([
            'agency' => $validated['agency'],
            'hourly_rate' => $validated['hourly_rate'],
            'status' => $validated['status'],
        ]);

        // Synchroniser les projets
        $interim->projects()->sync($validated['projects'] ?? []);

        return redirect()->route('admin.interims.index')
            ->with('success', 'Intérimaire mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Interim $interim)
    {
        // Vérifier si l'intérimaire a des pointages
        if ($interim->timesheets()->count() > 0) {
            return redirect()->route('admin.interims.index')
                ->with('error', 'Impossible de supprimer cet intérimaire car il a des pointages associés.');
        }

        // Détacher tous les projets avant la suppression
        $interim->projects()->detach();

        $interim->delete();

        return redirect()->route('admin.interims.index')
            ->with('success', 'Intérimaire supprimé avec succès.');
    }
}
