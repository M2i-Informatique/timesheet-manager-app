<?php

namespace App\Models;

use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TimeSheetable extends Model
{
    use UserTracking;
    protected $fillable = ['date', 'hours', 'category', 'project_id', 'timesheetable_id', 'timesheetable_type', 'created_by'];

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

    /**
     * Get the user who created this timesheet entry
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
