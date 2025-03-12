<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Project extends Model
{
    protected $fillable = ['code', 'category', 'name', 'address', 'city', 'distance', 'status', 'zone_id'];

    protected $casts = ['code' => 'integer', 'distance' => 'float'];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * Get the project zone name attribute.
     *
     * @return string
     */
    public function getZoneNameAttribute()
    {
        return $this->zone->name;
    }

    /**
     * Get the users (drivers) assigned to this project.
     */
    public function drivers()
    {
        return $this->belongsToMany(User::class)
        ->withTimestamps();
    }

    /**
     * Get all of the workers that are assigned to this project.
     *
     * @return MorphToMany
     */
    public function workers(): MorphToMany
    {
        return $this->morphedByMany(Worker::class, 'projectable');
    }

    /**
     * Get all of the interims that are assigned to this project.
     *
     * @return MorphToMany
     */
    public function interims(): MorphToMany
    {
        return $this->morphedByMany(Interim::class, 'projectable');
    }

    /**
     * Get all the timesheets for the project.
     *
     * @return HasMany
     */
    public function timesheets(): HasMany
    {
        return $this->hasMany(TimeSheetable::class);
    }
}
