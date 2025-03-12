<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TimeSheetable extends Model
{
    protected $fillable = ['date', 'hours', 'category', 'project_id', 'timesheetable_id', 'timesheetable_type'];

    protected $casts = ['date' => 'date', 'hours' => 'float'];

    /**
     * Get the project for the timesheetable
     * 
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the parent timesheetable model (worker, interim, etc.)
     * 
     * @return MorphTo
     */
    public function timesheetable(): MorphTo
    {
        return $this->morphTo();
    }
}
