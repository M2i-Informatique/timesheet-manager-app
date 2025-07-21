<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Monitoring\MetricsService;
use App\Services\Cache\CacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller pour les métriques et le monitoring
 */
class MetricsController extends Controller
{
    private MetricsService $metricsService;
    private CacheService $cacheService;

    public function __construct(MetricsService $metricsService, CacheService $cacheService)
    {
        $this->metricsService = $metricsService;
        $this->cacheService = $cacheService;
    }

    /**
     * Récupérer toutes les métriques
     */
    public function index(): JsonResponse
    {
        try {
            $metrics = $this->metricsService->collectMetrics();
            
            return response()->json([
                'status' => 'success',
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la collecte des métriques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Health check de l'application
     */
    public function health(): JsonResponse
    {
        try {
            $health = $this->metricsService->healthCheck();
            
            $statusCode = $health['overall_status'] === 'healthy' ? 200 : 503;
            
            return response()->json([
                'status' => $health['overall_status'],
                'data' => $health
            ], $statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Health check failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques du cache
     */
    public function cache(): JsonResponse
    {
        try {
            $stats = $this->cacheService->getCacheStats();
            
            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des stats cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vider le cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->cacheService->flushAll();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Cache vidé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du vidage du cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Métriques de performance pour une opération
     */
    public function performance(Request $request): JsonResponse
    {
        $request->validate([
            'operation' => 'required|string',
            'project_id' => 'nullable|integer',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:2030'
        ]);

        try {
            $operationName = $request->input('operation');
            
            $result = $this->metricsService->measureOperation($operationName, function () use ($request) {
                // Simuler différentes opérations selon le paramètre
                switch ($request->input('operation')) {
                    case 'tracking_data':
                        return $this->simulateTrackingOperation($request);
                    case 'costs_calculation':
                        return $this->simulateCostsOperation($request);
                    default:
                        return ['message' => 'Opération de test'];
                }
            });
            
            return response()->json([
                'status' => 'success',
                'data' => $result,
                'message' => 'Opération mesurée avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mesure de performance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simuler une opération de récupération de données de pointage
     */
    private function simulateTrackingOperation(Request $request): array
    {
        $projectId = $request->input('project_id', 1);
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        // Simuler une opération coûteuse
        usleep(rand(100000, 500000)); // 0.1 à 0.5 secondes
        
        return [
            'operation' => 'tracking_data',
            'project_id' => $projectId,
            'month' => $month,
            'year' => $year,
            'simulated' => true
        ];
    }

    /**
     * Simuler une opération de calcul de coûts
     */
    private function simulateCostsOperation(Request $request): array
    {
        $projectId = $request->input('project_id', 1);
        
        // Simuler une opération coûteuse
        usleep(rand(200000, 800000)); // 0.2 à 0.8 secondes
        
        return [
            'operation' => 'costs_calculation',
            'project_id' => $projectId,
            'simulated' => true
        ];
    }
}