<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $zones = Zone::orderBy('min_km')->paginate(10);
        return view('pages.admin.zones.index', compact('zones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.zones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'min_km' => 'required|numeric|min:0',
            'max_km' => 'nullable|numeric|gt:min_km',
            'rate' => 'required|numeric|min:0',
        ]);

        Zone::create($validated);

        return redirect()->route('admin.zones.index')
            ->with('success', 'Zone créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Zone $zone)
    {
        $zone->load('projects');
        return view('admin.zones.show', compact('zone'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Zone $zone)
    {
        return view('pages.admin.zones.edit', compact('zone'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'min_km' => 'required|numeric|min:0',
            'max_km' => 'nullable|numeric|min:0',
            'rate' => 'required|numeric|min:0',
        ]);

        $zone->update($validated);

        return redirect()->route('admin.zones.index')
            ->with('success', 'Zone mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Zone $zone)
    {
        // Vérifier si la zone est utilisée par des projets
        if ($zone->projects()->count() > 0) {
            return redirect()->route('admin.zones.index')
                ->with('error', 'Impossible de supprimer cette zone car elle est utilisée par des projets.');
        }

        $zone->delete();

        return redirect()->route('admin.zones.index')
            ->with('success', 'Zone supprimée avec succès.');
    }
}
