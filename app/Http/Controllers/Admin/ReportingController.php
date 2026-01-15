<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Worker;
use App\Models\Interim;
use App\Models\Setting;
use App\Models\TimeSheetable;
use App\Services\Costs\ProjectCostsService;
use App\Services\Costs\WorkerCostsService;
use App\Services\Hours\ProjectHoursService;
use App\Services\Hours\WorkerHoursService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;

class ReportingController extends Controller
{
    protected ProjectCostsService $projectCostsService;
    protected WorkerCostsService $workerCostsService;
    protected ProjectHoursService $projectHoursService;
    protected WorkerHoursService $workerHoursService;

    /**
     * Constructor with dependency injection
     */
    public function __construct(
        ProjectCostsService $projectCostsService,
        WorkerCostsService  $workerCostsService,
        ProjectHoursService $projectHoursService,
        WorkerHoursService  $workerHoursService
    ) {
        $this->projectCostsService = $projectCostsService;
        $this->workerCostsService = $workerCostsService;
        $this->projectHoursService = $projectHoursService;
        $this->workerHoursService = $workerHoursService;
    }

    /**
     * Display the reporting dashboard
     */
    public function index(Request $request)
    {
        // Vérifier si l'affichage du tableau de bord est demandé
        if ($request->input('view') === 'dashboard' || !$request->has('report_type')) {
            return $this->dashboard($request);
        }

        $projects = Project::where('status', 'active')->orderBy('code')->get();
        $workers = Worker::where('status', 'active')->orderBy('last_name')->get();

        // Par défaut, le mois en cours
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $projectId = $request->input('project_id');
        $workerId  = $request->input('worker_id');
        $category  = $request->input('category');

        $reportType = $request->input('report_type', 'project_hours');

        $reportData = [];
        $chartData  = [];

        switch ($reportType) {
            case 'project_hours':
                $reportData = $this->projectHoursService->getProjectHours($projectId, $category, $startDate, $endDate);

                // FILTRAGE POUR NE PAS AFFICHER LES PROJETS AVEC 0 HEURES
                $reportData = array_filter($reportData, function ($project) {
                    return isset($project['total_hours']) && $project['total_hours'] > 0;
                });

                // TRIAGE DES PROJETS PAR CODE
                usort($reportData, function ($a, $b) {
                    return $a['code'] <=> $b['code'];
                });

                $chartData = $this->prepareProjectHoursChartData($reportData);
                break;

            case 'worker_hours':
                // Passer null comme catégorie de timesheet, null comme projectCategory, et $category pour filtrer les workers
                $reportData = $this->workerHoursService->getWorkerHours($workerId, null, $startDate, $endDate, null, $category);

                // TRIAGE DES TRAVAILLEURS PAR NOM DE FAMILLE
                usort($reportData, function ($a, $b) {
                    return $a['last_name'] <=> $b['last_name'];
                });

                $chartData = $this->prepareWorkerHoursChartData($reportData);
                break;

            case 'project_costs':
                $reportData = $this->projectCostsService->getProjectCosts($projectId, $category, $startDate, $endDate);
                $chartData = $this->prepareProjectCostsChartData($reportData);
                break;

            case 'worker_costs':
                // Passer $category pour filtrer par catégorie de travailleur
                $reportData = $this->workerCostsService->getWorkerCosts($workerId, $category, $startDate, $endDate);

                // TRIAGE DES TRAVAILLEURS PAR NOM DE FAMILLE
                usort($reportData, function ($a, $b) {
                    return $a['last_name'] <=> $b['last_name'];
                });

                $chartData = $this->prepareWorkerCostsChartData($reportData);
                break;
        }

        // FILTRAGE POUR AFFICHER "Aucune donnée disponible" QUAND IL N'Y A PAS DE COÛT
        if ($reportType === 'project_costs') {
            $reportData = array_filter($reportData, function ($project) {
                return isset($project['total_cost']) && $project['total_cost'] > 0;
            });
        }

        // Calcul des pourcentages de variation par rapport au mois précédent
        $currentMonthStart  = Carbon::parse($startDate)->startOfMonth();
        $currentMonthEnd    = Carbon::parse($endDate)->endOfMonth();
        $previousMonthStart = Carbon::parse($startDate)->subMonth()->startOfMonth();
        $previousMonthEnd   = Carbon::parse($startDate)->subMonth()->endOfMonth();

        // Obtenir les classes pour les requêtes
        $workerClass = get_class(new Worker());
        $interimClass = get_class(new Interim());

        // Heures salariés
        $totalWorkerHoursCurrentMonth = TimeSheetable::whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('timesheetable_type', $workerClass)
            ->sum('hours');

        $totalWorkerHoursPreviousMonth = TimeSheetable::whereBetween('date', [$previousMonthStart, $previousMonthEnd])
            ->where('timesheetable_type', $workerClass)
            ->sum('hours');

        $workerHoursChangePercent = $totalWorkerHoursPreviousMonth > 0
            ? (($totalWorkerHoursCurrentMonth - $totalWorkerHoursPreviousMonth) / $totalWorkerHoursPreviousMonth) * 100
            : 0;

        // Heures intérimaires
        $totalInterimHoursCurrentMonth = TimeSheetable::whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('timesheetable_type', $interimClass)
            ->sum('hours');

        $totalInterimHoursPreviousMonth = TimeSheetable::whereBetween('date', [$previousMonthStart, $previousMonthEnd])
            ->where('timesheetable_type', $interimClass)
            ->sum('hours');

        $interimHoursChangePercent = $totalInterimHoursPreviousMonth > 0
            ? (($totalInterimHoursCurrentMonth - $totalInterimHoursPreviousMonth) / $totalInterimHoursPreviousMonth) * 100
            : 0;

        // Heures totales
        $totalHoursCurrentMonth = $totalWorkerHoursCurrentMonth + $totalInterimHoursCurrentMonth;
        $totalHoursPreviousMonth = $totalWorkerHoursPreviousMonth + $totalInterimHoursPreviousMonth;

        $hoursChangePercent = $totalHoursPreviousMonth > 0
            ? (($totalHoursCurrentMonth - $totalHoursPreviousMonth) / $totalHoursPreviousMonth) * 100
            : 0;

        // Coûts
        $currentMonthCosts = $this->projectCostsService->getProjectCosts(null, null, $currentMonthStart->format('Y-m-d'), $currentMonthEnd->format('Y-m-d'));
        $previousMonthCosts = $this->projectCostsService->getProjectCosts(null, null, $previousMonthStart->format('Y-m-d'), $previousMonthEnd->format('Y-m-d'));

        $totalCostCurrentMonth = array_sum(array_column($currentMonthCosts, 'total_cost'));
        $totalCostPreviousMonth = array_sum(array_column($previousMonthCosts, 'total_cost'));

        $costChangePercent = $totalCostPreviousMonth > 0
            ? (($totalCostCurrentMonth - $totalCostPreviousMonth) / $totalCostPreviousMonth) * 100
            : 0;

        // Calcul des coûts mensuels pour l'année en cours
        $currentYear = Carbon::now()->year;
        $monthlyTotalCosts = [];
        $monthLabels = [];

        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth()->format('Y-m-d');
            $endOfMonth   = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth()->format('Y-m-d');
            $monthLabel   = Carbon::createFromDate($currentYear, $month, 1)->locale('fr')->translatedFormat('F');
            $monthLabels[] = $monthLabel;

            // Utiliser le service pour obtenir les coûts du mois
            $monthCosts = $this->projectCostsService->getProjectCosts(
                null,      // pas de filtre par ID
                $category, // garder le filtre de catégorie actuel
                $startOfMonth,
                $endOfMonth
            );

            // Calculer le total des coûts pour ce mois
            $totalCost = array_sum(array_column($monthCosts, 'total_cost'));
            $monthlyTotalCosts[] = $totalCost;
        }

        $showBackButton = true;

        return view('pages.admin.reportings.index', compact(
            'reportType',
            'projects',
            'workers',
            'startDate',
            'endDate',
            'projectId',
            'workerId',
            'category',
            'reportData',
            'chartData',
            'workerHoursChangePercent',
            'interimHoursChangePercent',
            'hoursChangePercent',
            'costChangePercent',
            'monthlyTotalCosts',
            'monthLabels',
            'currentYear',
            'showBackButton'
        ));
    }

    /**
     * Récupère les coûts mensuels d'un projet spécifique pour l'année en cours
     */
    public function getProjectMonthlyCosts(Request $request)
    {
        $projectId = $request->input('project_id');

        if (!$projectId) {
            return response()->json(['error' => 'ID du projet requis'], 400);
        }

        $project = Project::findOrFail($projectId);
        $currentYear = Carbon::now()->year;
        $monthlyProjectCosts = [];
        $monthLabels = [];

        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth()->format('Y-m-d');
            $endOfMonth = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth()->format('Y-m-d');
            $monthLabel = Carbon::createFromDate($currentYear, $month, 1)->locale('fr')->translatedFormat('F');
            $monthLabels[] = $monthLabel;

            // Utiliser le service pour obtenir les coûts du mois
            $monthCosts = $this->projectCostsService->getProjectCosts(
                $projectId,  // filtre par ID du projet
                null,        // pas de filtre de catégorie
                $startOfMonth,
                $endOfMonth
            );

            $totalCost = 0;
            foreach ($monthCosts as $projectData) {
                if ($projectData['id'] == $projectId) {
                    $totalCost = $projectData['total_cost'];
                    break;
                }
            }

            $monthlyProjectCosts[] = $totalCost;
        }

        return response()->json([
            'labels' => $monthLabels,
            'costs' => $monthlyProjectCosts,
            'project_name' => $project->name,
            'project_code' => $project->code,
            'project_address' => $project->address ?? 'Adresse non spécifiée'
        ]);
    }

    /**
     * Display the dashboard with various statistics
     */
    public function dashboard(Request $request)
    {
        // Périodes pour les calculs
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Obtenir les classes pour les requêtes
        $workerClass = get_class(new Worker());
        $interimClass = get_class(new Interim());

        // KPI 1: Heures travaillées (worker vs interim)
        $totalHoursCurrentMonth = TimeSheetable::whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->sum('hours');

        $totalWorkerHoursCurrentMonth = TimeSheetable::whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('timesheetable_type', $workerClass)
            ->sum('hours');

        $totalInterimHoursCurrentMonth = TimeSheetable::whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('timesheetable_type', $interimClass)
            ->sum('hours');

        $totalHoursPreviousMonth = TimeSheetable::whereBetween('date', [$previousMonthStart, $previousMonthEnd])
            ->sum('hours');

        $hoursChangePercent = $totalHoursPreviousMonth > 0
            ? (($totalHoursCurrentMonth - $totalHoursPreviousMonth) / $totalHoursPreviousMonth) * 100
            : 0;

        // KPI 2: Coûts du mois courant (workers uniquement)
        $currentMonthDateRange = [$currentMonthStart->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')];
        $previousMonthDateRange = [$previousMonthStart->format('Y-m-d'), $previousMonthEnd->format('Y-m-d')];

        // Utiliser ProjectCostsService pour obtenir les coûts
        $currentMonthCosts = $this->projectCostsService->getProjectCosts(null, null, $currentMonthDateRange[0], $currentMonthDateRange[1]);
        $previousMonthCosts = $this->projectCostsService->getProjectCosts(null, null, $previousMonthDateRange[0], $previousMonthDateRange[1]);

        $totalCostCurrentMonth = array_sum(array_column($currentMonthCosts, 'total_cost'));
        $totalCostPreviousMonth = array_sum(array_column($previousMonthCosts, 'total_cost'));

        $costChangePercent = $totalCostPreviousMonth > 0
            ? (($totalCostCurrentMonth - $totalCostPreviousMonth) / $totalCostPreviousMonth) * 100
            : 0;

        // KPI 3: Projets actifs et avec activité
        $activeProjectsCount = Project::where('status', 'active')->count();
        $projectsWithActivityCount = TimeSheetable::whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->select('project_id')
            ->distinct()
            ->count();

        // KPI 4: Travailleurs actifs et avec activité
        $activeWorkersCount = Worker::where('status', 'active')->count();
        $workersWithActivityCount = TimeSheetable::whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('timesheetable_type', $workerClass)
            ->select('timesheetable_id')
            ->distinct()
            ->count();

        // Top 5 projets par heures
        $topProjects = DB::table('time_sheetables')
            ->join('projects', 'time_sheetables.project_id', '=', 'projects.id')
            ->select(
                'projects.id',
                'projects.name',
                DB::raw('SUM(time_sheetables.hours) as total_hours'),
                DB::raw("SUM(CASE WHEN time_sheetables.timesheetable_type = '{$workerClass}' THEN time_sheetables.hours ELSE 0 END) as worker_hours"),
                DB::raw("SUM(CASE WHEN time_sheetables.timesheetable_type = '{$interimClass}' THEN time_sheetables.hours ELSE 0 END) as interim_hours")
            )
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->groupBy('projects.id', 'projects.name')
            ->orderByDesc('total_hours')
            ->take(5)
            ->get();

        // Top 5 travailleurs par heures
        $topWorkers = DB::table('time_sheetables')
            ->join('workers', function ($join) use ($workerClass) {
                $join->on('time_sheetables.timesheetable_id', '=', 'workers.id')
                    ->where('time_sheetables.timesheetable_type', '=', $workerClass);
            })
            ->select(
                'workers.id',
                'workers.first_name',
                'workers.last_name',
                DB::raw('SUM(time_sheetables.hours) as total_hours')
            )
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->groupBy('workers.id', 'workers.first_name', 'workers.last_name')
            ->orderByDesc('total_hours')
            ->take(5)
            ->get();

        // Répartition des coûts par catégorie de projet (workers uniquement)
        $costsByCategory = [];
        foreach ($currentMonthCosts as $project) {
            $cat = $project['attributes']['category'];
            if (!isset($costsByCategory[$cat])) {
                $costsByCategory[$cat] = 0;
            }
            $costsByCategory[$cat] += $project['total_cost'];
        }

        // Tendance heures sur 6 derniers mois (workers vs interims)
        $monthlyHoursData = [];
        $monthlyHoursLabels = [];
        $monthlyWorkersData = [];
        $monthlyInterimsData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthLabel = $month->format('M Y');
            $monthlyHoursLabels[] = $monthLabel;

            $monthlyHours = TimeSheetable::whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('hours');

            $monthlyWorkerHours = TimeSheetable::whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->where('timesheetable_type', $workerClass)
                ->sum('hours');

            $monthlyInterimHours = TimeSheetable::whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->where('timesheetable_type', $interimClass)
                ->sum('hours');

            $monthlyHoursData[] = $monthlyHours;
            $monthlyWorkersData[] = $monthlyWorkerHours;
            $monthlyInterimsData[] = $monthlyInterimHours;
        }

        // Récupérer les logs d'activité liés aux timeSheetables
        $activityLogs = Activity::with(['causer', 'subject'])
            ->where('subject_type', TimeSheetable::class) // Utiliser la référence de classe
            ->latest()
            ->paginate(10);

        $showBackButton = false;

        return view('pages.admin.reportings.dashboard', compact(
            'totalHoursCurrentMonth',
            'totalWorkerHoursCurrentMonth',
            'totalInterimHoursCurrentMonth',
            'hoursChangePercent',
            'totalCostCurrentMonth',
            'costChangePercent',
            'activeProjectsCount',
            'projectsWithActivityCount',
            'activeWorkersCount',
            'workersWithActivityCount',
            'topProjects',
            'topWorkers',
            'costsByCategory',
            'monthlyHoursLabels',
            'monthlyHoursData',
            'monthlyWorkersData',
            'monthlyInterimsData',
            'activityLogs',
            'showBackButton',
        ));
    }

    /**
     * Format project hours data for charts
     */
    private function prepareProjectHoursChartData(array $data): array
    {
        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Heures par projet',
                    'data' => [],
                    'backgroundColor' => []
                ]
            ]
        ];

        $colors = [
            '#4299E1',
            '#48BB78',
            '#F6AD55',
            '#F56565',
            '#9F7AEA',
            '#ED64A6',
            '#38B2AC',
            '#667EEA',
            '#F6E05E',
            '#FC8181'
        ];

        foreach ($data as $index => $project) {
            $chartData['labels'][] = $project['name'];
            $chartData['datasets'][0]['data'][] = $project['total_hours'];
            $chartData['datasets'][0]['backgroundColor'][] = $colors[$index % count($colors)];
        }

        return $chartData;
    }

    /**
     * Format worker hours data for charts
     */
    private function prepareWorkerHoursChartData(array $data): array
    {
        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Heures par salarié',
                    'data' => [],
                    'backgroundColor' => []
                ]
            ]
        ];

        $colors = [
            '#4299E1',
            '#48BB78',
            '#F6AD55',
            '#F56565',
            '#9F7AEA',
            '#ED64A6',
            '#38B2AC',
            '#667EEA',
            '#F6E05E',
            '#FC8181'
        ];

        foreach ($data as $index => $worker) {
            $chartData['labels'][] = $worker['first_name'] . ' ' . $worker['last_name'];
            $chartData['datasets'][0]['data'][] = $worker['total_hours'];
            $chartData['datasets'][0]['backgroundColor'][] = $colors[$index % count($colors)];
        }

        return $chartData;
    }

    /**
     * Format project costs data for charts
     */
    private function prepareProjectCostsChartData(array $data): array
    {
        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Coûts par projet (salariés uniquement)',
                    'data' => [],
                    'backgroundColor' => []
                ]
            ]
        ];

        $colors = [
            '#4299E1',
            '#48BB78',
            '#F6AD55',
            '#F56565',
            '#9F7AEA',
            '#ED64A6',
            '#38B2AC',
            '#667EEA',
            '#F6E05E',
            '#FC8181'
        ];

        foreach ($data as $index => $project) {
            $chartData['labels'][] = $project['attributes']['name'];
            $chartData['datasets'][0]['data'][] = $project['total_cost'];
            $chartData['datasets'][0]['backgroundColor'][] = $colors[$index % count($colors)];
        }

        return $chartData;
    }

    /**
     * Format worker costs data for charts
     */
    private function prepareWorkerCostsChartData(array $data): array
    {
        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Coûts par salarié',
                    'data' => [],
                    'backgroundColor' => []
                ]
            ]
        ];

        $colors = [
            '#4299E1',
            '#48BB78',
            '#F6AD55',
            '#F56565',
            '#9F7AEA',
            '#ED64A6',
            '#38B2AC',
            '#667EEA',
            '#F6E05E',
            '#FC8181'
        ];

        foreach ($data as $index => $worker) {
            $chartData['labels'][] = $worker['first_name'] . ' ' . $worker['last_name'];
            $chartData['datasets'][0]['data'][] = $worker['total_cost'];
            $chartData['datasets'][0]['backgroundColor'][] = $colors[$index % count($colors)];
        }

        return $chartData;
    }
}
