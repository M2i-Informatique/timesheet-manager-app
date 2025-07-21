<?php

namespace App\CQRS\Handlers;

use App\CQRS\QueryHandlerInterface;
use App\CQRS\QueryInterface;
use App\CQRS\Queries\GetTrackingDataQuery;
use App\Services\Tracking\TrackingServiceInterface;
use App\Services\Cache\CacheService;
use InvalidArgumentException;

/**
 * Handler pour récupérer les données de pointage avec cache
 */
class GetTrackingDataHandler implements QueryHandlerInterface
{
    private TrackingServiceInterface $trackingService;
    private CacheService $cacheService;

    public function __construct(TrackingServiceInterface $trackingService, CacheService $cacheService)
    {
        $this->trackingService = $trackingService;
        $this->cacheService = $cacheService;
    }

    public function handle(QueryInterface $query): mixed
    {
        if (!$query instanceof GetTrackingDataQuery) {
            throw new InvalidArgumentException('Expected GetTrackingDataQuery');
        }

        $cacheKey = $this->cacheService->generateTrackingCacheKey(
            $query->getProjectId(),
            $query->getMonth(),
            $query->getYear(),
            $query->getCategory()
        );

        return $this->cacheService->remember($cacheKey, function () use ($query) {
            return $this->trackingService->getTrackingData($query->getParameters());
        });
    }
}