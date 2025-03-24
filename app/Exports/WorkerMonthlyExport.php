<?php

namespace App\Exports;

use App\Models\Worker;
use App\Models\TimeSheetable;
use App\Models\Project;
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

    // Propriété pour stocker les numéros de ligne des salariés
    protected $workerRows = [];

    public function __construct($month, $year, $holidays = [])
    {
        $this->month = $month;
        $this->year = $year;
        $this->holidays = $holidays;
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

        $data = [];

        // *** Ligne d'en-têtes directement ***
        $headerRow = ["DUBOCQ OUVRIER {$dateFormatted}", '']; // Colonne B vide
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $headerRow[] = $day;
        }
        $headerRow[] = 'TOTAL HEURES TRAVAILLEES';
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

            // *** Ajouter une ligne pour le nom du worker ***
            $workerNameRow = [$worker->last_name . ' ' . $worker->first_name, ''];

            // Remplir les colonnes des jours avec les statuts
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $workerNameRow[] = $workerDayStatus[$day];
            }

            // Ajouter le total des heures pour le worker
            $workerNameRow[] = $totalWorkerHours > 0 ? $totalWorkerHours : null;

            // Avant d'ajouter la ligne du salarié, enregistrer le numéro de ligne
            $currentRow = count($data) + 1; // Numéro de ligne actuel
            $this->workerRows[] = $currentRow;

            $data[] = $workerNameRow;

            // *** Ajouter les lignes des projets sous chaque worker ***
            // Regrouper les pointages par projet
            $projectGroups = $timeSheets->groupBy('project_id');

            foreach ($projectGroups as $projectId => $projectTimeSheets) {
                $project = Project::find($projectId);

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

                // *** Ligne des heures de jour ***
                if ($totalProjectDayHours > 0) {
                    $dayRow = ['    ' . $projectDetails, '']; // Indentation pour différencier
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $dayRow[] = isset($projectDayHours[$day]) ? $projectDayHours[$day] : null;
                    }
                    $dayRow[] = $totalProjectDayHours > 0 ? $totalProjectDayHours : null;
                    $data[] = $dayRow;
                }

                // *** Ligne des heures de nuit (si présentes) ***
                if ($totalProjectNightHours > 0) {
                    $nightRow = ['    ' . $projectDetails . ' (Nuit)', ''];
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $nightRow[] = isset($projectNightHours[$day]) ? $projectNightHours[$day] : null;
                    }
                    $nightRow[] = $totalProjectNightHours > 0 ? $totalProjectNightHours : null;
                    $data[] = $nightRow;
                }
            }

            // *** Ajouter une ligne vide après chaque worker pour une meilleure lisibilité ***
            $data[] = array_fill(0, $daysInMonth + 3, '');
        }

        // *** Ajouter une ligne totale pour tout le mois ***
        $totalRow = ['TOTAL', ''];
        $monthlyTotal = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($this->year, $this->month, $day)->format('Y-m-d');

            $dayTotal = TimeSheetable::where('timesheetable_type', Worker::class)
                ->where('date', $date)
                ->sum('hours');

            $monthlyTotal += $dayTotal;
            $totalRow[] = $dayTotal > 0 ? $dayTotal : null;
        }

        $totalRow[] = $monthlyTotal > 0 ? $monthlyTotal : null;
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

                // *** Définir la largeur des colonnes ***
                $sheet->getColumnDimension('A')->setAutoSize(true); // SALARIES / Chantiers
                $sheet->getColumnDimension('B')->setWidth(5);      // Colonne B vide ou minimale

                // Auto-ajuster les colonnes restantes (jours et total)
                $highestRow = $sheet->getHighestRow();
                $daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;
                $totalColumns = 2 + $daysInMonth + 1; // 2 colonnes (A et B) + jours + total

                for ($i = 3; $i <= $totalColumns; $i++) { // Colonnes C à ... (jours + Total)
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // *** Alignement des premières lignes (Titre et En-têtes) au centre ***
                $highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);
                $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // *** Alignement des autres cellules à gauche ***
                $sheet->getStyle("A2:{$highestColumn}{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // *** Format personnalisé pour les heures avec le suffixe " H" ***
                for ($i = 3; $i <= $totalColumns; $i++) { // Colonnes C à ... (jours + Total)
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    // Définir le format personnalisé : nombre avec deux décimales suivi de " H"
                    $sheet->getStyle("{$column}2:{$column}{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('0.00" H"');
                }

                // *** Ajouter des bordures à toute la plage de données ***
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // *** Griser les jours de weekend ***
                $this->greyOutWeekends($sheet, $highestColumn, $highestRow);

                // *** Colorer les jours fériés ***
                if (!empty($this->holidays)) {
                    $this->colorOutHolidays($sheet, $this->holidays);
                }

                // *** Appliquer le style gras aux lignes des workers ***
                // Appliquer le gras uniquement aux numéros de ligne des workers stockés
                foreach ($this->workerRows as $row) {
                    // Mettre en gras toute la ligne du salarié
                    $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->getFont()->setBold(true);

                    // Appliquer un fond jaune uniquement à la cellule du nom du salarié (colonne A)
                    $sheet->getStyle("A{$row}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('FFF3C7'); // Jaune
                }

                // *** Appliquer les couleurs de fond aux cellules spécifiques ***
                for ($row = 2; $row <= $highestRow; $row++) {
                    $cellAValue = $sheet->getCell("A{$row}")->getValue();
                    if (!$cellAValue) continue;

                    $trimmedCellAValue = trim($cellAValue);

                    // Vérifier si c'est une ligne de salarié
                    if (in_array($row, $this->workerRows)) {
                        // Vérifier les cellules contenant "abs" et appliquer un fond rouge
                        for ($col = 3; $col <= $totalColumns - 1; $col++) { // Exclure la colonne Total
                            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                            $cellValue = $sheet->getCell("{$columnLetter}{$row}")->getValue();
                            if ($cellValue === 'abs') {
                                // Appliquer un fond rouge
                                $sheet->getStyle("{$columnLetter}{$row}")
                                    ->getFill()
                                    ->setFillType(Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setRGB('FFCCCC'); // Rouge
                            }
                        }
                    } elseif (strpos($cellAValue, '    ') === 0) { // Ligne de projet (indentée)
                        if (strpos($trimmedCellAValue, '(Nuit)') !== false) {
                            // Ligne des heures de nuit
                            // Appliquer un fond violet aux cellules avec des valeurs numériques
                            for ($col = 3; $col <= $totalColumns - 1; $col++) { // Exclure la colonne Total
                                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                                $cellValue = $sheet->getCell("{$columnLetter}{$row}")->getValue();
                                if (is_numeric($cellValue)) {
                                    // Appliquer un fond violet
                                    $sheet->getStyle("{$columnLetter}{$row}")
                                        ->getFill()
                                        ->setFillType(Fill::FILL_SOLID)
                                        ->getStartColor()
                                        ->setRGB('E6CCFF'); // Violet
                                }
                            }
                        } else {
                            // Ligne des heures de jour
                            // Appliquer un fond vert aux cellules avec des valeurs numériques
                            for ($col = 3; $col <= $totalColumns - 1; $col++) { // Exclure la colonne Total
                                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                                $cellValue = $sheet->getCell("{$columnLetter}{$row}")->getValue();
                                if (is_numeric($cellValue)) {
                                    // Appliquer un fond vert
                                    $sheet->getStyle("{$columnLetter}{$row}")
                                        ->getFill()
                                        ->setFillType(Fill::FILL_SOLID)
                                        ->getStartColor()
                                        ->setRGB('CCFFCC'); // Vert
                                }
                            }
                        }
                    }
                }

                // *** Supprimer les bordures et le fond des lignes vides ***
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellA = trim($sheet->getCell("A{$row}")->getValue() ?? '');
                    $cellB = trim($sheet->getCell("B{$row}")->getValue() ?? '');

                    // Si les colonnes A et B sont vides, considérer la ligne comme vide
                    if ($cellA === '' && $cellB === '') {
                        // Définir la plage de la ligne
                        $range = "A{$row}:{$highestColumn}{$row}";

                        // Supprimer toutes les bordures
                        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);

                        // Supprimer le fond gris (fill)
                        $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_NONE);
                    }
                }

                // *** Mettre en gras la dernière ligne (Total) ***
                $sheet->getStyle("A{$highestRow}:{$highestColumn}{$highestRow}")->getFont()->setBold(true);
            },
        ];
    }

    /**
     * Fonction pour griser les colonnes correspondant aux weekends.
     *
     * @param Worksheet $sheet
     * @param string $highestColumn
     * @param int $highestRow
     * @return void
     */
    protected function greyOutWeekends(Worksheet $sheet, string $highestColumn, int $highestRow): void
    {
        // Déterminer le premier jour du mois
        $firstDayOfMonth = Carbon::create($this->year, $this->month, 1);

        // Parcourir tous les jours du mois
        $daysInMonth = $firstDayOfMonth->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($this->year, $this->month, $day);
            $dayOfWeek = $currentDate->dayOfWeek; // 0 (dimanche) à 6 (samedi)

            if ($dayOfWeek == Carbon::SATURDAY || $dayOfWeek == Carbon::SUNDAY) {
                // Calculer l'index de la colonne correspondant au jour
                $columnIndex = 2 + $day; // 2 colonnes avant les jours
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);

                // Appliquer un fond gris à la colonne entière (à partir de la ligne 1 pour inclure l'en-tête)
                $range = "{$columnLetter}1:{$columnLetter}{$highestRow}";
                $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D3D3D3'); // Gris clair
            }
        }
    }

    /**
     * Colorie les colonnes correspondant aux jours fériés
     * 
     * @param Worksheet $sheet
     * @param array $holidays
     * @return void
     */
    public function colorOutHolidays(Worksheet $sheet, $holidays): void
    {
        foreach ($holidays as $holiday) {
            // Obtenez la colonne correspondant au jour férié
            $day = $holiday->date->day;
            $columnIndex = 2 + $day; // Les colonnes commencent après les colonnes "SALARIES" et une colonne vide
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);

            // Définir la couleur en fonction du type de jour non travaillé
            $fillColor = match ($holiday->type ?? 'Férié') {
                'Férié' => 'FFFFCC',    // Jaune clair
                default => 'FF6347',    // Rouge clair
            };

            // Appliquez le style de remplissage
            $sheet->getStyle("{$columnLetter}1:{$columnLetter}" . $sheet->getHighestRow())
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB($fillColor);
        }
    }
}
