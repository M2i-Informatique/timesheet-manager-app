<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\TimesheetController;
use App\Http\Controllers\Api\V1\MetricsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route de test pour vérifier l'authentification
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API V1 - Timesheet Management
Route::prefix('v1')->group(function () {
    
    // Routes publiques pour les tests (à sécuriser en production)
    Route::prefix('timesheets')->group(function () {
        
        // GET /api/v1/timesheets/show - Récupérer les données de pointage
        Route::get('/show', [TimesheetController::class, 'show']);
        
        // POST /api/v1/timesheets - Sauvegarder les données de pointage
        Route::post('/', [TimesheetController::class, 'store']);
        
        // GET /api/v1/timesheets/costs - Récupérer les coûts d'un projet
        Route::get('/costs', [TimesheetController::class, 'costs']);
        
        // POST /api/v1/timesheets/assign-employee - Assigner un employé
        Route::post('/assign-employee', [TimesheetController::class, 'assignEmployee']);
    });
    
    // Routes pour le monitoring et les métriques
    Route::prefix('metrics')->group(function () {
        
        // GET /api/v1/metrics - Toutes les métriques
        Route::get('/', [MetricsController::class, 'index']);
        
        // GET /api/v1/metrics/health - Health check
        Route::get('/health', [MetricsController::class, 'health']);
        
        // GET /api/v1/metrics/cache - Statistiques du cache
        Route::get('/cache', [MetricsController::class, 'cache']);
        
        // DELETE /api/v1/metrics/cache - Vider le cache
        Route::delete('/cache', [MetricsController::class, 'clearCache']);
        
        // POST /api/v1/metrics/performance - Mesurer une opération
        Route::post('/performance', [MetricsController::class, 'performance']);
    });
    
    // Routes sécurisées (à implémenter avec middleware auth)
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // Routes pour les rapports avancés
        Route::prefix('reports')->group(function () {
            // À implémenter : endpoints pour les rapports
        });
        
        // Routes pour l'administration
        Route::prefix('admin')->group(function () {
            // À implémenter : endpoints pour l'administration
        });
    });
});

// Gestion des erreurs 404 pour les routes API
Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Endpoint non trouvé',
        'error' => 'Cette route API n\'existe pas'
    ], 404);
});