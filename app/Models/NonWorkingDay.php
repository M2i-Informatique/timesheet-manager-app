<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class NonWorkingDay extends Model
{
    protected $fillable = [
        'date',
        'type',
        'comment'
    ];

    // Types disponibles pour le select
    const TYPES = [
        'Férié' => 'Férié',
        'RTT Imposé' => 'RTT Imposé', 
        'Fermeture' => 'Fermeture'
    ];

    // Codes d'affichage pour le tracking
    const DISPLAY_CODES = [
        'Férié' => 'FER',
        'RTT Imposé' => 'RTT',
        'Fermeture' => 'FRM'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function getNonWorkingDays(): Collection
    {
        return $this->all();
    }

    /**
     * Retourne le code d'affichage pour le tracking
     */
    public function getDisplayCode(): string
    {
        return self::DISPLAY_CODES[$this->type] ?? 'FRM';
    }
}
