<?php

namespace App\Exports;

use App\Models\Worker;
use App\Models\TimeSheetable;
use App\Models\Project;
use App\Models\Zone;
use App\Services\Export\ExcelStyleService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WorkerMonthlyExport implements FromArray, WithStyles, WithEvents
{
    protected $month;
    protected $year;
    protected $holidays;
    protected $styleService;

    // Propriété pour stocker les numéros de ligne des salariés
    protected $workerRows = [];
    
    // Propriété pour stocker les numéros de ligne des totaux de workers
    protected $workerTotalRows = [];
    
    // Propriété pour stocker les groupes de workers (début et fin de chaque groupe)
    protected $workerGroups = [];
    
    // Propriété pour stocker les catégories des lignes de projet (day/night)
    protected $projectCategories = [];
    
    // Propriété pour stocker les tarifs de zones uniques
    protected $zoneRates = [];

    public function __construct($month, $year, $holidays = [], ExcelStyleService $styleService = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->holidays = $holidays;
        $this->styleService = $styleService ?? app(ExcelStyleService::class);
    }

    /**
     * Retourne la collection de données pour l'export.
     *
     * @return array
     */
    public function array(): array
    {
        // Récupérer tous les workers actifs avec leurs pointages
        $workers = Worker::where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Déterminer le nombre de jours dans le mois sélectionné
        $daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;

        // Formater le mois et l'année
        $monthFormatted = str_pad($this->month, 2, '0', STR_PAD_LEFT);
        $yearFormatted = substr($this->year, -2); // Prend les deux derniers chiffres de l'année
        $dateFormatted = "{$monthFormatted}/{$yearFormatted}";

        // *** Récupérer tous les tarifs de zones uniques utilisés ce mois ***
        $this->zoneRates = $this->getUniqueZoneRates();

        $data = [];

        // *** Ligne d'en-têtes directement ***
        $headerRow = ["DUBOCQ OUVRIER {$dateFormatted}", 'DEPLACEMENT']; // Colonne B pour déplacement
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $headerRow[] = $day;
        }
        $headerRow[] = "TOTAL\nHEURES\nTRAVAILLEES";
        
        // *** Ajouter les colonnes des tarifs de zones ***
        foreach ($this->zoneRates as $rate) {
            $headerRow[] = $this->formatZoneRate($rate);
        }
        
        // *** Ajouter la colonne PANIER avec affichage vertical ***
        $headerRow[] = "P\nA\nN\nI\nE\nR";
        
        $data[] = $headerRow;

        // Parcourir chaque worker
        foreach ($workers as $worker) {
            // Récupérer les pointages pour le mois et l'année spécifiés
            $timeSheets = TimeSheetable::where('timesheetable_type', Worker::class)
                ->where('timesheetable_id', $worker->id)
                ->whereYear('date', $this->year)
                ->whereMonth('date', $this->month)
                ->get();

            // Calculer le total des heures pour le worker
            $totalWorkerHours = $timeSheets->sum('hours');

            // *** Créer un tableau pour stocker le statut de chaque jour ***
            $workerDayStatus = [];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $workerDayStatus[$day] = '';
            }

            // Parcourir les pointages pour déterminer les absences et heures
            foreach ($timeSheets as $sheet) {
                $day = $sheet->date->day;
                $hours = $sheet->hours;

                // Si heures = 0, marquer "abs" pour ce jour
                if ($hours == 0) {
                    $workerDayStatus[$day] = 'abs';
                }
            }

            // *** Ajouter une ligne pour le nom du worker (TOUJOURS) ***
            $workerNameRow = [$worker->last_name . ' ' . $worker->first_name, ''];

            // Remplir les colonnes des jours avec les statuts
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $workerNameRow[] = $workerDayStatus[$day];
            }

            // Ne pas afficher le total sur la ligne du nom
            $workerNameRow[] = null;
            
            // *** Ajouter des colonnes vides pour les tarifs de zones ***
            foreach ($this->zoneRates as $rate) {
                $workerNameRow[] = null;
            }
            
            // *** Ajouter colonne PANIER vide ***
            $workerNameRow[] = null;

            // Avant d'ajouter la ligne du salarié, enregistrer le numéro de ligne
            $currentRow = count($data) + 1; // Numéro de ligne actuel
            $this->workerRows[] = $currentRow;
            
            // Début du groupe worker
            $workerGroupStart = $currentRow;

            $data[] = $workerNameRow;

            // *** Ajouter les lignes des projets sous chaque worker ***
            // Regrouper les pointages par projet
            $projectGroups = $timeSheets->groupBy('project_id');

                foreach ($projectGroups as $projectId => $projectTimeSheets) {
                $project = Project::with('zone')->find($projectId);

                if (!$project) continue;

                // Construire le libellé du projet
                $code = trim($project->code ?? '');
                $name = trim($project->name ?? '');
                $city = trim($project->city ?? '');

                // Créer un tableau avec les parties du projet
                $projectParts = [$code, $name, $city];

                // Filtrer les parties vides ou nulles après trim
                $filteredParts = array_filter($projectParts, function ($part) {
                    return $part !== '';
                });

                // Concaténer les parties restantes avec des tirets
                $projectDetails = implode(' - ', $filteredParts);

                // Initialiser les heures par jour et catégorie
                $projectDayHours = [];
                $projectNightHours = [];
                $totalProjectDayHours = 0;
                $totalProjectNightHours = 0;

                foreach ($projectTimeSheets as $sheet) {
                    $day = $sheet->date->day;
                    $hours = $sheet->hours;
                    $category = $sheet->category;

                    // Stocker les heures de jour/nuit
                    if ($category == 'day') {
                        $projectDayHours[$day] = ($projectDayHours[$day] ?? 0) + $hours;
                        $totalProjectDayHours += $hours;
                    } else { // night
                        $projectNightHours[$day] = ($projectNightHours[$day] ?? 0) + $hours;
                        $totalProjectNightHours += $hours;
                    }
                }

                // Récupérer le tarif de zone du projet
                $zoneRate = $project->zone ? $project->zone->rate : null;
                $zoneRateDisplay = $zoneRate ? rtrim(rtrim(number_format($zoneRate, 2, '.', ''), '0'), '.') : '';

                // *** Ligne des heures de jour ***
                if ($totalProjectDayHours > 0) {
                    $dayRow = ['    ' . $projectDetails, $zoneRateDisplay]; // Tarif zone dans colonne B
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $dayRow[] = isset($projectDayHours[$day]) ? $this->formatHours($projectDayHours[$day]) : null;
                    }
                    $dayRow[] = $totalProjectDayHours > 0 ? $this->formatHours($totalProjectDayHours) : null;
                    
                    // *** Ajouter les jours dans la colonne du tarif de zone correspondant ***
                    foreach ($this->zoneRates as $rate) {
                        if ($zoneRate && abs($rate - $zoneRate) < 0.01) { // Comparaison avec tolérance
                            $dayRow[] = $this->calculateDaysFromHours($totalProjectDayHours);
                        } else {
                            $dayRow[] = null;
                        }
                    }
                    
                    // *** Ajouter colonne PANIER vide pour les lignes de projet ***
                    $dayRow[] = null;
                    
                    $data[] = $dayRow;
                    
                    // Enregistrer la catégorie pour cette ligne
                    $currentRowIndex = count($data);
                    $this->projectCategories[$currentRowIndex] = 'day';
                }

                // *** Ligne des heures de nuit (si présentes) ***
                if ($totalProjectNightHours > 0) {
                    $nightRow = ['    ' . $projectDetails . ' (Nuit)', $zoneRateDisplay]; // Tarif zone dans colonne B
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $nightRow[] = isset($projectNightHours[$day]) ? $this->formatHours($projectNightHours[$day]) : null;
                    }
                    $nightRow[] = $totalProjectNightHours > 0 ? $this->formatHours($totalProjectNightHours) : null;
                    
                    // *** Ajouter les jours dans la colonne du tarif de zone correspondant ***
                    foreach ($this->zoneRates as $rate) {
                        if ($zoneRate && abs($rate - $zoneRate) < 0.01) { // Comparaison avec tolérance
                            $nightRow[] = $this->calculateDaysFromHours($totalProjectNightHours);
                        } else {
                            $nightRow[] = null;
                        }
                    }
                    
                    // *** Ajouter colonne PANIER vide pour les lignes de projet ***
                    $nightRow[] = null;
                    
                    $data[] = $nightRow;
                    
                    // Enregistrer la catégorie pour cette ligne
                    $currentRowIndex = count($data);
                    $this->projectCategories[$currentRowIndex] = 'night';
                }
                }

            // *** Ajouter une ligne de total pour le worker SEULEMENT s'il a des heures ***
            if ($totalWorkerHours > 0) {
                $workerTotalRow = ['TOTAL', ''];
                
                // Calculer les totaux par jour pour ce worker
                $dailyTotals = [];
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::create($this->year, $this->month, $day)->format('Y-m-d');
                    $dayTotal = $timeSheets->where('date', $date)->sum('hours');
                    $dailyTotals[] = $dayTotal > 0 ? $this->formatHours($dayTotal) : null;
                }
                
                // Ajouter les totaux journaliers
                $workerTotalRow = array_merge($workerTotalRow, $dailyTotals);
                
                // Ajouter le total général du worker
                $workerTotalRow[] = $this->formatHours($totalWorkerHours);
                
                // *** Calculer les totaux par tarif de zone pour ce worker (en jours) ***
                $workerZoneTotals = $this->calculateWorkerZoneDays($timeSheets);
                foreach ($this->zoneRates as $rate) {
                    $workerTotalRow[] = isset($workerZoneTotals[$rate]) ? $workerZoneTotals[$rate] : null;
                }
                
                // *** Calculer le total des jours pour la colonne PANIER ***
                $totalDays = array_sum($workerZoneTotals);
                $workerTotalRow[] = $totalDays > 0 ? $totalDays : null;
                
                $data[] = $workerTotalRow;
                
                // Enregistrer le numéro de ligne du total worker (après l'ajout)
                $currentTotalRow = count($data);
                $this->workerTotalRows[] = $currentTotalRow;
                
                // Fin du groupe worker (ligne de total)
                $workerGroupEnd = $currentTotalRow;
                
                // Enregistrer le groupe complet
                $this->workerGroups[] = [
                    'start' => $workerGroupStart,
                    'end' => $workerGroupEnd
                ];
            }
            
            // *** Ajouter une ligne vide après chaque worker pour une meilleure lisibilité ***
            $data[] = array_fill(0, $daysInMonth + 3 + count($this->zoneRates) + 1, ''); // +1 pour colonne PANIER
        }

        // *** Ajouter une ligne totale pour tout le mois ***
        $totalRow = ['TOTAL GENERAL', ''];
        $monthlyTotal = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($this->year, $this->month, $day)->format('Y-m-d');

            $dayTotal = TimeSheetable::where('timesheetable_type', Worker::class)
                ->where('date', $date)
                ->sum('hours');

            $monthlyTotal += $dayTotal;
            $totalRow[] = $dayTotal > 0 ? $this->formatHours($dayTotal) : null;
        }

        $totalRow[] = $monthlyTotal > 0 ? $this->formatHours($monthlyTotal) : null;
        
        // *** Calculer les totaux généraux par tarif de zone (en jours) ***
        $generalZoneTotals = $this->calculateGeneralZoneDays();
        foreach ($this->zoneRates as $rate) {
            $totalRow[] = isset($generalZoneTotals[$rate]) ? $generalZoneTotals[$rate] : null;
        }
        
        // *** Calculer le total général des jours pour la colonne PANIER ***
        $totalGeneralDays = array_sum($generalZoneTotals);
        $totalRow[] = $totalGeneralDays > 0 ? $totalGeneralDays : null;
        
        $data[] = $totalRow;

        return $data;
    }

    /**
     * Appliquer des styles à la feuille de calcul.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Déterminer la dernière colonne utilisée
        $highestColumn = $sheet->getHighestColumn();

        // *** Appliquer le gras aux en-têtes (ligne "SALARIES" et jours) ***
        $sheet->getStyle("A1:" . $highestColumn . "1")->getFont()->setBold(true);

        return [
            // Pas de styles additionnels ici
        ];
    }

    /**
     * Définir les événements pour la feuille de calcul.
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();
                $daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;
                $totalColumns = 2 + $daysInMonth + 1 + count($this->zoneRates) + 1; // 2 colonnes (A et B) + jours + total + zones + panier
                $highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);

                // *** Appliquer la largeur de colonnes spécifique à WorkerMonthly ***
                $this->setWorkerColumnWidths($sheet, $totalColumns);

                // *** Appliquer l'alignement spécifique à WorkerMonthly ***
                $this->setWorkerAlignment($sheet, $highestColumn, $highestRow);

                // *** Format personnalisé pour les heures avec le suffixe " H" (sauf colonne B) ***
                $this->applyWorkerNumberFormat($sheet, $daysInMonth, $highestRow);

                // *** Ajouter des bordures à toute la plage de données ***
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // *** Appliquer la coloration des weekends et fériés ***
                $this->styleService->applyWeekendColoring($sheet, $this->month, $this->year, $highestRow, 2);
                
                if (!empty($this->holidays)) {
                    $this->styleService->applyHolidayColoring($sheet, $this->holidays, $highestRow, 2);
                }

                // *** Appliquer les styles spécifiques aux workers ***
                $this->styleService->applyWorkerRowStyles($sheet, $this->workerRows, $highestColumn);
                $this->styleService->applyAbsenceColoring($sheet, $this->workerRows, $totalColumns);
                // *** Appliquer la coloration conditionnelle des tarifs selon jour/nuit (par-dessus l'orange) ***
                $totalColumnIndex = 2 + $daysInMonth + 1; // Limite aux colonnes de jours + total
                $this->styleService->applyProjectHoursColoring($sheet, 2, $highestRow, $totalColumns, $totalColumnIndex);

                // *** Appliquer le background orange à toute la colonne B ***
                $this->styleService->applyColumnColoring($sheet, 'B', 1, $highestRow, 'F4A471');

                // *** Appliquer le texte vertical centré au header "DEPLACEMENT" ***
                $this->styleService->applyVerticalText($sheet, 'B1');

                // *** Appliquer le texte multi-lignes centré au header "TOTAL HEURES TRAVAILLEES" ***
                $totalColumnIndex = 2 + $daysInMonth + 1; // Index de la colonne TOTAL
                $totalColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumnIndex);
                $this->styleService->applyMultiLineText($sheet, "{$totalColumn}1");

                // *** Appliquer la coloration conditionnelle des tarifs selon jour/nuit (par-dessus l'orange) ***
                $this->styleService->applyRateColoring($sheet, 2, $highestRow, $this->projectCategories);
                
                // *** Augmenter la hauteur du header ***
                $this->styleService->setRowHeights($sheet, 1, 1, 84);

                // *** Centrer tous les headers ***
                $this->styleService->applyCenteredAlignment($sheet, "A1:{$highestColumn}1");

                // *** Appliquer le background jaune à la cellule PANIER (dernière colonne) ***
                $panierColumnIndex = $totalColumns; // PANIER est la dernière colonne
                $panierColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($panierColumnIndex);
                $sheet->getStyle("{$panierColumn}1")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('FFF3C7'); // Jaune comme les workers

                // *** Figer la première ligne (header) pour qu'elle reste visible au scroll ***
                $sheet->freezePane('A2');
                
                // *** Styles pour les lignes de total des workers ***
                foreach ($this->workerTotalRows as $row) {
                    
                    // Gras sur toute la ligne
                    $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->getFont()->setBold(true);
                    
                    // Background gris sur toute la ligne
                    $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('E8E8E8'); // Gris clair
                    
                    // Background bleu ciel UNIQUEMENT sur la cellule de total (par-dessus le gris)
                    $sheet->getStyle("{$highestColumn}{$row}")
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('87CEEB'); // Bleu ciel
                    
                    // *** SUPPRIMER TOUTES LES BORDURES DE LA LIGNE DE TOTAL D'ABORD ***
                    $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);
                    
                    // *** AJOUTER SEULEMENT LES BORDURES VOULUES ***
                    // Bordure normale UNIQUEMENT sur la cellule bleu ciel (total)
                    $sheet->getStyle("{$highestColumn}{$row}")
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                        ->getColor()
                        ->setRGB('000000'); // Noir
                    
                    // Bordure fine en bas de toute la ligne
                    $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                        ->getBorders()
                        ->getBottom()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                        ->getColor()
                        ->setRGB('000000'); // Noir
                }
                
                // *** Appliquer le style gris à la ligne TOTAL finale ***
                $sheet->getStyle("A{$highestRow}:{$highestColumn}{$highestRow}")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('E8E8E8'); // Gris clair

                // *** Supprimer les bordures et le fond des lignes vides ***
                $this->styleService->removeEmptyRowsStyling($sheet, $highestRow, $highestColumn);

                // *** Mettre en gras la dernière ligne (Total) ***
                $this->styleService->applyBoldToRow($sheet, $highestRow, $highestColumn);
            },
        ];
    }


    /**
     * Définit la largeur des colonnes spécifique à WorkerMonthlyExport
     */
    private function setWorkerColumnWidths(Worksheet $sheet, int $totalColumns): void
    {
        $daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;
        $totalColumnIndex = 2 + $daysInMonth + 1; // Index de la colonne TOTAL (A=1, B=2, jours, puis TOTAL)
        
        $sheet->getColumnDimension('A')->setAutoSize(true); // SALARIES / Chantiers
        $sheet->getColumnDimension('B')->setWidth(8);      // Colonne B pour tarifs zone

        // Auto-ajuster les colonnes des jours uniquement
        for ($i = 3; $i < $totalColumnIndex; $i++) { // Colonnes C à avant TOTAL (jours seulement)
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Largeur ajustée pour la colonne TOTAL
        $totalColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumnIndex);
        $sheet->getColumnDimension($totalColumn)->setWidth(18); // Largeur suffisante pour "TRAVAILLEES"
        
        // Largeur pour les colonnes de zones (après TOTAL)
        for ($i = $totalColumnIndex + 1; $i <= $totalColumns; $i++) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($column)->setWidth(8); // Largeur pour tarifs de zones et PANIER
        }
    }

    /**
     * Définit l'alignement spécifique à WorkerMonthlyExport
     */
    private function setWorkerAlignment(Worksheet $sheet, string $highestColumn, int $highestRow): void
    {
        // Alignement des premières lignes (Titre et En-têtes) au centre
        $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Alignement de la colonne A (noms) à gauche
        $sheet->getStyle("A2:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        // Alignement de la colonne B (déplacements) au centre
        $sheet->getStyle("B2:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Alignement des colonnes des heures, totaux et zones (C à fin) au centre
        $daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;
        $totalColumnsForAlignment = 2 + $daysInMonth + 1 + count($this->zoneRates) + 1; // A + B + jours + total + zones + panier
        for ($i = 3; $i <= $totalColumnsForAlignment; $i++) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getStyle("{$column}2:{$column}{$highestRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
    }

    /**
     * Applique le format numérique spécifique à WorkerMonthlyExport
     * Colonne B: pas de format (tarifs zone)
     * Colonnes C+: format avec " H" (heures)
     */
    private function applyWorkerNumberFormat(Worksheet $sheet, int $daysInMonth, int $highestRow): void
    {
        $totalColumns = 2 + $daysInMonth + 1; // A + B + jours + total

        // Pas besoin de format spécial car les valeurs sont déjà formatées
    }

    /**
     * Formate les heures en supprimant les zéros inutiles
     * 5.00 -> 5, 7.50 -> 7.5, 10.25 -> 10.25
     */
    private function formatHours($hours): string
    {
        if (!is_numeric($hours) || $hours == 0) {
            return '';
        }
        
        // Convertir en float puis formater
        $formatted = (float) $hours;
        
        // Si c'est un nombre entier, ne pas afficher de décimales
        if ($formatted == intval($formatted)) {
            return (string) intval($formatted);
        }
        
        // Sinon, formater avec décimales mais supprimer les zéros à la fin
        return rtrim(rtrim(number_format($formatted, 2, '.', ''), '0'), '.');
    }
    
    /**
     * Récupère tous les tarifs de zones uniques utilisés ce mois
     */
    private function getUniqueZoneRates(): array
    {
        // Récupérer tous les tarifs de zones (pas seulement ceux utilisés ce mois)
        $rates = Zone::distinct()
            ->pluck('rate')
            ->map(function ($rate) {
                return floatval($rate);
            })
            ->sort()
            ->values()
            ->toArray();
        
        return $rates;
    }
    
    /**
     * Formate un tarif de zone pour l'affichage en header
     */
    private function formatZoneRate(float $rate): string
    {
        return rtrim(rtrim(number_format($rate, 2, '.', ''), '0'), '.');
    }
    
    /**
     * Calcule les totaux par tarif de zone pour un worker
     */
    private function calculateWorkerZoneTotals($timeSheets): array
    {
        $zoneTotals = [];
        
        foreach ($timeSheets as $timeSheet) {
            $project = Project::with('zone')->find($timeSheet->project_id);
            if ($project && $project->zone) {
                $zoneRate = floatval($project->zone->rate);
                $zoneTotals[$zoneRate] = ($zoneTotals[$zoneRate] ?? 0) + $timeSheet->hours;
            }
        }
        
        return $zoneTotals;
    }
    
    /**
     * Calcule les totaux généraux par tarif de zone
     */
    private function calculateGeneralZoneTotals(): array
    {
        $zoneTotals = [];
        
        foreach ($this->zoneRates as $rate) {
            $total = TimeSheetable::where('timesheetable_type', Worker::class)
                ->whereYear('date', $this->year)
                ->whereMonth('date', $this->month)
                ->whereHas('project.zone', function ($query) use ($rate) {
                    $query->where('rate', $rate);
                })
                ->sum('hours');
            
            if ($total > 0) {
                $zoneTotals[$rate] = $total;
            }
        }
        
        return $zoneTotals;
    }
    
    /**
     * Calcule les jours par tarif de zone pour un worker
     */
    private function calculateWorkerZoneDays($timeSheets): array
    {
        $zoneDays = [];
        
        foreach ($timeSheets as $timeSheet) {
            $project = Project::with('zone')->find($timeSheet->project_id);
            if ($project && $project->zone && $timeSheet->hours > 0) {
                $zoneRate = floatval($project->zone->rate);
                $zoneDays[$zoneRate] = ($zoneDays[$zoneRate] ?? 0) + 1; // Compter 1 jour par timesheet avec heures > 0
            }
        }
        
        return $zoneDays;
    }
    
    /**
     * Calcule les totaux généraux par tarif de zone (en jours)
     */
    private function calculateGeneralZoneDays(): array
    {
        $zoneDays = [];
        
        foreach ($this->zoneRates as $rate) {
            $count = TimeSheetable::where('timesheetable_type', Worker::class)
                ->whereYear('date', $this->year)
                ->whereMonth('date', $this->month)
                ->where('hours', '>', 0)
                ->whereHas('project.zone', function ($query) use ($rate) {
                    $query->where('rate', $rate);
                })
                ->count();
            
            if ($count > 0) {
                $zoneDays[$rate] = $count;
            }
        }
        
        return $zoneDays;
    }
    
    /**
     * Calcule le nombre de jours à partir des heures
     */
    private function calculateDaysFromHours($hours): int
    {
        return $hours > 0 ? 1 : 0;
    }
}
