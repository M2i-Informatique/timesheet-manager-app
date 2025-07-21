<?php

namespace App\CQRS\Queries;

use App\CQRS\QueryInterface;
use Illuminate\Support\Str;

/**
 * Query pour récupérer les coûts d'un projet
 */
class GetProjectCostsQuery implements QueryInterface
{
    private string $id;
    private int $projectId;
    private ?string $startDate;
    private ?string $endDate;
    private bool $detailed;

    public function __construct(
        int $projectId,
        ?string $startDate = null,
        ?string $endDate = null,
        bool $detailed = false
    ) {
        $this->id = Str::uuid()->toString();
        $this->projectId = $projectId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->detailed = $detailed;
    }

    public function validate(): bool
    {
        if ($this->projectId <= 0) {
            return false;
        }

        if ($this->startDate && !$this->isValidDate($this->startDate)) {
            return false;
        }

        if ($this->endDate && !$this->isValidDate($this->endDate)) {
            return false;
        }

        if ($this->startDate && $this->endDate && $this->startDate > $this->endDate) {
            return false;
        }

        return true;
    }

    public function getParameters(): array
    {
        return [
            'project_id' => $this->projectId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'detailed' => $this->detailed,
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

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function isDetailed(): bool
    {
        return $this->detailed;
    }

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}