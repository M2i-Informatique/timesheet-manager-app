<?php

namespace App\Exports;

use App\Models\Project;
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

class BlankMonthlyExport implements FromArray, WithStyles, WithEvents
{
    protected $month;
    protected $year;
    protected $project;
    protected $holidays;
    protected $styleService;

    public function __construct($month, $year, $holidays = [], Project $project = null, ExcelStyleService $styleService = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->holidays = $holidays;
        $this->project = $project;
        $this->styleService = $styleService ?? app(ExcelStyleService::class);
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

        // *** Première ligne : En-tête général ***
        $headerTitle = $this->project
            ? "{$this->project->code} - {$this->project->name} - {$this->project->address} - " . strtoupper($this->project->city)
            : "Code - Nom - Adresse - VILLE";

        $firstRow = [$headerTitle];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $firstRow[] = '';
        }
        // Ajouter une colonne pour "TOTAL" à la fin
        $firstRow[] = '';
        $data[] = $firstRow;

        // *** Deuxième ligne : En-têtes avec numéros des jours ***
        // Formater le mois et l'année
        $monthFormatted = str_pad($this->month, 2, '0', STR_PAD_LEFT);
        $yearFormatted = substr($this->year, -2); // Prend les deux derniers chiffres de l'année
        $dateFormatted = "{$monthFormatted}/{$yearFormatted}";

        $headerRow = ["DUBOCQ OUVRIERS {$dateFormatted}"];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $headerRow[] = $day;
        }
        // Ajouter une colonne "TOTAL" à la fin
        $headerRow[] = "TOTAL";
        $data[] = $headerRow;

        // *** Ajouter 30 lignes vides pour les données ***
        for ($i = 0; $i < 30; $i++) {
            $row = [];
            // Première colonne (A) - laisser vide
            $row[] = '';

            // Colonnes des jours (B à la fin)
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $row[] = '';
            }

            // Dernière colonne (TOTAL) - laissée vide
            $row[] = '';

            $data[] = $row;
        }

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
        $daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;
        $totalColumns = $daysInMonth + 2; // +1 pour colonne A et +1 pour colonne TOTAL
        $highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);

        // *** Fusionner les cellules de la première ligne (Titre) ***
        $sheet->mergeCells("A1:{$highestColumn}1");

        // *** Appliquer les styles à la première ligne (Titre) ***
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 20, // Taille de police augmentée à 20
                'name' => 'Calibri',
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // *** Appliquer le gras à la ligne d'en-tête ***
        $sheet->getStyle("A2:{$highestColumn}2")->getFont()->setBold(true);

        return [];
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

                // Calculer le nombre de colonnes basé sur le nombre de jours dans le mois
                $daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;
                $totalColumns = $daysInMonth + 2; // +1 pour colonne A et +1 pour colonne TOTAL
                $highestRow = 32; // 2 lignes d'en-tête + 30 lignes de données

                // *** Appliquer les styles de base via le service ***
                $this->styleService->applyBaseStyles($sheet, $daysInMonth, $highestRow);
                $this->styleService->setRowHeights($sheet, 3, $highestRow, 20);
                $this->styleService->applyNumberFormat($sheet, $daysInMonth, $highestRow, '0.00');

                // *** Assurer que toutes les cellules sont définies pour les bordures ***
                $this->styleService->ensureEmptyCells($sheet, 3, $highestRow, $totalColumns);

                // *** Ajouter les formules de TOTAL ***
                $this->addTotalFormulas($sheet, $totalColumns, $highestRow);

                // *** Appliquer la coloration des weekends et fériés ***
                $this->styleService->applyWeekendColoring($sheet, $this->month, $this->year, $highestRow, 1);
                
                if (!empty($this->holidays)) {
                    $this->styleService->applyHolidayColoring($sheet, $this->holidays, $highestRow, 1);
                }
            },
        ];
    }

    /**
     * Ajoute les formules de total pour chaque ligne
     */
    private function addTotalFormulas(Worksheet $sheet, int $totalColumns, int $highestRow): void
    {
        for ($row = 3; $row <= $highestRow; $row++) {
            // Créer la formule de somme pour la ligne
            $sumRange = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(2) . $row .
                ':' .
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns - 1) . $row;

            // Définir une formule conditionnelle qui ne calcule le total que si des heures sont saisies
            $sheet->setCellValue(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns) . $row,
                "=IF(SUMPRODUCT(--(({$sumRange})<>\"\")),SUM({$sumRange}),\"\")"
            );
        }
    }
}
