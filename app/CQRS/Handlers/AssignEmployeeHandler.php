<?php

namespace App\CQRS\Handlers;

use App\CQRS\CommandHandlerInterface;
use App\CQRS\CommandInterface;
use App\CQRS\Commands\AssignEmployeeCommand;
use App\Models\Project;
use App\Models\Worker;
use App\Models\Interim;
use App\Services\Cache\CacheService;
use InvalidArgumentException;

/**
 * Handler pour assigner un employé à un projet
 */
class AssignEmployeeHandler implements CommandHandlerInterface
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function handle(CommandInterface $command): mixed
    {
        if (!$command instanceof AssignEmployeeCommand) {
            throw new InvalidArgumentException('Expected AssignEmployeeCommand');
        }

        $project = Project::findOrFail($command->getProjectId());
        $employeeType = $command->getEmployeeType();
        $employeeId = $command->getEmployeeId();

        // Vérifier que l'employé existe
        if ($employeeType === 'worker') {
            $employee = Worker::findOrFail($employeeId);
            if ($employee->status !== 'active') {
                throw new InvalidArgumentException('Le worker doit être actif');
            }
            $project->workers()->syncWithoutDetaching([$employeeId]);
        } else {
            $employee = Interim::findOrFail($employeeId);
            if ($employee->status !== 'active') {
                throw new InvalidArgumentException('L\'interim doit être actif');
            }
            $project->interims()->syncWithoutDetaching([$employeeId]);
        }

        // Invalider le cache pour ce projet car la liste des employés disponibles a changé
        $this->cacheService->invalidateProjectCache($project->id);

        // Nom de l'employé selon le type
        $employeeName = $employeeType === 'worker' 
            ? $employee->first_name . ' ' . $employee->last_name
            : $employee->agency . ' (Intérim)';

        return [
            'success' => true,
            'message' => 'Employé assigné avec succès au projet',
            'data' => [
                'project_id' => $project->id,
                'employee_type' => $employeeType,
                'employee_id' => $employeeId,
                'employee_name' => $employeeName
            ]
        ];
    }
}