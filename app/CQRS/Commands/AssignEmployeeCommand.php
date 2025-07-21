<?php

namespace App\CQRS\Commands;

use App\CQRS\CommandInterface;
use Illuminate\Support\Str;

/**
 * Commande pour assigner un employé à un projet
 */
class AssignEmployeeCommand implements CommandInterface
{
    private string $id;
    private int $projectId;
    private string $employeeType;
    private int $employeeId;

    public function __construct(
        int $projectId,
        string $employeeType,
        int $employeeId
    ) {
        $this->id = Str::uuid()->toString();
        $this->projectId = $projectId;
        $this->employeeType = $employeeType;
        $this->employeeId = $employeeId;
    }

    public function validate(): bool
    {
        if ($this->projectId <= 0) {
            return false;
        }

        if (!in_array($this->employeeType, ['worker', 'interim'])) {
            return false;
        }

        if ($this->employeeId <= 0) {
            return false;
        }

        return true;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'employee_type' => $this->employeeType,
            'employee_id' => $this->employeeId,
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getEmployeeType(): string
    {
        return $this->employeeType;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }
}