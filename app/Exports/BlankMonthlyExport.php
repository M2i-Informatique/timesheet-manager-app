<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\Worker;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BlankMonthlyExport implements FromArray, WithStyles, WithEvents
{
    protected $month;
    protected $year;
    protected $project;
    protected $holidays;

    // Propriété pour stocker les numéros de ligne des salariés
    protected $workerRows = [];

    public function __construct($month, $year, $holidays = [], Project $project = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->holidays = $holidays;
        $this->project = $project;
    }

    /**
     * Retourne la collection de données pour l'export.
     *
     * @return array
     */
    public function array(): array
    {
        // Déterminer le nombre de jours dans le mois sélectionné
        $daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;

        $data = [];

        // *** Première ligne : Titre du projet ***
        $projectName = "";
        if ($this->project) {
            $projectName = $this->project->name . " - " . $this->project->city . " (" . $this->project->code . ")";
        }
        $monthName = strtoupper(Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y'));
        $firstRow = [$projectName . " - " . $monthName];
        for ($i = 1; $i <= $daysInMonth + 2; $i++) { // +2 pour "SALARIES" et "TOTAL HEURES TRAVAILLEES"
            $firstRow[] = '';
        }
        $data[] = $firstRow;

        // *** Ajouter une ligne vide entre la première ligne et les en-têtes ***
        $data[] = array_fill(0, $daysInMonth + 3, '');

        // *** Troisième ligne : En-têtes ***
        $headerRow = ['SALARIES', '']; // Colonne B vide
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $headerRow[] = $day;
        }
        $headerRow[] = 'TOTAL HEURES TRAVAILLEES';
        $data[] = $headerRow;

        // Si un projet est spécifié, récupérer les travailleurs assignés à ce projet
        if ($this->project) {
            $workers = $this->project->workers()->where('status', 'active')->get();
        } else {
            $workers = Worker::where('status', 'active')->get();
        }

        // Parcourir chaque worker pour ajouter une ligne vide
        foreach ($workers as $worker) {
            // *** Ajouter une ligne pour le nom du worker ***
            $workerNameRow = [$worker->last_name . ' ' . $worker->first_name, ''];

            // Remplir les colonnes des jours avec des cellules vides
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $workerNameRow[] = '';
            }

            // Colonne du total (vide également)
            $workerNameRow[] = '';

            // Avant d'ajouter la ligne du salarié, enregistrer le numéro de ligne
            $currentRow = count($data) + 1; // Numéro de ligne actuel
            $this->workerRows[] = $currentRow;

            $data[] = $workerNameRow;

            // Ajouter une ligne pour les heures de jour (vide)
            $dayRow = ['    Heures Jour', ''];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dayRow[] = '';
            }
            $dayRow[] = ''; // Total
            $data[] = $dayRow;

            // Ajouter une ligne pour les heures de nuit (vide)
            $nightRow = ['    Heures Nuit', ''];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $nightRow[] = '';
            }
            $nightRow[] = ''; // Total
            $data[] = $nightRow;

            // *** Ajouter une ligne vide après chaque worker pour une meilleure lisibilité ***
            $data[] = array_fill(0, $daysInMonth + 3, '');
        }

        // *** Ajouter une ligne totale pour tout le mois ***
        $totalRow = ['TOTAL', ''];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $totalRow[] = '';
        }
        $totalRow[] = '';
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

        // *** Fusionner les cellules de la première ligne (Titre) ***
        $sheet->mergeCells("A1:" . $highestColumn . "1");

        // *** Appliquer les styles à la première ligne (Titre) ***
        $sheet->getStyle("A1:" . $highestColumn . "1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'name' => 'Calibri',
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // *** Appliquer le gras aux en-têtes (ligne "SALARIES" et jours) ***
        $sheet->getStyle("A3:" . $highestColumn . "3")->getFont()->setBold(true);

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
                $sheet->getStyle("A3:{$highestColumn}3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // *** Alignement des autres cellules à gauche ***
                $sheet->getStyle("A4:{$highestColumn}{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // *** Format personnalisé pour les heures avec le suffixe " H" ***
                for ($i = 3; $i <= $totalColumns; $i++) { // Colonnes C à ... (jours + Total)
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    // Définir le format personnalisé : nombre avec deux décimales suivi de " H"
                    $sheet->getStyle("{$column}4:{$column}{$highestRow}")
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

                // *** Appliquer des couleurs différentes pour les lignes heures jour et nuit ***
                for ($row = 4; $row <= $highestRow; $row++) {
                    $cellAValue = $sheet->getCell("A{$row}")->getValue();
                    $trimmedCellAValue = trim($cellAValue);

                    if (strpos($cellAValue, '    Heures Jour') === 0) {
                        // Appliquer un fond vert clair à la ligne des heures de jour
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('E0FFE0'); // Vert très clair
                    } elseif (strpos($cellAValue, '    Heures Nuit') === 0) {
                        // Appliquer un fond violet clair à la ligne des heures de nuit
                        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('E6E6FF'); // Violet très clair
                    }
                }

                // *** Supprimer les bordures et le fond des lignes vides ***
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellA = trim($sheet->getCell("A{$row}")->getValue());
                    $cellB = trim($sheet->getCell("B{$row}")->getValue());

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

                // Appliquer un fond gris à la colonne entière (à partir de la ligne 3 pour inclure l'en-tête)
                $range = "{$columnLetter}3:{$columnLetter}{$highestRow}";
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
            $sheet->getStyle("{$columnLetter}3:{$columnLetter}" . $sheet->getHighestRow())
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB($fillColor);
        }
    }
}
