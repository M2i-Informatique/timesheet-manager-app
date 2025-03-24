<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class NonWorkingDay extends Model
{
    protected $fillable = [
        'date',
        'type'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function getNonWorkingDays(): Collection
    {
        return $this->all();
    }
}
