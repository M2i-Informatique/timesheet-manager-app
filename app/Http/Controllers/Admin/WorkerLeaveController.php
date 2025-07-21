<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkerLeave;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class WorkerLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = WorkerLeave::with(['worker', 'createdBy']);

        // Filtrage par salarié
        if ($request->filled('worker_id')) {
            $query->where('worker_id', $request->worker_id);
        }

        // Filtrage par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtrage par période
        if ($request->filled('start_date')) {
            $query->where('end_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('start_date', '<=', $request->end_date);
        }

        $leaves = $query->orderBy('start_date', 'desc')->paginate(15);

        // Données pour les filtres
        $workers = Worker::orderBy('last_name')->get();
        $types = WorkerLeave::getTypes();

        return view('pages.admin.worker-leaves.index', compact('leaves', 'workers', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $workers = Worker::orderBy('last_name')->get();
        $types = WorkerLeave::getTypes();

        return view('pages.admin.worker-leaves.create', compact('workers', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'type' => 'required|in:' . implode(',', array_keys(WorkerLeave::getTypes())),
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500'
        ]);

        $validated['created_by'] = Auth::id();

        // Vérifier qu'il n'y a pas de chevauchement avec d'autres congés
        $overlap = WorkerLeave::where('worker_id', $validated['worker_id'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                      });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'dates' => 'Ces dates chevauchent avec un congé existant pour ce salarié.'
            ])->withInput();
        }

        WorkerLeave::create($validated);

        return redirect()->route('admin.worker-leaves.index')
            ->with('success', 'Congé créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkerLeave $workerLeave): View
    {
        $workerLeave->load(['worker', 'createdBy']);
        
        return view('pages.admin.worker-leaves.show', compact('workerLeave'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkerLeave $workerLeave): View
    {
        $workers = Worker::orderBy('last_name')->get();
        $types = WorkerLeave::getTypes();

        return view('pages.admin.worker-leaves.edit', compact('workerLeave', 'workers', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkerLeave $workerLeave): RedirectResponse
    {
        $validated = $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'type' => 'required|in:' . implode(',', array_keys(WorkerLeave::getTypes())),
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500'
        ]);

        // Vérifier qu'il n'y a pas de chevauchement avec d'autres congés (exclure le congé actuel)
        $overlap = WorkerLeave::where('worker_id', $validated['worker_id'])
            ->where('id', '!=', $workerLeave->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                      });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'dates' => 'Ces dates chevauchent avec un congé existant pour ce salarié.'
            ])->withInput();
        }

        $workerLeave->update($validated);

        return redirect()->route('admin.worker-leaves.index')
            ->with('success', 'Congé modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkerLeave $workerLeave): RedirectResponse
    {
        $workerLeave->delete();

        return redirect()->route('admin.worker-leaves.index')
            ->with('success', 'Congé supprimé avec succès.');
    }

    /**
     * Afficher les congés d'un salarié spécifique
     */
    public function workerLeaves(Worker $worker): View
    {
        $leaves = WorkerLeave::forWorker($worker->id)
            ->with('createdBy')
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return view('pages.admin.worker-leaves.worker-leaves', compact('worker', 'leaves'));
    }
}