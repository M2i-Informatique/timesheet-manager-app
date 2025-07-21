<?php

namespace App\Exports;

use App\Models\WorkerLeave;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class LeaveColorLegendExport implements FromArray, WithStyles, WithTitle
{
    /**
     * Retourne les données pour la légende des congés
     */
    public function array(): array
    {
        $data = [];
        
        // Titre principal
        $data[] = ['LÉGENDE DES CODES COULEURS CONGÉS', '', ''];
        $data[] = ['', '', '']; // Ligne vide
        
        // En-têtes des colonnes
        $data[] = ['TYPE DE CONGÉ', 'CODE', 'COULEUR'];
        
        // Récupérer tous les types de congés avec leurs informations
        $leaveTypes = WorkerLeave::getTypes();
        $leaveCodes = WorkerLeave::getTypeCodes();
        $leaveColors = WorkerLeave::getTypeColors();
        
        foreach ($leaveTypes as $key => $type) {
            $data[] = [
                $type,
                $leaveCodes[$key] ?? '',
                $leaveCodes[$key] ?? ''  // On utilisera le code pour afficher la couleur
            ];
        }
        
        // Ajouter quelques lignes vides
        $data[] = ['', '', ''];
        $data[] = ['', '', ''];
        
        // Ajouter des informations supplémentaires
        $data[] = ['INFORMATIONS COMPLÉMENTAIRES', '', ''];
        $data[] = ['• Ces codes apparaissent dans les exports Excel', '', ''];
        $data[] = ['• Les couleurs correspondent à celles du système de tracking', '', ''];
        $data[] = ['• Tous les congés sont validés par défaut lors de la saisie', '', ''];
        
        return $data;
    }

    /**
     * Applique les styles à la feuille
     */
    public function styles(Worksheet $sheet)
    {
        // Définir la largeur des colonnes
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(15);
        
        // Fusionner et styler le titre principal
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6F3FF'], // Bleu très clair
            ],
        ]);
        
        // Styler les en-têtes de colonnes
        $sheet->getStyle('A3:C3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'], // Bleu foncé
            ],
        ]);
        
        // Appliquer les bordures à toute la zone de données
        $sheet->getStyle('A1:C11')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        
        return [];
    }

    /**
     * Configure les événements pour appliquer les couleurs spécifiques
     */
    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function(\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Récupérer les couleurs des congés
                $leaveColors = WorkerLeave::getTypeColors();
                $leaveCodes = WorkerLeave::getTypeCodes();
                
                // Appliquer les couleurs aux cellules de la colonne C (codes)
                $row = 4; // Commencer après les en-têtes
                foreach ($leaveCodes as $key => $code) {
                    $colorHex = ltrim($leaveColors[$key] ?? 'FFFFFF', '#');
                    
                    // Appliquer la couleur de fond
                    $sheet->getStyle("C{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $colorHex],
                        ],
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF'], // Texte blanc pour contraste
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    
                    $row++;
                }
                
                // Styler la section informations complémentaires
                $sheet->mergeCells('A13:C13');
                $sheet->getStyle('A13')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => '4472C4'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
                
                // Styler les lignes d'informations
                $sheet->getStyle('A14:A16')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
            },
        ];
    }

    /**
     * Définit le nom de la feuille
     */
    public function title(): string
    {
        return 'Légende Congés';
    }
}