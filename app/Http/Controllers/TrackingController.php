<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Worker;
use App\Models\Interim;
use App\Models\TimeSheetable;
use App\Models\NonWorkingDay;
use App\Models\WorkerLeave;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\Costs\CostsCalculator;

class TrackingController extends Controller
{
    /**
     * Page d'accueil (sélection projet + mois+année).
     */
    public function index()
    {
        $projects = Project::where('status', 'active')->orderBy('code')->get();
        $month = now()->month;
        $year  = now()->year;

        return view('pages.tracking.index', compact('projects', 'month', 'year'));
    }

    /**
     * Affiche la page de saisie.
     */
    public function show(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'month'      => 'required|integer|min:1|max:12',
            'year'       => 'required|integer|min:1900|max:2099',
            'category'   => 'nullable|in:day,night'
        ]);

        $project  = Project::findOrFail($request->project_id);
        $month    = $request->month;
        $year     = $request->year;
        $category = $request->input('category', 'day'); // "day" ou "night"
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // 1) Récupérer les Workers & Interims assignés au projet
        $workers  = $project->workers()->where('status', 'active')->get();
        $interims = $project->interims()->where('status', 'active')->get();

        // Construire entriesData pour Handsontable
        $entriesData = [];
        foreach ($workers as $w) {
            $entriesData[] = [
                'id'         => $w->id,
                'model_type' => 'worker',
                'full_name'  => $w->first_name . ' ' . $w->last_name
            ];
        }
        foreach ($interims as $i) {
            $entriesData[] = [
                'id'         => $i->id,
                'model_type' => 'interim',
                'full_name'  => $i->agency . ' (Intérim)'
            ];
        }

        // Charger time_sheetables pour ce projet, ce mois, cette année et catégorie
        $timeSheets = TimeSheetable::where('project_id', $project->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('category', $category)
            ->whereIn('timesheetable_type', [Worker::class, Interim::class])
            ->get();

        // Remplir days pour chaque entrée
        foreach ($entriesData as &$entry) {
            $days = array_fill(1, $daysInMonth, null);
            $className = ($entry['model_type'] === 'worker') ? Worker::class : Interim::class;
            $filtered = $timeSheets->where('timesheetable_id', $entry['id'])
                ->where('timesheetable_type', $className);
            foreach ($filtered as $ts) {
                $dayNum = (int)$ts->date->format('j');
                $days[$dayNum] = floatval($ts->hours);
            }
            $entry['days'] = $days;
        }
        unset($entry);

        // 2) Récupérer la liste des Workers / Interims disponibles (non assignés)
        $availableWorkers = Worker::where('status', 'active')
            ->whereDoesntHave('projects', function ($q) use ($project) {
                $q->where('projects.id', $project->id);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $availableInterims = Interim::where('status', 'active')
            ->whereDoesntHave('projects', function ($q) use ($project) {
                $q->where('projects.id', $project->id);
            })
            ->get();

        // 3) Récap : total "day" et "night" pour ce mois
        $recap = [];
        // Workers
        foreach ($workers as $w) {
            $daySum = TimeSheetable::where('project_id', $project->id)
                ->where('timesheetable_id', $w->id)
                ->where('timesheetable_type', Worker::class)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('category', 'day')
                ->sum('hours');

            $nightSum = TimeSheetable::where('project_id', $project->id)
                ->where('timesheetable_id', $w->id)
                ->where('timesheetable_type', Worker::class)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('category', 'night')
                ->sum('hours');

            $recap[] = [
                'id'          => $w->id,
                'model_type'  => 'worker',
                'full_name'   => $w->first_name . ' ' . $w->last_name,
                'day_hours'   => $daySum,
                'night_hours' => $nightSum,
                'total'       => $daySum + $nightSum,
            ];
        }
        // Interims
        foreach ($interims as $i) {
            $daySum = TimeSheetable::where('project_id', $project->id)
                ->where('timesheetable_id', $i->id)
                ->where('timesheetable_type', Interim::class)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('category', 'day')
                ->sum('hours');

            $nightSum = TimeSheetable::where('project_id', $project->id)
                ->where('timesheetable_id', $i->id)
                ->where('timesheetable_type', Interim::class)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('category', 'night')
                ->sum('hours');

            $recap[] = [
                'id'          => $i->id,
                'model_type'  => 'interim',
                'full_name'   => $i->agency . ' (Intérim)',
                'day_hours'   => $daySum,
                'night_hours' => $nightSum,
                'total'       => $daySum + $nightSum,
            ];
        }

        // Calcul des KPI à partir du recap
        $totalWorkerHours = 0;
        $totalInterimHours = 0;

        foreach ($recap as $r) {
            if ($r['model_type'] === 'worker') {
                $totalWorkerHours += $r['day_hours'] + $r['night_hours'];
            } else { // interim
                $totalInterimHours += $r['day_hours'] + $r['night_hours'];
            }
        }

        $totalHoursCurrentMonth = $totalWorkerHours + $totalInterimHours;
        $totalWorkerHoursCurrentMonth = $totalWorkerHours;

        // Définir les bornes de la période
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = \Carbon\Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');

        // Instancier le service
        $calculator = new CostsCalculator();

        // Utiliser le service pour calculer le coût des workers uniquement
        $costData = $calculator->calculateTotalCostForProject($project, $startDate, $endDate);
        $costWorkerTotal = $costData['cost'];

        $totalInterimHoursCurrentMonth = $totalInterimHours;

        // Calcul du mois précédent et suivant
        $prevMonth = $month - 1;
        $prevYear  = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }
        $nextMonth = $month + 1;
        $nextYear  = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        // Récupérer les jours non travaillés pour ce mois
        $nonWorkingDays = NonWorkingDay::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $formattedDays = $nonWorkingDays->pluck('date')
            ->map(function ($date) {
                return (int)$date->format('j');
            })
            ->toArray();

        $nonWorkingDayTypes = NonWorkingDay::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->mapWithKeys(function ($day) {
                return [(int)$day->date->format('j') => $day->type];
            })
            ->toArray();

        // Récupérer les congés des salariés pour ce mois
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();
        
        $workerLeaves = WorkerLeave::with('worker')
            ->inPeriod($startOfMonth, $endOfMonth)
            ->get();

        // Formater les congés par worker et par jour
        $workerLeaveDays = [];
        $workerLeaveTypes = [];
        foreach ($workerLeaves as $leave) {
            $startDate = max($leave->start_date, Carbon::create($year, $month, 1));
            $endDate = min($leave->end_date, Carbon::create($year, $month, 1)->endOfMonth());
            
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $day = (int)$current->format('j');
                $workerLeaveDays[$leave->worker_id][$day] = $leave->type_code;
                $workerLeaveTypes[$leave->worker_id][$day] = [
                    'type' => $leave->type,
                    'code' => $leave->type_code,
                    'color' => $leave->type_color
                ];
                $current->addDay();
            }
        }

        return view('pages.tracking.show', [
            'project'           => $project,
            'month'             => $month,
            'year'              => $year,
            'category'          => $category,
            'daysInMonth'       => $daysInMonth,
            'entriesData'       => $entriesData,
            'availableWorkers'  => $availableWorkers,
            'availableInterims' => $availableInterims,
            'recap'             => $recap,
            'totalHoursCurrentMonth'       => $totalHoursCurrentMonth,
            'totalWorkerHoursCurrentMonth' => $totalWorkerHoursCurrentMonth,
            'totalInterimHoursCurrentMonth' => $totalInterimHoursCurrentMonth,
            'prevMonth'         => $prevMonth,
            'prevYear'          => $prevYear,
            'nextMonth'         => $nextMonth,
            'nextYear'          => $nextYear,
            'nonWorkingDays'    => $formattedDays,
            'nonWorkingDayTypes' => $nonWorkingDayTypes,
            'costWorkerTotal'   => $costWorkerTotal,
            'workerLeaveDays'   => $workerLeaveDays,
            'workerLeaveTypes'  => $workerLeaveTypes,

        ]);
    }

    /**
     * Enregistre la saisie (ne stocke pas "vide" -> 0)
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'month'      => 'required|integer|min:1|max:12',
            'year'       => 'required|integer|min:1900|max:2099',
            'category'   => 'required|in:day,night',
            'data'       => 'required|array',
        ]);

        $projectId = $request->project_id;
        $month     = $request->month;
        $year      = $request->year;
        $category  = $request->category; // "day" / "night"
        $otherCat  = ($category === 'day') ? 'night' : 'day';

        foreach ($request->data as $entry) {
            $empId     = $entry['id'];
            $modelType = $entry['model_type']; // 'worker' / 'interim'
            $className = ($modelType === 'worker') ? Worker::class : Interim::class;

            foreach ($entry['days'] as $dayNum => $value) {
                // value peut être null (cellule vide) ou un nombre
                if ($value === null) {
                    // => l'utilisateur n'a rien saisi => on supprime s'il existait
                    $ts = TimeSheetable::where('project_id', $projectId)
                        ->where('timesheetable_id', $empId)
                        ->where('timesheetable_type', $className)
                        ->whereDay('date', $dayNum)
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year)
                        ->where('category', $category)
                        ->first();
                    if ($ts) {
                        $ts->delete();
                    }
                    continue;
                }

                // ICI $value n'est pas null => c'est un nombre (0..12)
                $hours = floatval($value);
                if ($hours < 0) {
                    $hours = 0;
                }

                // Vérif day + night <= 12
                $date  = Carbon::create($year, $month, $dayNum)->format('Y-m-d');
                $other = TimeSheetable::where('project_id', $projectId)
                    ->where('timesheetable_id', $empId)
                    ->where('timesheetable_type', $className)
                    ->where('date', $date)
                    ->where('category', $otherCat)
                    ->first();

                $otherHours = $other ? floatval($other->hours) : 0;
                if (($hours + $otherHours) > 12) {
                    return response()->json([
                        'success' => false,
                        'message' => "Somme jour+nuit >12h le " . date('d/m/Y', strtotime($date))
                    ], 422);
                }

                // Upsert
                $ts = TimeSheetable::firstOrNew([
                    'project_id' => $projectId,
                    'timesheetable_id' => $empId,
                    'timesheetable_type' => $className,
                    'date' => $date,
                    'category' => $category
                ]);
                $ts->hours = $hours;
                $ts->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Données sauvegardées avec succès.'
        ]);
    }

    /**
     * Attacher un Worker ou un Interim au projet
     */
    public function assignEmployee(Request $request)
    {
        $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'employee_type' => 'required|in:worker,interim',
            'employee_id'   => 'required|integer',
            'month'         => 'required|integer|min:1|max:12',
            'year'          => 'required|integer|min:1900|max:2099',
            'category'      => 'required|in:day,night'
        ]);

        $project = Project::findOrFail($request->project_id);

        if ($request->employee_type === 'worker') {
            $project->workers()->syncWithoutDetaching([$request->employee_id]);
        } else {
            $project->interims()->syncWithoutDetaching([$request->employee_id]);
        }

        return redirect()->route('tracking.show', [
            'project_id' => $project->id,
            'month'      => $request->month,
            'year'       => $request->year,
            'category'   => $request->category
        ])->with('success', 'Employé assigné avec succès.');
    }

    /**
     * Détacher un employé (Worker ou Interim) du projet
     * (et effacer ses heures sur ce mois).
     */
    public function detachEmployee(Request $request)
    {
        $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'employee_type' => 'required|in:worker,interim',
            'employee_id'   => 'required|integer',
            'month'         => 'required|integer|min:1|max:12',
            'year'          => 'required|integer|min:1900|max:2099',
            'category'      => 'required|in:day,night'
        ]);

        $project = Project::findOrFail($request->project_id);

        // Retirer du pivot
        if ($request->employee_type === 'worker') {
            $project->workers()->detach($request->employee_id);
        } else {
            $project->interims()->detach($request->employee_id);
        }

        // Supprimer ses heures ce mois
        $className = ($request->employee_type === 'worker') ? Worker::class : Interim::class;
        $startDate = Carbon::create($request->year, $request->month, 1);
        $endDate   = $startDate->copy()->endOfMonth();

        TimeSheetable::where('project_id', $project->id)
            ->where('timesheetable_id', $request->employee_id)
            ->where('timesheetable_type', $className)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->delete();

        return redirect()->route('tracking.show', [
            'project_id' => $project->id,
            'month' => $request->month,
            'year' => $request->year,
            'category' => $request->category
        ])->with('success', 'Employé détaché du projet avec succès.');
    }
}
