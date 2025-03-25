<?php

namespace App\Exports;

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

class BlankMonthlyExport implements FromArray, WithStyles, WithEvents
{
    protected $month;
    protected $year;
    protected $project;
    protected $holidays;

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
                $highestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);

                // S'assurer que nous avons 32 lignes au total (2 lignes d'en-tête + 30 lignes de données)
                $highestRow = 32;

                // S'assurer que les 30 lignes du tableau sont visibles en définissant leur hauteur
                for ($i = 3; $i <= $highestRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(20); // Hauteur fixe pour toutes les lignes
                }

                // *** Définir la largeur des colonnes ***
                $sheet->getColumnDimension('A')->setWidth(30); // Colonne des noms plus large

                // Largeur fixe pour les colonnes des jours
                for ($i = 2; $i < $totalColumns; $i++) {
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $sheet->getColumnDimension($column)->setWidth(5);
                }

                // Largeur de la colonne TOTAL pour s'adapter au texte
                $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);
                $sheet->getColumnDimension($lastColumn)->setAutoSize(true);

                // *** Alignement des cellules ***
                // Centrer l'en-tête (ligne 1 et ligne 2)
                $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("A2:{$highestColumn}2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Centrer les cellules des jours (colonnes B à la fin)
                for ($i = 2; $i <= $totalColumns; $i++) {
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $sheet->getStyle("{$column}3:{$column}{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Aligner à gauche la colonne A à partir de la ligne 3
                $sheet->getStyle("A3:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // *** Format des nombres pour les cellules de données ***
                // Colonnes des jours permettant des valeurs numériques
                for ($i = 2; $i < $totalColumns; $i++) {
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $sheet->getStyle("{$column}3:{$column}{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('0.00');
                }

                // Ajouter des formules conditionnelles de TOTAL pour chaque ligne
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

                // *** IMPORTANT: Définir explicitement les cellules vides pour s'assurer que les bordures sont visibles ***
                for ($r = 3; $r <= $highestRow; $r++) {
                    for ($c = 1; $c <= $totalColumns; $c++) {
                        $cellRef = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c) . $r;
                        if (!$sheet->getCell($cellRef)->getValue()) {
                            // Définir explicitement comme chaîne vide si la cellule est null
                            $sheet->setCellValue($cellRef, '');
                        }
                    }
                }

                // *** Ajouter des bordures à toutes les cellules ***
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // *** Griser les weekends pour toutes les lignes ***
                $this->greyOutWeekends($sheet, $highestColumn, $highestRow);

                // *** Colorer les jours fériés si définis ***
                if (!empty($this->holidays)) {
                    $this->colorOutHolidays($sheet, $this->holidays);
                }
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
                $columnIndex = 1 + $day; // 1 colonne avant les jours
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);

                // Appliquer un fond gris à la colonne entière (à partir de la ligne 2 pour inclure l'en-tête)
                $range = "{$columnLetter}2:{$columnLetter}{$highestRow}";
                $sheet->getStyle($range)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('D3D3D3'); // Gris clair
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
            // Obtenir la colonne correspondant au jour férié
            $day = $holiday->date->day;
            $columnIndex = 1 + $day; // Les colonnes commencent après la colonne A
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);

            // Définir la couleur en fonction du type de jour non travaillé
            $fillColor = match ($holiday->type ?? 'Férié') {
                'Férié' => 'FFFFCC',    // Jaune clair
                default => 'FF6347',    // Rouge clair
            };

            // Appliquer le style de remplissage
            $sheet->getStyle("{$columnLetter}2:{$columnLetter}" . $sheet->getHighestRow())
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB($fillColor);
        }
    }
}
