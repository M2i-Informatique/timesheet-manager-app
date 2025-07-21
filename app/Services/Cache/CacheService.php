<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Service centralisé pour la gestion du cache
 */
class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 heure
    private const TRACKING_DATA_TTL = 1800; // 30 minutes pour les données de pointage
    private const COSTS_DATA_TTL = 7200; // 2 heures pour les coûts
    
    /**
     * Obtenir les données de pointage depuis le cache ou les calculer
     */
    public function getTrackingData(string $key, callable $callback): array
    {
        return Cache::remember($key, self::TRACKING_DATA_TTL, function () use ($callback) {
            Log::info("Cache miss for tracking data: generating new data");
            $startTime = microtime(true);
            
            $data = $callback();
            
            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000; // en ms
            
            Log::info("Tracking data generated in {$executionTime}ms");
            
            return $data;
        });
    }
    
    /**
     * Obtenir les coûts depuis le cache ou les calculer
     */
    public function getCostsData(string $key, callable $callback): array
    {
        return Cache::remember($key, self::COSTS_DATA_TTL, function () use ($callback) {
            Log::info("Cache miss for costs data: generating new data");
            $startTime = microtime(true);
            
            $data = $callback();
            
            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000; // en ms
            
            Log::info("Costs data generated in {$executionTime}ms");
            
            return $data;
        });
    }
    
    /**
     * Générer une clé de cache pour les données de pointage
     */
    public function generateTrackingCacheKey(int $projectId, int $month, int $year, string $category): string
    {
        return "tracking_data_{$projectId}_{$month}_{$year}_{$category}";
    }
    
    /**
     * Générer une clé de cache pour les coûts
     */
    public function generateCostsCacheKey(int $projectId, ?string $startDate, ?string $endDate, bool $detailed): string
    {
        $dateRange = $startDate && $endDate ? "_{$startDate}_{$endDate}" : "_all";
        $detailLevel = $detailed ? "_detailed" : "_summary";
        
        return "costs_data_{$projectId}{$dateRange}{$detailLevel}";
    }
    
    /**
     * Invalider le cache pour un projet spécifique
     */
    public function invalidateProjectCache(int $projectId): void
    {
        $patterns = [
            "tracking_data_{$projectId}_*",
            "costs_data_{$projectId}_*"
        ];
        
        foreach ($patterns as $pattern) {
            $this->forgetByPattern($pattern);
        }
        
        Log::info("Cache invalidated for project {$projectId}");
    }
    
    /**
     * Invalider le cache pour un mois spécifique
     */
    public function invalidateMonthCache(int $projectId, int $month, int $year): void
    {
        $patterns = [
            "tracking_data_{$projectId}_{$month}_{$year}_*",
        ];
        
        foreach ($patterns as $pattern) {
            $this->forgetByPattern($pattern);
        }
        
        Log::info("Cache invalidated for project {$projectId}, month {$month}/{$year}");
    }
    
    /**
     * Supprimer les clés de cache par pattern (utilise Redis si disponible)
     */
    private function forgetByPattern(string $pattern): void
    {
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            // Si Redis est disponible, utiliser le pattern matching
            $keys = Cache::getStore()->getRedis()->keys($pattern);
            if (!empty($keys)) {
                Cache::getStore()->getRedis()->del($keys);
            }
        } else {
            // Fallback : supprimer manuellement les clés connues
            // Cette approche est moins efficace mais fonctionne avec tous les drivers
            $this->forgetKnownKeys($pattern);
        }
    }
    
    /**
     * Supprimer les clés connues (fallback pour drivers non-Redis)
     */
    private function forgetKnownKeys(string $pattern): void
    {
        // Générer les clés possibles basées sur le pattern
        // Cette méthode pourrait être améliorée en stockant les clés actives
        
        if (str_contains($pattern, 'tracking_data_')) {
            // Invalider les données de pointage pour tous les mois/catégories
            $months = range(1, 12);
            $years = range(2020, 2030);
            $categories = ['day', 'night'];
            
            foreach ($months as $month) {
                foreach ($years as $year) {
                    foreach ($categories as $category) {
                        $key = str_replace('*', '', $pattern) . $month . '_' . $year . '_' . $category;
                        Cache::forget($key);
                    }
                }
            }
        }
    }
    
    /**
     * Obtenir les statistiques du cache
     */
    public function getCacheStats(): array
    {
        $store = Cache::getStore();
        
        return [
            'driver' => get_class($store),
            'default_ttl' => self::DEFAULT_TTL,
            'tracking_ttl' => self::TRACKING_DATA_TTL,
            'costs_ttl' => self::COSTS_DATA_TTL,
            'redis_available' => $store instanceof \Illuminate\Cache\RedisStore,
        ];
    }
    
    /**
     * Méthode générique remember pour compatibilité avec le handler
     */
    public function remember(string $key, callable $callback, int $ttl = null): mixed
    {
        return Cache::remember($key, $ttl ?? self::DEFAULT_TTL, $callback);
    }
    
    /**
     * Vider tout le cache de l'application
     */
    public function flushAll(): void
    {
        Cache::flush();
        Log::info("All cache cleared");
    }
}