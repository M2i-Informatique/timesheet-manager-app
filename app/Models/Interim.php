<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Interim extends Model
{
    protected $fillable = ['agency', 'hourly_rate', 'status'];

    protected $casts = ['hourly_rate' => 'float'];

    /**
     * Get all the projects for the interim.
     *
     * @return MorphToMany
     */
    public function projects(): MorphToMany
    {
        return $this->morphToMany(Project::class, 'projectable');
    }

    /**
     * Get all the timesheet's interims.
     * 
     * @return MorphMany
     */
    public function timesheets(): MorphMany
    {
        return $this->morphMany(TimeSheetable::class, 'timesheetable');
    }
}
