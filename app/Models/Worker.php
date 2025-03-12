<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Worker extends Model
{
    protected $fillable = ['first_name', 'last_name', 'category', 'contract_hours', 'monthly_salary', 'status'];

    protected $casts = ['contract_hours' => 'integer', 'monthly_salary' => 'float'];

    /**
     * Get the worker hourly rate attribute.
     *
     * @return float|null
     */
    public function getHourlyRateAttribute(): ?float
    {
        if ($this->contract_hours && $this->monthly_salary) {
            $monthlyHours = $this->contract_hours * (52 / 12);
            return $this->monthly_salary / $monthlyHours;
        }
        return null;
    }

    /**
     * Get the worker hourly rate charged attribute.
     *
     * @return float|null
     */
    public function getHourlyRateChargedAttribute(): ?float
    {
        $hourlyRate = $this->hourly_rate;
        if (! $hourlyRate) {
            return null;
        }

        $chargePercentage = (float) Setting::getValue('rate_charged', 70);
        $factor = 1 + ($chargePercentage / 100);

        return $hourlyRate * $factor;
    }

    /**
     * Get all the projects for the worker.
     *
     * @return MorphToMany
     */
    public function projects(): MorphToMany
    {
        return $this->morphToMany(Project::class, 'projectable');
    }

    /**
     * Get all the timesheet's workers.
     * 
     * @return MorphMany
     */
    public function timesheets(): MorphMany
    {
        return $this->morphMany(TimeSheetable::class, 'timesheetable');
    }
}