<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Worker;
use App\Models\Interim;
use Illuminate\Database\Eloquent\Collection;

interface ProjectRepositoryInterface
{
    /**
     * Récupérer tous les projets actifs.
     */
    public function findActiveProjects(): Collection;

    /**
     * Récupérer un projet avec ses relations.
     */
    public function findWithRelations(int $projectId): Project;

    /**
     * Récupérer les workers actifs assignés à un projet.
     */
    public function findActiveWorkersForProject(int $projectId): Collection;

    /**
     * Récupérer les interims actifs assignés à un projet.
     */
    public function findActiveInterimsForProject(int $projectId): Collection;

    /**
     * Récupérer les workers disponibles (non assignés au projet).
     */
    public function findAvailableWorkers(int $projectId): Collection;

    /**
     * Récupérer les interims disponibles (non assignés au projet).
     */
    public function findAvailableInterims(int $projectId): Collection;

    /**
     * Assigner un worker à un projet.
     */
    public function assignWorkerToProject(int $projectId, int $workerId): void;

    /**
     * Assigner un interim à un projet.
     */
    public function assignInterimToProject(int $projectId, int $interimId): void;

    /**
     * Détacher un worker d'un projet.
     */
    public function detachWorkerFromProject(int $projectId, int $workerId): void;

    /**
     * Détacher un interim d'un projet.
     */
    public function detachInterimFromProject(int $projectId, int $interimId): void;
}