<?php

namespace App\CQRS\Queries;

use App\CQRS\QueryInterface;
use Illuminate\Support\Str;

/**
 * Query pour récupérer les données de pointage d'un projet
 */
class GetTrackingDataQuery implements QueryInterface
{
    private string $id;
    private int $projectId;
    private int $month;
    private int $year;
    private string $category;

    public function __construct(
        int $projectId,
        int $month,
        int $year,
        string $category = 'day'
    ) {
        $this->id = Str::uuid()->toString();
        $this->projectId = $projectId;
        $this->month = $month;
        $this->year = $year;
        $this->category = $category;
    }

    public function validate(): bool
    {
        if ($this->projectId <= 0) {
            return false;
        }

        if ($this->month < 1 || $this->month > 12) {
            return false;
        }

        if ($this->year < 1900 || $this->year > 2099) {
            return false;
        }

        if (!in_array($this->category, ['day', 'night'])) {
            return false;
        }

        return true;
    }

    public function getParameters(): array
    {
        return [
            'project_id' => $this->projectId,
            'month' => $this->month,
            'year' => $this->year,
            'category' => $this->category,
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

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getCategory(): string
    {
        return $this->category;
    }
}