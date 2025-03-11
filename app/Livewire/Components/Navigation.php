<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Navigation extends Component
{
    public $links = [
        [
            'name' => 'Accueil',
            'route' => 'home'
        ],
        [
            'name' => 'Pointage',
            'route' => 'tracking'
        ]
    ];

    public function render()
    {
        return view('livewire.components.navigation');
    }
}
