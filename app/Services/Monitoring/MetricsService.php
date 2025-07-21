<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\TimeSheetable;
use App\Models\Project;
use App\Models\Worker;
use Carbon\Carbon;

/**
 * Service pour collecter et exposer les métriques de l'application
 */
class MetricsService
{
    private const METRICS_CACHE_KEY = 'app_metrics';
    private const METRICS_TTL = 300; // 5 minutes

    /**
     * Collecter toutes les métriques système
     */
    public function collectMetrics(): array
    {
        return Cache::remember(self::METRICS_CACHE_KEY, self::METRICS_TTL, function () {
            $startTime = microtime(true);
            
            $metrics = [
                'timestamp' => now()->toISOString(),
                'database' => $this->getDatabaseMetrics(),
                'business' => $this->getBusinessMetrics(),
                'performance' => $this->getPerformanceMetrics(),
                'cache' => $this->getCacheMetrics(),
                'system' => $this->getSystemMetrics()
            ];
            
            $executionTime = (microtime(true) - $startTime) * 1000;
            $metrics['collection_time_ms'] = round($executionTime, 2);
            
            return $metrics;
        });
    }

    /**
     * Métriques base de données
     */
    private function getDatabaseMetrics(): array
    {
        try {
            return [
                'total_timesheets' => TimeSheetable::count(),
                'total_projects' => Project::count(),
                'active_projects' => Project::where('status', 'active')->count(),
                'total_workers' => Worker::count(),
                'active_workers' => Worker::where('status', 'active')->count(),
                'connection_status' => 'connected'
            ];
        } catch (\Exception $e) {
            Log::error('Database metrics collection failed', ['error' => $e->getMessage()]);
            return [
                'connection_status' => 'failed',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Métriques métier
     */
    private function getBusinessMetrics(): array
    {
        try {
            $currentMonth = now()->format('Y-m');
            $previousMonth = now()->subMonth()->format('Y-m');
            
            return [
                'current_month_hours' => $this->getMonthlyHours($currentMonth),
                'previous_month_hours' => $this->getMonthlyHours($previousMonth),
                'day_night_ratio' => $this->getDayNightRatio(),
                'project_categories' => $this->getProjectCategoriesStats(),
                'worker_utilization' => $this->getWorkerUtilization()
            ];
        } catch (\Exception $e) {
            Log::error('Business metrics collection failed', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Métriques de performance
     */
    private function getPerformanceMetrics(): array
    {
        return [
            'memory_usage' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => ini_get('memory_limit')
            ],
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status() !== false
        ];
    }

    /**
     * Métriques de cache
     */
    private function getCacheMetrics(): array
    {
        $store = Cache::getStore();
        
        return [
            'driver' => get_class($store),
            'redis_available' => $store instanceof \Illuminate\Cache\RedisStore,
            'default_ttl' => 3600,
            'tracking_ttl' => 1800,
            'costs_ttl' => 7200
        ];
    }

    /**
     * Métriques système
     */
    private function getSystemMetrics(): array
    {
        return [
            'php_sapi' => php_sapi_name(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'os' => PHP_OS,
            'timezone' => config('app.timezone'),
            'debug_mode' => config('app.debug'),
            'environment' => config('app.env')
        ];
    }

    /**
     * Heures mensuelles
     */
    private function getMonthlyHours(string $month): array
    {
        $timesheets = TimeSheetable::whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$month])->get();
        
        return [
            'total' => $timesheets->sum('hours'),
            'day' => $timesheets->where('category', 'day')->sum('hours'),
            'night' => $timesheets->where('category', 'night')->sum('hours'),
            'entries_count' => $timesheets->count()
        ];
    }

    /**
     * Ratio jour/nuit
     */
    private function getDayNightRatio(): array
    {
        $dayHours = TimeSheetable::where('category', 'day')->sum('hours');
        $nightHours = TimeSheetable::where('category', 'night')->sum('hours');
        $total = $dayHours + $nightHours;
        
        if ($total === 0) {
            return ['day' => 0, 'night' => 0, 'total' => 0];
        }
        
        return [
            'day' => round(($dayHours / $total) * 100, 2),
            'night' => round(($nightHours / $total) * 100, 2),
            'total' => $total
        ];
    }

    /**
     * Statistiques des catégories de projets
     */
    private function getProjectCategoriesStats(): array
    {
        return Project::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Utilisation des workers
     */
    private function getWorkerUtilization(): array
    {
        $activeWorkers = Worker::where('status', 'active')->count();
        $workersWithHours = Worker::whereHas('timesheets', function ($query) {
            $query->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()]);
        })->count();
        
        return [
            'active_workers' => $activeWorkers,
            'workers_with_hours' => $workersWithHours,
            'utilization_rate' => $activeWorkers > 0 ? round(($workersWithHours / $activeWorkers) * 100, 2) : 0
        ];
    }

    /**
     * Enregistrer une métrique personnalisée
     */
    public function logMetric(string $name, mixed $value, array $tags = []): void
    {
        Log::info("Custom metric: {$name}", [
            'value' => $value,
            'tags' => $tags,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Surveiller les performances d'une opération
     */
    public function measureOperation(string $operationName, callable $callback): mixed
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        try {
            $result = $callback();
            
            $endTime = microtime(true);
            $endMemory = memory_get_usage(true);
            
            $executionTime = ($endTime - $startTime) * 1000; // ms
            $memoryUsed = $endMemory - $startMemory;
            
            $this->logMetric("operation_performance", [
                'operation' => $operationName,
                'execution_time_ms' => round($executionTime, 2),
                'memory_used_bytes' => $memoryUsed,
                'status' => 'success'
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000;
            
            $this->logMetric("operation_performance", [
                'operation' => $operationName,
                'execution_time_ms' => round($executionTime, 2),
                'status' => 'error',
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Vérifier la santé de l'application
     */
    public function healthCheck(): array
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'disk_space' => $this->checkDiskSpace(),
            'memory' => $this->checkMemory()
        ];
        
        $overallStatus = collect($checks)->every(fn($check) => $check['status'] === 'ok') ? 'healthy' : 'unhealthy';
        
        return [
            'overall_status' => $overallStatus,
            'timestamp' => now()->toISOString(),
            'checks' => $checks
        ];
    }

    private function checkDatabase(): array
    {
        try {
            \DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
    }

    private function checkCache(): array
    {
        try {
            Cache::put('health_check', 'test', 60);
            $value = Cache::get('health_check');
            return ['status' => $value === 'test' ? 'ok' : 'error', 'message' => 'Cache test'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache error: ' . $e->getMessage()];
        }
    }

    private function checkDiskSpace(): array
    {
        $freeSpace = disk_free_space('/');
        $totalSpace = disk_total_space('/');
        $usedPercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;
        
        return [
            'status' => $usedPercent < 90 ? 'ok' : 'warning',
            'message' => "Disk usage: {$usedPercent}%",
            'free_space' => $freeSpace,
            'total_space' => $totalSpace
        ];
    }

    private function checkMemory(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        
        if ($memoryLimit === '-1') {
            return ['status' => 'ok', 'message' => 'No memory limit'];
        }
        
        $limitBytes = $this->convertToBytes($memoryLimit);
        $usedPercent = ($memoryUsage / $limitBytes) * 100;
        
        return [
            'status' => $usedPercent < 80 ? 'ok' : 'warning',
            'message' => "Memory usage: {$usedPercent}%",
            'current_usage' => $memoryUsage,
            'limit' => $limitBytes
        ];
    }

    private function convertToBytes(string $value): int
    {
        $unit = strtolower(substr($value, -1));
        $value = (int) $value;
        
        switch ($unit) {
            case 'g': $value *= 1024;
            case 'm': $value *= 1024;
            case 'k': $value *= 1024;
        }
        
        return $value;
    }
}