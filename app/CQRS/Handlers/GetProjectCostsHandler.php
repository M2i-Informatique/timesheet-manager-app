<?php

namespace App\CQRS\Handlers;

use App\CQRS\QueryHandlerInterface;
use App\CQRS\QueryInterface;
use App\CQRS\Queries\GetProjectCostsQuery;
use App\Services\Costs\CostCalculatorInterface;
use App\Services\Cache\CacheService;
use App\Models\Project;
use InvalidArgumentException;

/**
 * Handler pour récupérer les coûts d'un projet avec cache
 */
class GetProjectCostsHandler implements QueryHandlerInterface
{
    private CostCalculatorInterface $costCalculator;
    private CacheService $cacheService;

    public function __construct(CostCalculatorInterface $costCalculator, CacheService $cacheService)
    {
        $this->costCalculator = $costCalculator;
        $this->cacheService = $cacheService;
    }

    public function handle(QueryInterface $query): mixed
    {
        if (!$query instanceof GetProjectCostsQuery) {
            throw new InvalidArgumentException('Expected GetProjectCostsQuery');
        }

        $cacheKey = $this->cacheService->generateCostsCacheKey(
            $query->getProjectId(),
            $query->getStartDate(),
            $query->getEndDate(),
            $query->isDetailed()
        );

        return $this->cacheService->getCostsData($cacheKey, function () use ($query) {
            $project = Project::findOrFail($query->getProjectId());

            if ($query->isDetailed()) {
                return $this->costCalculator->calculateDetailedProjectCostForProject(
                    $project,
                    $query->getStartDate(),
                    $query->getEndDate()
                );
            }

            return $this->costCalculator->calculateTotalCostForProject(
                $project,
                $query->getStartDate(),
                $query->getEndDate()
            );
        });
    }
}