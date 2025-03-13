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
        // Check if dashboard view is requested
        if ($request->input('view') === 'dashboard' || !$request->has('report_type')) {
            return $this->dashboard($request);
        }

        $projects = Project::where('status', 'active')->orderBy('code')->get();
        $workers = Worker::where('status', 'active')->orderBy('last_name')->get();

        // Default to current month
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $projectId = $request->input('project_id');
        $workerId = $request->input('worker_id');
        $category = $request->input('category');

        $reportType = $request->input('report_type', 'project_hours');

        $reportData = [];
        $chartData = [];

        switch ($reportType) {
            case 'project_hours':
                $reportData = $this->projectHoursService->getProjectHours($projectId, $category, $startDate, $endDate);
                $chartData = $this->prepareProjectHoursChartData($reportData);
                break;

            case 'worker_hours':
                $reportData = $this->workerHoursService->getWorkerHours($workerId, $category, $startDate, $endDate);
                $chartData = $this->prepareWorkerHoursChartData($reportData);
                break;

            case 'project_costs':
                $reportData = $this->projectCostsService->getProjectCosts($projectId, $category, $startDate, $endDate);
                $chartData = $this->prepareProjectCostsChartData($reportData);
                break;

            case 'worker_costs':
                $reportData = $this->workerCostsService->getWorkerCosts($workerId, $category, $startDate, $endDate);
                $chartData = $this->prepareWorkerCostsChartData($reportData);
                break;
        }

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
        ));
    }

    /**
     * Display the dashboard with key metrics
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
            $category = $project['attributes']['category'];
            if (!isset($costsByCategory[$category])) {
                $costsByCategory[$category] = 0;
            }
            $costsByCategory[$category] += $project['total_cost'];
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
            ->paginate(10)
            ->appends($request->except('page'));

        // Pour déboguer, vérifiez si des logs sont récupérés
        Log::info('Nombre de logs d\'activité: ' . $activityLogs->count());

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
            'activityLogs'
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
                    'label' => 'Heures par travailleur',
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
                    'label' => 'Coûts par projet (workers uniquement)',
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
                    'label' => 'Coûts par travailleur',
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
