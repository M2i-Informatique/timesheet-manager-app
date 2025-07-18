<?php

namespace App\Services\Export;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;

/**
 * Service pour centraliser tous les styles Excel
 * Évite la duplication de code entre BlankMonthlyExport et WorkerMonthlyExport
 */
class ExcelStyleService
{
    /**
     * Applique les styles de base communs : largeur colonnes, alignement, bordures
     */
    public function applyBaseStyles(Worksheet $sheet, int $daysInMonth, int $totalRows): void
    {
        $totalColumns = $daysInMonth + 2; // +1 pour colonne A et +1 pour colonne TOTAL
        $highestColumn = Coordinate::stringFromColumnIndex($totalColumns);

        // Définir la largeur des colonnes
        $this->setColumnWidths($sheet, $totalColumns);

        // Alignement des cellules
        $this->setCellAlignment($sheet, $highestColumn, $totalRows, $totalColumns);

        // Ajouter des bordures à toutes les cellules
        $sheet->getStyle("A1:{$highestColumn}{$totalRows}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }

    /**
     * Définit la largeur des colonnes
     */
    public function setColumnWidths(Worksheet $sheet, int $totalColumns): void
    {
        // Colonne des noms plus large
        $sheet->getColumnDimension('A')->setWidth(30);

        // Largeur fixe pour les colonnes des jours
        for ($i = 2; $i < $totalColumns; $i++) {
            $column = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($column)->setWidth(5);
        }

        // Largeur de la colonne TOTAL pour s'adapter au texte
        $lastColumn = Coordinate::stringFromColumnIndex($totalColumns);
        $sheet->getColumnDimension($lastColumn)->setAutoSize(true);
    }

    /**
     * Configure l'alignement des cellules
     */
    public function setCellAlignment(Worksheet $sheet, string $highestColumn, int $totalRows, int $totalColumns): void
    {
        // Centrer l'en-tête (ligne 1 et ligne 2)
        $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A2:{$highestColumn}2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Centrer les cellules des jours (colonnes B à la fin)
        for ($i = 2; $i <= $totalColumns; $i++) {
            $column = Coordinate::stringFromColumnIndex($i);
            $sheet->getStyle("{$column}3:{$column}{$totalRows}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Aligner à gauche la colonne A à partir de la ligne 3
        $sheet->getStyle("A3:A{$totalRows}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }

    /**
     * Grise les colonnes correspondant aux weekends
     */
    public function applyWeekendColoring(Worksheet $sheet, int $month, int $year, int $totalRows, int $columnOffset = 1): void
    {
        $firstDayOfMonth = Carbon::create($year, $month, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::create($year, $month, $day);
            $dayOfWeek = $currentDate->dayOfWeek;

            if ($dayOfWeek == Carbon::SATURDAY || $dayOfWeek == Carbon::SUNDAY) {
                $columnIndex = $columnOffset + $day;
                $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);

                // Déterminer la ligne de départ selon le type d'export
                $startRow = ($columnOffset === 1) ? 2 : 1; // BlankMonthly vs WorkerMonthly

                $range = "{$columnLetter}{$startRow}:{$columnLetter}{$totalRows}";
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
     */
    public function applyHolidayColoring(Worksheet $sheet, $holidays, int $totalRows, int $columnOffset = 1): void
    {
        foreach ($holidays as $holiday) {
            $day = $holiday->date->day;
            $columnIndex = $columnOffset + $day;
            $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);

            // Définir la couleur en fonction du type
            $fillColor = match ($holiday->type ?? 'Férié') {
                'Férié' => 'FFFFCC',    // Jaune clair
                default => 'FF6347',    // Rouge clair
            };

            // Déterminer la ligne de départ selon le type d'export
            $startRow = ($columnOffset === 1) ? 2 : 1;

            $range = "{$columnLetter}{$startRow}:{$columnLetter}{$totalRows}";
            $sheet->getStyle($range)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB($fillColor);
        }
    }

    /**
     * Applique le format numérique aux colonnes de jours
     */
    public function applyNumberFormat(Worksheet $sheet, int $daysInMonth, int $totalRows, string $format = '0.00'): void
    {
        $totalColumns = $daysInMonth + 2;

        for ($i = 2; $i < $totalColumns; $i++) {
            $column = Coordinate::stringFromColumnIndex($i);
            $sheet->getStyle("{$column}3:{$column}{$totalRows}")
                ->getNumberFormat()
                ->setFormatCode($format);
        }
    }

    /**
     * Applique les styles spécifiques aux lignes de workers (pour WorkerMonthlyExport)
     */
    public function applyWorkerRowStyles(Worksheet $sheet, array $workerRows, string $highestColumn): void
    {
        foreach ($workerRows as $row) {
            // Mettre en gras toute la ligne du worker
            $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->getFont()->setBold(true);

            // Appliquer un fond jaune à la cellule du nom (colonne A)
            $sheet->getStyle("A{$row}")
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('FFF3C7'); // Jaune
        }
    }

    /**
     * Applique les couleurs pour les heures jour/nuit et absences (pour WorkerMonthlyExport)
     */
    public function applyProjectHoursColoring(Worksheet $sheet, int $startRow, int $endRow, int $totalColumns): void
    {
        for ($row = $startRow; $row <= $endRow; $row++) {
            $cellAValue = $sheet->getCell("A{$row}")->getValue();
            if (!$cellAValue) continue;

            $trimmedCellAValue = trim($cellAValue);

            // Lignes de projet (indentées avec 4 espaces)
            if (strpos($cellAValue, '    ') === 0) {
                if (strpos($trimmedCellAValue, '(Nuit)') !== false) {
                    // Ligne des heures de nuit - fond violet
                    $this->applyHoursCellColoring($sheet, $row, $totalColumns, 'E6CCFF');
                } else {
                    // Ligne des heures de jour - fond vert
                    $this->applyHoursCellColoring($sheet, $row, $totalColumns, 'CCFFCC');
                }
            }
        }
    }

    /**
     * Applique la couleur de fond aux cellules contenant des valeurs numériques
     */
    private function applyHoursCellColoring(Worksheet $sheet, int $row, int $totalColumns, string $color): void
    {
        for ($col = 3; $col <= $totalColumns - 1; $col++) { // Exclure la colonne Total
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $cellValue = $sheet->getCell("{$columnLetter}{$row}")->getValue();
            
            if (is_numeric($cellValue)) {
                $sheet->getStyle("{$columnLetter}{$row}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB($color);
            }
        }
    }

    /**
     * Applique le fond rouge aux cellules contenant "abs"
     */
    public function applyAbsenceColoring(Worksheet $sheet, array $workerRows, int $totalColumns): void
    {
        foreach ($workerRows as $row) {
            for ($col = 3; $col <= $totalColumns - 1; $col++) {
                $columnLetter = Coordinate::stringFromColumnIndex($col);
                $cellValue = $sheet->getCell("{$columnLetter}{$row}")->getValue();
                
                if ($cellValue === 'abs') {
                    $sheet->getStyle("{$columnLetter}{$row}")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('FFCCCC'); // Rouge
                }
            }
        }
    }

    /**
     * Supprime les bordures et le fond des lignes vides
     */
    public function removeEmptyRowsStyling(Worksheet $sheet, int $totalRows, string $highestColumn): void
    {
        for ($row = 1; $row <= $totalRows; $row++) {
            $cellA = trim($sheet->getCell("A{$row}")->getValue() ?? '');
            $cellB = trim($sheet->getCell("B{$row}")->getValue() ?? '');

            // Si les colonnes A et B sont vides, considérer la ligne comme vide
            if ($cellA === '' && $cellB === '') {
                $range = "A{$row}:{$highestColumn}{$row}";

                // Supprimer toutes les bordures
                $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);

                // Supprimer le fond
                $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_NONE);
            }
        }
    }

    /**
     * Met en gras une ligne spécifique (pour les totaux)
     */
    public function applyBoldToRow(Worksheet $sheet, int $row, string $highestColumn): void
    {
        $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->getFont()->setBold(true);
    }

    /**
     * Applique une couleur de fond à une colonne spécifique
     */
    public function applyColumnColoring(Worksheet $sheet, string $column, int $startRow, int $endRow, string $color): void
    {
        $range = "{$column}{$startRow}:{$column}{$endRow}";
        $sheet->getStyle($range)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($color);
    }

    /**
     * Applique une rotation de texte (vertical de bas en haut) avec centrage
     */
    public function applyVerticalText(Worksheet $sheet, string $cell): void
    {
        $sheet->getStyle($cell)
            ->getAlignment()
            ->setTextRotation(90) // 90 degrés = vertical de bas en haut
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    /**
     * Applique le wrap text et centrage pour texte multi-lignes
     */
    public function applyMultiLineText(Worksheet $sheet, string $cell): void
    {
        $sheet->getStyle($cell)
            ->getAlignment()
            ->setWrapText(true)
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    /**
     * Applique la coloration conditionnelle des tarifs selon jour/nuit
     */
    public function applyRateColoring(Worksheet $sheet, int $startRow, int $endRow, array $projectCategories): void
    {
        foreach ($projectCategories as $row => $category) {
            if ($category === 'day') {
                // Vert pour les tarifs des projets jour
                $sheet->getStyle("B{$row}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('CCFFCC'); // Vert comme les heures jour
            } elseif ($category === 'night') {
                // Violet pour les tarifs des projets nuit
                $sheet->getStyle("B{$row}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('E6CCFF'); // Violet comme les heures nuit
            }
        }
    }

    /**
     * Assure que toutes les cellules dans une plage ont une valeur (même vide)
     * Nécessaire pour que les bordures s'affichent correctement
     */
    public function ensureEmptyCells(Worksheet $sheet, int $startRow, int $endRow, int $totalColumns): void
    {
        for ($r = $startRow; $r <= $endRow; $r++) {
            for ($c = 1; $c <= $totalColumns; $c++) {
                $cellRef = Coordinate::stringFromColumnIndex($c) . $r;
                if (!$sheet->getCell($cellRef)->getValue()) {
                    $sheet->setCellValue($cellRef, '');
                }
            }
        }
    }

    /**
     * Définit la hauteur des lignes
     */
    public function setRowHeights(Worksheet $sheet, int $startRow, int $endRow, float $height = 20): void
    {
        for ($i = $startRow; $i <= $endRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight($height);
        }
    }

    /**
     * Applique le centrage à une plage de cellules (pour headers)
     */
    public function applyCenteredAlignment(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    /**
     * Applique les styles aux lignes de total des workers
     */
    public function applyWorkerTotalStyles(Worksheet $sheet, array $workerTotalRows, string $highestColumn): void
    {
        foreach ($workerTotalRows as $row) {
            // Mettre en gras la ligne de total
            $sheet->getStyle("A{$row}:{$highestColumn}{$row}")
                ->getFont()
                ->setBold(true);
            
            // Appliquer le style uniquement à la cellule de total (dernière colonne)
            $sheet->getStyle("{$highestColumn}{$row}")
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('A9EDED'); // Bleu clair
                
            // Ajouter une bordure noire à la cellule de total
            $sheet->getStyle("{$highestColumn}{$row}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->getColor()
                ->setRGB('000000'); // Noir
        }
    }
}