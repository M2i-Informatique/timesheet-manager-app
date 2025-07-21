<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Worker;
use App\Models\Interim;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository implements ProjectRepositoryInterface
{
    /**
     * Récupérer tous les projets actifs.
     */
    public function findActiveProjects(): Collection
    {
        return Project::where('status', 'active')
            ->orderBy('code')
            ->get();
    }

    /**
     * Récupérer un projet avec ses relations.
     */
    public function findWithRelations(int $projectId): Project
    {
        return Project::with(['workers', 'interims', 'zone'])
            ->findOrFail($projectId);
    }

    /**
     * Récupérer les workers actifs assignés à un projet.
     */
    public function findActiveWorkersForProject(int $projectId): Collection
    {
        $project = Project::findOrFail($projectId);
        return $project->workers()
            ->where('status', 'active')
            ->get();
    }

    /**
     * Récupérer les interims actifs assignés à un projet.
     */
    public function findActiveInterimsForProject(int $projectId): Collection
    {
        $project = Project::findOrFail($projectId);
        return $project->interims()
            ->where('status', 'active')
            ->get();
    }

    /**
     * Récupérer les workers disponibles (non assignés au projet).
     */
    public function findAvailableWorkers(int $projectId): Collection
    {
        return Worker::where('status', 'active')
            ->whereDoesntHave('projects', function ($query) use ($projectId) {
                $query->where('projects.id', $projectId);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * Récupérer les interims disponibles (non assignés au projet).
     */
    public function findAvailableInterims(int $projectId): Collection
    {
        return Interim::where('status', 'active')
            ->whereDoesntHave('projects', function ($query) use ($projectId) {
                $query->where('projects.id', $projectId);
            })
            ->get();
    }

    /**
     * Assigner un worker à un projet.
     */
    public function assignWorkerToProject(int $projectId, int $workerId): void
    {
        $project = Project::findOrFail($projectId);
        $project->workers()->syncWithoutDetaching([$workerId]);
    }

    /**
     * Assigner un interim à un projet.
     */
    public function assignInterimToProject(int $projectId, int $interimId): void
    {
        $project = Project::findOrFail($projectId);
        $project->interims()->syncWithoutDetaching([$interimId]);
    }

    /**
     * Détacher un worker d'un projet.
     */
    public function detachWorkerFromProject(int $projectId, int $workerId): void
    {
        $project = Project::findOrFail($projectId);
        $project->workers()->detach($workerId);
    }

    /**
     * Détacher un interim d'un projet.
     */
    public function detachInterimFromProject(int $projectId, int $interimId): void
    {
        $project = Project::findOrFail($projectId);
        $project->interims()->detach($interimId);
    }
}