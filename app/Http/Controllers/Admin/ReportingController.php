<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Worker;
use App\Models\Setting;
use App\Models\TimeSheetable;
use App\Services\Costs\ProjectCostsService;
use App\Services\Costs\WorkerCostsService;
use App\Services\Hours\ProjectHoursService;
use App\Services\Hours\WorkerHoursService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        WorkerCostsService $workerCostsService,
        ProjectHoursService $projectHoursService,
        WorkerHoursService $workerHoursService
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
        Log::info("ReportingController@index - Start with view: " . $request->input('view') . ", report_type: " . $request->input('report_type'));

        // Check if dashboard view is requested
        if ($request->input('view') === 'dashboard' || !$request->has('report_type')) {
            return $this->dashboard();
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

        Log::info("Getting report data for type: {$reportType}, start_date: {$startDate}, end_date: {$endDate}, project_id: {$projectId}, worker_id: {$workerId}, category: {$category}");

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

        // Log the summary of the report data
        Log::info("Report data summary:");
        if ($reportType === 'project_hours' || $reportType === 'project_costs') {
            foreach ($reportData as $index => $project) {
                $hours = $reportType === 'project_hours' ? $project['total_hours'] : $project['total_hours'];
                $name = $reportType === 'project_hours' ? $project['name'] : $project['attributes']['name'];
                Log::info("  Project {$index}: {$name}, Hours: {$hours}");
            }
        } else {
            foreach ($reportData as $index => $worker) {
                Log::info("  Worker {$index}: {$worker['first_name']} {$worker['last_name']}, Hours/Cost: " .
                    ($reportType === 'worker_hours' ? $worker['total_hours'] : $worker['total_cost']));
            }
        }

        return view('pages.admin.reporting', compact(
            'reportType',
            'projects',
            'workers',
            'startDate',
            'endDate',
            'projectId',
            'workerId',
            'category',
            'reportData',
            'chartData'
        ));
    }

    /**
     * Display the dashboard with key metrics
     */
    public function dashboard()
    {
        Log::info("ReportingController@dashboard - Starting dashboard generation");

        // Périodes pour les calculs
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        Log::info("Current month: {$currentMonthStart->format('Y-m-d')} to {$currentMonthEnd->format('Y-m-d')}");
        Log::info("Previous month: {$previousMonthStart->format('Y-m-d')} to {$previousMonthEnd->format('Y-m-d')}");

        // KPI 1 & 2: Heures et coûts du mois courant et variation vs mois précédent
        $totalHoursCurrentMonth = TimeSheetable::whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->sum('hours');

        $totalHoursPreviousMonth = TimeSheetable::whereBetween('date', [$previousMonthStart, $previousMonthEnd])
            ->sum('hours');

        $hoursChangePercent = $totalHoursPreviousMonth > 0
            ? (($totalHoursCurrentMonth - $totalHoursPreviousMonth) / $totalHoursPreviousMonth) * 100
            : 0;

        Log::info("Total hours - Current month: {$totalHoursCurrentMonth}, Previous month: {$totalHoursPreviousMonth}, Change: {$hoursChangePercent}%");

        // Les coûts totaux nécessitent plus de calculs et utilisent les services
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

        Log::info("Total costs - Current month: {$totalCostCurrentMonth}, Previous month: {$totalCostPreviousMonth}, Change: {$costChangePercent}%");

        // KPI 3: Projets actifs et avec activité
        $activeProjectsCount = Project::where('status', 'active')->count();
        $projectsWithActivityCount = TimeSheetable::whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->select('project_id')
            ->distinct()
            ->count();

        Log::info("Projects - Active: {$activeProjectsCount}, With activity this month: {$projectsWithActivityCount}");

        // KPI 4: Travailleurs actifs et avec activité
        $activeWorkersCount = Worker::where('status', 'active')->count();
        $workersWithActivityCount = TimeSheetable::whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->where('timesheetable_type', Worker::class)
            ->select('timesheetable_id')
            ->distinct()
            ->count();

        Log::info("Workers - Active: {$activeWorkersCount}, With activity this month: {$workersWithActivityCount}");

        // Top 5 projets par heures
        $topProjects = DB::table('time_sheetables')
            ->join('projects', 'time_sheetables.project_id', '=', 'projects.id')
            ->select('projects.id', 'projects.name', DB::raw('SUM(time_sheetables.hours) as total_hours'))
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->groupBy('projects.id', 'projects.name')
            ->orderByDesc('total_hours')
            ->take(5)
            ->get();

        Log::info("Top projects by hours: " . $topProjects->count());
        foreach ($topProjects as $index => $project) {
            Log::info("  Project {$index}: {$project->name}, Hours: {$project->total_hours}");
        }

        // Top 5 travailleurs par heures
        $topWorkers = DB::table('time_sheetables')
            ->join('workers', function ($join) {
                $join->on('time_sheetables.timesheetable_id', '=', 'workers.id')
                    ->where('time_sheetables.timesheetable_type', '=', Worker::class);
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

        Log::info("Top workers by hours: " . $topWorkers->count());
        foreach ($topWorkers as $index => $worker) {
            Log::info("  Worker {$index}: {$worker->first_name} {$worker->last_name}, Hours: {$worker->total_hours}");
        }

        // Répartition des coûts par catégorie de projet
        $costsByCategory = [];
        foreach ($currentMonthCosts as $project) {
            $category = $project['attributes']['category'];
            if (!isset($costsByCategory[$category])) {
                $costsByCategory[$category] = 0;
            }
            $costsByCategory[$category] += $project['total_cost'];
        }

        Log::info("Costs by category:");
        foreach ($costsByCategory as $category => $cost) {
            Log::info("  {$category}: {$cost}");
        }

        // Tendance heures sur 6 derniers mois
        $monthlyHoursData = [];
        $monthlyHoursLabels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthLabel = $month->format('M Y');
            $monthlyHoursLabels[] = $monthLabel;

            $monthlyHours = TimeSheetable::whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('hours');

            $monthlyHoursData[] = $monthlyHours;

            Log::info("  {$monthLabel}: {$monthlyHours} hours");
        }

        return view('pages.admin.reporting-dashboard', compact(
            'totalHoursCurrentMonth',
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
            'monthlyHoursData'
        ));
    }

    /**
     * Format project hours data for charts
     */
    private function prepareProjectHoursChartData(array $data): array
    {
        Log::info("Preparing chart data for project hours");

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

            Log::info("  Chart data for {$project['name']}: {$project['total_hours']} hours");
        }

        return $chartData;
    }

    /**
     * Format worker hours data for charts
     */
    private function prepareWorkerHoursChartData(array $data): array
    {
        Log::info("Preparing chart data for worker hours");

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
            $label = $worker['first_name'] . ' ' . $worker['last_name'];
            $chartData['labels'][] = $label;
            $chartData['datasets'][0]['data'][] = $worker['total_hours'];
            $chartData['datasets'][0]['backgroundColor'][] = $colors[$index % count($colors)];

            Log::info("  Chart data for {$label}: {$worker['total_hours']} hours");
        }

        return $chartData;
    }

    /**
     * Format project costs data for charts
     */
    private function prepareProjectCostsChartData(array $data): array
    {
        Log::info("Preparing chart data for project costs");

        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Coûts par projet',
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

            Log::info("  Chart data for {$project['attributes']['name']}: {$project['total_cost']} €");
        }

        return $chartData;
    }

    /**
     * Format worker costs data for charts
     */
    private function prepareWorkerCostsChartData(array $data): array
    {
        Log::info("Preparing chart data for worker costs");

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
            $label = $worker['first_name'] . ' ' . $worker['last_name'];
            $chartData['labels'][] = $label;
            $chartData['datasets'][0]['data'][] = $worker['total_cost'];
            $chartData['datasets'][0]['backgroundColor'][] = $colors[$index % count($colors)];

            Log::info("  Chart data for {$label}: {$worker['total_cost']} €");
        }

        return $chartData;
    }
}
