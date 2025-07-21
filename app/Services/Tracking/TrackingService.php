<?php

namespace App\Services\Tracking;

use App\Models\Project;
use App\Models\Worker;
use App\Models\Interim;
use App\Models\TimeSheetable;
use App\Models\NonWorkingDay;
use App\Repositories\ProjectRepositoryInterface;
use App\Services\Costs\CostCalculatorInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TrackingService implements TrackingServiceInterface
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private CostCalculatorInterface $costCalculator
    ) {}

    /**
     * Récupérer toutes les données nécessaires pour l'affichage de la page de tracking
     * LOGIQUE EXTRAITE IDENTIQUE du TrackingController::show()
     */
    public function getTrackingData(array $params): array
    {
        $projectId = $params['project_id'];
        $month = $params['month'];
        $year = $params['year'];
        $category = $params['category'] ?? 'day';
        
        // Récupérer le projet (ligne 40 du contrôleur)
        $project = Project::findOrFail($projectId);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        // Construire les données d'entrée (lignes 47-87 du contrôleur)
        $entriesData = $this->buildEntriesData($project, $month, $year, $category);
        
        // Récupérer les employés disponibles (lignes 89-102 du contrôleur)
        $availableEmployees = $this->getAvailableEmployees($projectId);
        
        // Construire le récapitulatif (lignes 104-159 du contrôleur)
        $recap = $this->buildRecapData($project, $month, $year);
        
        // Calculer les KPIs (lignes 161-186 du contrôleur)
        $kpis = $this->calculateKPIs($project, $month, $year);
        
        // Données de navigation (lignes 189-201 du contrôleur)
        $navigation = $this->buildNavigationData($month, $year);
        
        // Jours non travaillés (lignes 203-220 du contrôleur)
        $nonWorkingDaysData = $this->getNonWorkingDays($month, $year);
        
        return [
            'project' => $project,
            'month' => $month,
            'year' => $year,
            'category' => $category,
            'daysInMonth' => $daysInMonth,
            'entriesData' => $entriesData,
            'availableWorkers' => $availableEmployees['workers'],
            'availableInterims' => $availableEmployees['interims'],
            'recap' => $recap,
            'totalHoursCurrentMonth' => $kpis['totalHoursCurrentMonth'],
            'totalWorkerHoursCurrentMonth' => $kpis['totalWorkerHoursCurrentMonth'],
            'totalInterimHoursCurrentMonth' => $kpis['totalInterimHoursCurrentMonth'],
            'costWorkerTotal' => $kpis['costWorkerTotal'],
            'prevMonth' => $navigation['prevMonth'],
            'prevYear' => $navigation['prevYear'],
            'nextMonth' => $navigation['nextMonth'],
            'nextYear' => $navigation['nextYear'],
            'nonWorkingDays' => $nonWorkingDaysData['formattedDays'],
            'nonWorkingDayTypes' => $nonWorkingDaysData['types']
        ];
    }

    /**
     * Construire les données d'entrée pour Handsontable
     * LOGIQUE EXTRAITE IDENTIQUE des lignes 47-87 du contrôleur
     */
    public function buildEntriesData(Project $project, int $month, int $year, string $category): array
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        // 1) Récupérer les Workers & Interims assignés au projet (lignes 47-48)
        $workers = $project->workers()->where('status', 'active')->get();
        $interims = $project->interims()->where('status', 'active')->get();

        // Construire entriesData pour Handsontable (lignes 50-66)
        $entriesData = [];
        foreach ($workers as $w) {
            $entriesData[] = [
                'id' => $w->id,
                'model_type' => 'worker',
                'full_name' => $w->first_name . ' ' . $w->last_name
            ];
        }
        foreach ($interims as $i) {
            $entriesData[] = [
                'id' => $i->id,
                'model_type' => 'interim',
                'full_name' => $i->agency . ' (Intérim)'
            ];
        }

        // Charger time_sheetables pour ce projet, ce mois, cette année et catégorie (lignes 68-73)
        $timeSheets = TimeSheetable::where('project_id', $project->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('category', $category)
            ->whereIn('timesheetable_type', [Worker::class, Interim::class])
            ->get();

        // Remplir days pour chaque entrée (lignes 75-87)
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

        return $entriesData;
    }

    /**
     * Construire les données de récapitulatif mensuel
     * OPTIMISÉ : Utilise une seule requête au lieu de 2 requêtes par employé
     */
    public function buildRecapData(Project $project, int $month, int $year): array
    {
        $recap = [];
        $workers = $project->workers()->where('status', 'active')->get();
        $interims = $project->interims()->where('status', 'active')->get();

        // Récupérer toutes les heures en une seule requête pour les workers
        $workerHours = TimeSheetable::where('project_id', $project->id)
            ->where('timesheetable_type', Worker::class)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->selectRaw('timesheetable_id, category, SUM(hours) as total_hours')
            ->groupBy('timesheetable_id', 'category')
            ->get()
            ->groupBy('timesheetable_id');

        // Récupérer toutes les heures en une seule requête pour les interims
        $interimHours = TimeSheetable::where('project_id', $project->id)
            ->where('timesheetable_type', Interim::class)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->selectRaw('timesheetable_id, category, SUM(hours) as total_hours')
            ->groupBy('timesheetable_id', 'category')
            ->get()
            ->groupBy('timesheetable_id');

        // Construire le recap pour les workers
        foreach ($workers as $w) {
            $daySum = $workerHours->get($w->id)?->where('category', 'day')->first()?->total_hours ?? 0;
            $nightSum = $workerHours->get($w->id)?->where('category', 'night')->first()?->total_hours ?? 0;

            $recap[] = [
                'id' => $w->id,
                'model_type' => 'worker',
                'full_name' => $w->first_name . ' ' . $w->last_name,
                'day_hours' => $daySum,
                'night_hours' => $nightSum,
                'total' => $daySum + $nightSum,
            ];
        }

        // Construire le recap pour les interims
        foreach ($interims as $i) {
            $daySum = $interimHours->get($i->id)?->where('category', 'day')->first()?->total_hours ?? 0;
            $nightSum = $interimHours->get($i->id)?->where('category', 'night')->first()?->total_hours ?? 0;

            $recap[] = [
                'id' => $i->id,
                'model_type' => 'interim',
                'full_name' => $i->agency . ' (Intérim)',
                'day_hours' => $daySum,
                'night_hours' => $nightSum,
                'total' => $daySum + $nightSum,
            ];
        }

        return $recap;
    }

    /**
     * Calculer les KPIs (heures totales, coûts)
     * LOGIQUE EXTRAITE IDENTIQUE des lignes 161-186 du contrôleur
     */
    public function calculateKPIs(Project $project, int $month, int $year): array
    {
        $recap = $this->buildRecapData($project, $month, $year);
        
        // Calcul des KPI à partir du recap (lignes 161-174)
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
        $totalInterimHoursCurrentMonth = $totalInterimHours;

        // Définir les bornes de la période (lignes 176-178)
        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');

        // Utiliser le service pour calculer le coût des workers uniquement (lignes 181-185)
        $costData = $this->costCalculator->calculateTotalCostForProject($project, $startDate, $endDate);
        $costWorkerTotal = $costData['cost'];

        return [
            'totalHoursCurrentMonth' => $totalHoursCurrentMonth,
            'totalWorkerHoursCurrentMonth' => $totalWorkerHoursCurrentMonth,
            'totalInterimHoursCurrentMonth' => $totalInterimHoursCurrentMonth,
            'costWorkerTotal' => $costWorkerTotal
        ];
    }

    /**
     * Préparer les données de navigation (mois précédent/suivant)
     * LOGIQUE EXTRAITE IDENTIQUE des lignes 189-201 du contrôleur
     */
    public function buildNavigationData(int $month, int $year): array
    {
        // Calcul du mois précédent et suivant (lignes 189-201)
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }
        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        return [
            'prevMonth' => $prevMonth,
            'prevYear' => $prevYear,
            'nextMonth' => $nextMonth,
            'nextYear' => $nextYear
        ];
    }

    /**
     * Récupérer les workers/interims disponibles (non assignés au projet)
     * LOGIQUE EXTRAITE IDENTIQUE des lignes 89-102 du contrôleur
     */
    public function getAvailableEmployees(int $projectId): array
    {
        // 2) Récupérer la liste des Workers / Interims disponibles (non assignés) (lignes 89-102)
        $availableWorkers = Worker::where('status', 'active')
            ->whereDoesntHave('projects', function ($q) use ($projectId) {
                $q->where('projects.id', $projectId);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $availableInterims = Interim::where('status', 'active')
            ->whereDoesntHave('projects', function ($q) use ($projectId) {
                $q->where('projects.id', $projectId);
            })
            ->get();

        return [
            'workers' => $availableWorkers,
            'interims' => $availableInterims
        ];
    }

    /**
     * Récupérer les jours non travaillés pour un mois donné
     * LOGIQUE EXTRAITE IDENTIQUE des lignes 203-220 du contrôleur
     */
    public function getNonWorkingDays(int $month, int $year): array
    {
        // Récupérer les jours non travaillés pour ce mois (lignes 203-220)
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

        return [
            'formattedDays' => $formattedDays,
            'types' => $nonWorkingDayTypes
        ];
    }
}