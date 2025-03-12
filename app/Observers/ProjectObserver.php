<?php

namespace App\Observers;

use App\Models\Project;

class ProjectObserver
{
    /**
     * Handle the Project "saving" event.
     */
    public function saving(Project $project): void
    {
        $this->assignZone($project);
    }


    /**
     * Assign the zone to the project based on the distance.
     *
     * @param Project $project
     *
     * @throws \Exception
     */
    protected function assignZone(Project $project): void
    {
        $zone = \App\Models\Zone::where('min_km', '<=', $project->distance)
            ->where(function ($query) use ($project) {
                $query->whereNull('max_km')
                    ->orWhere('max_km', '>=', $project->distance);
            })
            ->first();

        if ($zone) {
            $project->zone()->associate($zone);
        } else {
            throw new \Exception('No zone found for the given distance');
        }
    }
}
