<?php

namespace App\Exports;

use App\Models\Worker;
use App\Models\TimeSheetable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class WorkerYearlyExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected $year;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function headings(): array
    {
        $title = "RECAPITULATIF HEURES SALARIES {$this->year}";

        // Ligne 1 : Titre
        // Ligne 2 : Mois (fusionnés deux par deux)
        // Ligne 3 : MH / GO

        $monthRow = ['Nom Prénom'];
        $subHeaderRow = [''];

        $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        foreach ($months as $month) {
            $monthRow[] = $month;
            $monthRow[] = ''; // Colonne fusionnée
            $subHeaderRow[] = 'MH';
            $subHeaderRow[] = 'GO';
        }

        // Colonnes Totaux
        $monthRow[] = 'TOTAL ANNUEL';
        $monthRow[] = ''; // MH
        $monthRow[] = ''; // GO
        
        $subHeaderRow[] = 'TOTAL MH';
        $subHeaderRow[] = 'TOTAL GO';
        $subHeaderRow[] = 'TOTAL GÉNÉRAL';

        return [
            [$title],
            $monthRow,
            $subHeaderRow
        ];
    }

    public function array(): array
    {
        // Récupérer tous les workers actifs
        $workers = Worker::where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Récupérer tous les pointages de l'année avec le projet
        $allTimeSheets = TimeSheetable::with('project')
            ->where('timesheetable_type', Worker::class)
            ->whereYear('date', $this->year)
            ->get();

        $data = [];

        foreach ($workers as $worker) {
            $row = [
                $worker->last_name . ' ' . $worker->first_name
            ];

            $yearlyTotalMh = 0;
            $yearlyTotalGo = 0;
            
            // Pour chaque mois
            for ($month = 1; $month <= 12; $month++) {
                // Filtrer les timesheets du mois pour ce worker
                $monthSheets = $allTimeSheets->filter(function ($ts) use ($worker, $month) {
                    return $ts->timesheetable_id == $worker->id && $ts->date->month == $month;
                });

                // Calcul MH
                $mhHours = $monthSheets->filter(function($ts) {
                    return $ts->project && $ts->project->category === 'mh';
                })->sum('hours');

                // Calcul GO
                $goHours = $monthSheets->filter(function($ts) {
                    return $ts->project && $ts->project->category === 'go';
                })->sum('hours');

                $row[] = $mhHours > 0 ? (float)$mhHours : '';
                $row[] = $goHours > 0 ? (float)$goHours : '';

                $yearlyTotalMh += $mhHours;
                $yearlyTotalGo += $goHours;
            }

            // Totaux Finaux
            $row[] = $yearlyTotalMh > 0 ? (float)$yearlyTotalMh : '';
            $row[] = $yearlyTotalGo > 0 ? (float)$yearlyTotalGo : '';
            $row[] = ($yearlyTotalMh + $yearlyTotalGo) > 0 ? (float)($yearlyTotalMh + $yearlyTotalGo) : '';

            $data[] = $row;
        }

        // Ligne de total général tout en bas
        $totalRow = ['TOTAL GÉNÉRAL'];
        
        // On a 24 colonnes de mois (2*12) + 3 colonnes de totaux = 27 colonnes de données + 1 colonne Nom = 28 colonnes au total
        // Les données commencent à l'index 1 du tableau $row (index 0 est le nom)
        
        $numDataColumns = 24 + 3; 

        for ($colIndex = 1; $colIndex <= $numDataColumns; $colIndex++) {
            $colSum = 0;
            foreach ($data as $d) {
                if (isset($d[$colIndex]) && is_numeric($d[$colIndex])) {
                    $colSum += $d[$colIndex];
                }
            }
            $totalRow[] = $colSum > 0 ? $colSum : '';
        }

        $data[] = $totalRow;

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestCol = $sheet->getHighestColumn();
                
                // Fusion des mois (Ligne 2)
                // A ne bouge pas.
                // Janvier : B2:C2, Février : D2:E2...
                // Index : 0=A, 1=B, 2=C...
                // Janvier start col index 1 (B)
                
                $colIndex = 2; // Colonne B (index 1-based dans stringFromColumnIndex? Non 1=A)
                // PhpSpreadsheet: 1=A, 2=B
                
                for ($m = 0; $m < 12; $m++) {
                    $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex); // B
                    $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1); // C
                    
                    $sheet->mergeCells("{$startCol}2:{$endCol}2");
                    $sheet->getStyle("{$startCol}2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    $colIndex += 2;
                }
                
                // Fusion "TOTAL ANNUEL" sur les 3 dernières colonnes ?
                // Non, on a mis 'TOTAL ANNUEL', '', '' dans headings pour B, C, D de fin
                // Les colonnes de totaux sont : Total MH, Total GO, Total Général
                // Disons qu'on fusionne "TOTAL ANNUEL" sur les 3 colonnes au-dessus
                $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 2);
                $sheet->mergeCells("{$startCol}2:{$endCol}2");
                $sheet->getStyle("{$startCol}2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();

        // 1. TITRE (Ligne 1)
        $sheet->mergeCells("A1:{$highestCol}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // 2. EN-TETES (Lignes 2 et 3)
        $sheet->getStyle("A2:{$highestCol}3")->getFont()->setBold(true);
        $sheet->getStyle("A2:{$highestCol}3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A2:{$highestCol}3")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        // Fond gris pour mois
        $sheet->getStyle("A2:{$highestCol}2")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9D9D9');

        // Fond gris plus clair pour MH/GO
        $sheet->getStyle("A3:{$highestCol}3")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F2F2F2');

        // 3. BORDURES
        $sheet->getStyle("A2:{$highestCol}{$highestRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
        // 4. TOTAUX GENERAUX (Dernière ligne)
        $sheet->getStyle("A{$highestRow}:{$highestCol}{$highestRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$highestRow}:{$highestCol}{$highestRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4'); // Bleu
        $sheet->getStyle("A{$highestRow}:{$highestCol}{$highestRow}")->getFont()->getColor()->setRGB('FFFFFF');

        // 5. TOTAUX ANNUELS (Dernières colonnes)
        // Calculer les lettres des 3 dernières colonnes
        // On a 1 (Nom) + 24 (Mois) = 25 colonnes de données avant les totaux
        // Donc colonnes 26, 27, 28 (Z, AA, AB)
        
        $totalStartColIndex = 26; // Z
        $totalEndColIndex = 28; // AB
        
        $startColChar = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalStartColIndex);
        $endColChar = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalEndColIndex);

        $sheet->getStyle("{$startColChar}2:{$endColChar}{$highestRow}")->getFont()->setBold(true);
        $sheet->getStyle("{$startColChar}3:{$endColChar}{$highestRow}")->getFill() // A partir de la ligne 3 (données)
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FCE4D6'); // Orange clair

        return [];
    }
}
