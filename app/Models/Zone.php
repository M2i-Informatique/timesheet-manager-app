<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['name', 'min_km', 'max_km', 'rate'];

    protected $casts = ['min_km' => 'float', 'max_km' => 'float', 'rate' => 'float'];

    /**
     * Get all the projects for the zone.
     *
     * @return HasMany
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
