<?php

namespace App\Models;

use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TimeSheetable extends Model
{
    use UserTracking, LogsActivity;
    protected $fillable = ['date', 'hours', 'category', 'project_id', 'timesheetable_id', 'timesheetable_type', 'created_by'];

    protected $casts = ['date' => 'date', 'hours' => 'float'];

    /**
     * Get the options for the activitylog
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['date', 'hours', 'category', 'project_id', 'timesheetable_id', 'timesheetable_type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function(string $eventName) {
                if ($eventName === 'created') {
                    return 'Un nouveau pointage a été créé';
                }
                if ($eventName === 'updated') {
                    return 'Un pointage a été modifié';
                }
                if ($eventName === 'deleted') {
                    return 'Un pointage a été supprimé';
                }
                return "Un pointage a été {$eventName}";
            });
    }

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
