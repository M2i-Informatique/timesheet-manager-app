<?php

namespace App\Services\Costs;

use App\Models\Project;
use App\Models\Worker;

interface CostCalculatorInterface
{
    /**
     * Calculer le coût horaire jour pour un worker sur un projet.
     */
    public function calculateHourlyDayCost(Worker $worker, Project $project): float;

    /**
     * Calculer le coût horaire nuit pour un worker sur un projet.
     */
    public function calculateHourlyNightCost(Worker $worker, Project $project): float;

    /**
     * Calculer le coût total pour un worker sur un projet sur une période.
     */
    public function calculateTotalCostForOneWorker(Worker $worker, Project $project, string $startDate, string $endDate): float;

    /**
     * Calculer le coût total pour tous les workers d'un projet.
     */
    public function calculateTotalCostForProject(Project $project, ?string $startDate, ?string $endDate): array;

    /**
     * Calculer le détail des coûts pour un projet.
     */
    public function calculateDetailedProjectCostForProject(Project $project, ?string $startDate, ?string $endDate): array;

    /**
     * Vérifier si un worker est de catégorie ETAM.
     */
    public function isEtam(Worker $worker): bool;
}