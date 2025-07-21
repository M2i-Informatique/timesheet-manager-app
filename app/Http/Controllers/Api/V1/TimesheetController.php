<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\CQRS\CommandBus;
use App\CQRS\QueryBus;
use App\CQRS\Commands\SaveTimesheetCommand;
use App\CQRS\Commands\AssignEmployeeCommand;
use App\CQRS\Queries\GetTrackingDataQuery;
use App\CQRS\Queries\GetProjectCostsQuery;
use App\Http\Resources\V1\TrackingDataResource;
use App\Http\Resources\V1\ProjectCostsResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * API Controller pour les opérations de pointage
 * 
 * @group Timesheet Management
 */
class TimesheetController extends Controller
{
    private CommandBus $commandBus;
    private QueryBus $queryBus;

    public function __construct(CommandBus $commandBus, QueryBus $queryBus)
    {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    /**
     * Récupérer les données de pointage d'un projet
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|integer|exists:projects,id',
                'month' => 'required|integer|min:1|max:12',
                'year' => 'required|integer|min:1900|max:2099',
                'category' => 'nullable|string|in:day,night'
            ]);

            $query = new GetTrackingDataQuery(
                $validated['project_id'],
                $validated['month'],
                $validated['year'],
                $validated['category'] ?? 'day'
            );

            $data = $this->queryBus->dispatch($query);

            return response()->json([
                'status' => 'success',
                'data' => new TrackingDataResource($data),
                'message' => 'Données de pointage récupérées avec succès'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Données de validation invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des données',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sauvegarder les données de pointage
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|integer|exists:projects,id',
                'month' => 'required|integer|min:1|max:12',
                'year' => 'required|integer|min:1900|max:2099',
                'category' => 'required|string|in:day,night',
                'data' => 'required|array',
                'data.*.id' => 'required|integer',
                'data.*.model_type' => 'required|string|in:worker,interim',
                'data.*.days' => 'required|array',
                'data.*.days.*' => 'nullable|numeric|min:0|max:12'
            ]);

            $command = new SaveTimesheetCommand(
                $validated['project_id'],
                $validated['month'],
                $validated['year'],
                $validated['category'],
                $validated['data']
            );

            $result = $this->commandBus->dispatch($command);

            return response()->json([
                'status' => 'success',
                'data' => $result,
                'message' => 'Données sauvegardées avec succès'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Données de validation invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation métier',
                'error' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la sauvegarde',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les coûts d'un projet
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function costs(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|integer|exists:projects,id',
                'start_date' => 'nullable|date|date_format:Y-m-d',
                'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
                'detailed' => 'nullable|boolean'
            ]);

            $query = new GetProjectCostsQuery(
                $validated['project_id'],
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null,
                $validated['detailed'] ?? false
            );

            $data = $this->queryBus->dispatch($query);

            return response()->json([
                'status' => 'success',
                'data' => new ProjectCostsResource($data),
                'message' => 'Coûts du projet récupérés avec succès'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Données de validation invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des coûts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assigner un employé à un projet
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function assignEmployee(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|integer|exists:projects,id',
                'employee_type' => 'required|string|in:worker,interim',
                'employee_id' => 'required|integer'
            ]);

            $command = new AssignEmployeeCommand(
                $validated['project_id'],
                $validated['employee_type'],
                $validated['employee_id']
            );

            $result = $this->commandBus->dispatch($command);

            return response()->json([
                'status' => 'success',
                'data' => $result,
                'message' => 'Employé assigné avec succès'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Données de validation invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'assignation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}