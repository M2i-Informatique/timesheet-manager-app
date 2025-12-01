<?php

namespace App\Services\Costs;

use App\Models\Project;
use Illuminate\Support\Facades\Log;

class ProjectCostsService extends CostsCalculator
{
    /**
     * Récupère les coûts des projets avec des filtres optionnels.
     *
     * Filtres disponibles :
     *   - id: ID du projet (facultatif)
     *   - category: Catégorie du projet (facultatif)
     *   - startDate: Date de début (facultatif)
     *   - endDate: Date de fin (facultatif)
     *
     * @param string|null $id
     * @param string|null $category
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getProjectCosts(?string $id, ?string $category, ?string $startDate, ?string $endDate): array
    {
        Log::info("getProjectCosts - Start with filters: ID={$id}, Category={$category}, StartDate={$startDate}, EndDate={$endDate}");

        $query = Project::query();

        if ($id) {
            $query->where('id', $id);
        }

        if ($category) {
            $query->where('category', $category);
        }

        // Ajout du tri par code
        $query->orderBy('code', 'asc');

        $projects = $query->get();
        $results  = [];

        Log::info("Found " . $projects->count() . " projects matching filters");

        foreach ($projects as $project) {
            Log::info("Processing Project ID: {$project->id}, Name: {$project->name}");

            $detailedCost = $this->calculateDetailedProjectCostForProject($project, $startDate, $endDate);
            $results[] = $detailedCost['data'];

            Log::info("Project total hours: {$detailedCost['data']['total_hours']}, Project total cost: {$detailedCost['data']['total_cost']}");
        }

        Log::info("getProjectCosts - Returning " . count($results) . " projects");

        Log::info("Results Summary:");
        foreach ($results as $index => $project) {
            Log::info("  Project {$index}: ID={$project['id']}, Name={$project['attributes']['name']}, Hours={$project['total_hours']}, Cost={$project['total_cost']}");
        }

        return $results;
    }
}
