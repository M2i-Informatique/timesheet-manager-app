<?php

namespace App\Livewire\TimeSheet;

use Livewire\Component;

class Spreadsheet extends Component
{
    public $data = [];

    public function mount()
    {
        // Données de test pour Handsontable
        $this->data = [
            ['Nom', 'Jour 1', 'Jour 2', 'Jour 3'],
            ['Travailleur 1', 8, 7.5, 8],
            ['Travailleur 2', 7, 8, 6.5],
            ['Travailleur 3', 6, 8, 8],
        ];
    }

    public function render()
    {
        return view('livewire.time-sheet.spreadsheet');
    }
}
