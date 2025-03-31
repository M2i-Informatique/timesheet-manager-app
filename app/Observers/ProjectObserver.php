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
        $this->assignCategory($project);
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
        if (is_null($project->distance)) {
            return;
        }
    
        $zone = \App\Models\Zone::where('min_km', '<=', $project->distance)
            ->where(function ($query) use ($project) {
                $query->whereNull('max_km')
                    ->orWhere('max_km', '>=', $project->distance);
            })
            ->first();
    
        if ($zone) {
            $project->zone()->associate($zone);
        }
    }

    /**
     * Assign category to project based on code prefix
     * - If code starts with '1', category = 'mh'
     * - If code starts with '2', category = 'go'
     * - Otherwise leave as is
     *
     * @param Project $project
     */
    protected function assignCategory(Project $project): void
    {
        // Convertir le code en string pour s'assurer de pouvoir utiliser la fonction startsWith
        $codeStr = (string) $project->code;

        if (str_starts_with($codeStr, '1')) {
            $project->category = 'mh';
        } elseif (str_starts_with($codeStr, '2')) {
            $project->category = 'go';
        } else {
            $project->category = 'other';
        }
    }
}
