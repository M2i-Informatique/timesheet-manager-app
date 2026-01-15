<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\Project;
use App\Services\Hours\WorkerHoursService;
use App\Exports\WorkerYearlyExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class WorkerController extends Controller
{
    protected $workerHoursService;

    public function __construct(WorkerHoursService $workerHoursService)
    {
        $this->workerHoursService = $workerHoursService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupérer les filtres
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $projectCategory = $request->input('project_category'); // 'mh' ou 'go'

        $workers = Worker::orderBy('last_name')->paginate(10);

        // Calculer les heures pour les travailleurs de la page courante
        $workersHours = [];
        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');

        foreach ($workers as $worker) {
            // On utilise le service pour calculer les heures avec les filtres
            // Signature: getWorkerHours($id, $timesheetCategory, $startDate, $endDate, $projectCategory)
            $data = $this->workerHoursService->getWorkerHours(
                $worker->id,
                null, // Pas de filtre sur day/night
                $startDate,
                $endDate,
                $projectCategory // Filtre MH/GO
            );

            // Le service retourne un array, on prend le premier élément s'il existe
            if (!empty($data)) {
                $workersHours[$worker->id] = $data[0]['total_hours'];
            } else {
                $workersHours[$worker->id] = 0;
            }
        }

        return view('pages.admin.workers.index', compact('workers', 'month', 'year', 'projectCategory', 'workersHours'));
    }

    /**
     * Exporte le récapitulatif annuel des travailleurs.
     */
    public function exportYearly(Request $request)
    {
        $year = $request->input('year', now()->year);
        // On garde la catégorie dans le nom du fichier si elle est demandée, bien que l'export contienne tout
        $projectCategory = $request->input('project_category'); 

        $filename = 'recap_salaries_' . $year . '.xlsx';

        return Excel::download(new WorkerYearlyExport($year), $filename);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('status', 'active')->orderBy('name')->get();
        return view('pages.admin.workers.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'category' => 'required|in:worker,etam',
            'contract_hours' => 'required|integer|min:1',
            'monthly_salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'projects' => 'nullable|array',
            'projects.*' => 'exists:projects,id',
        ]);

        $worker = Worker::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'category' => $validated['category'],
            'contract_hours' => $validated['contract_hours'],
            'monthly_salary' => $validated['monthly_salary'],
            'status' => $validated['status'],
        ]);

        // Associer les projets sélectionnés au travailleur
        if (isset($validated['projects'])) {
            $worker->projects()->attach($validated['projects']);
        }

        return redirect()->route('admin.workers.index')
            ->with('success', 'Travailleur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Worker $worker)
    {
        return view('pages.admin.workers.show', compact('worker'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Worker $worker)
    {
        $projects = Project::where('status', 'active')->orderBy('name')->get();
        $assignedProjects = $worker->projects->pluck('id')->toArray();

        return view('pages.admin.workers.edit', compact('worker', 'projects', 'assignedProjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Worker $worker)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'category' => 'required|in:worker,etam',
            'contract_hours' => 'required|integer|min:1',
            'monthly_salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'projects' => 'nullable|array',
            'projects.*' => 'exists:projects,id',
        ]);

        $worker->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'category' => $validated['category'],
            'contract_hours' => $validated['contract_hours'],
            'monthly_salary' => $validated['monthly_salary'],
            'status' => $validated['status'],
        ]);

        // Synchroniser les projets
        $worker->projects()->sync($validated['projects'] ?? []);

        return redirect()->route('admin.workers.index')
            ->with('success', 'Travailleur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Worker $worker)
    {
        // Vérifier si le travailleur a des pointages
        if ($worker->timesheets()->count() > 0) {
            return redirect()->route('admin.workers.index')
                ->with('error', 'Impossible de supprimer ce travailleur car il a des pointages associés.');
        }

        // Détacher tous les projets avant la suppression
        $worker->projects()->detach();

        $worker->delete();

        return redirect()->route('admin.workers.index')
            ->with('success', 'Travailleur supprimé avec succès.');
    }
}
