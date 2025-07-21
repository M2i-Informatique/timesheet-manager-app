<?php

namespace App\CQRS\Commands;

use App\CQRS\CommandInterface;
use Illuminate\Support\Str;

/**
 * Commande pour sauvegarder les données de pointage
 */
class SaveTimesheetCommand implements CommandInterface
{
    private string $id;
    private int $projectId;
    private int $month;
    private int $year;
    private string $category;
    private array $data;

    public function __construct(
        int $projectId,
        int $month,
        int $year,
        string $category,
        array $data
    ) {
        $this->id = Str::uuid()->toString();
        $this->projectId = $projectId;
        $this->month = $month;
        $this->year = $year;
        $this->category = $category;
        $this->data = $data;
    }

    public function validate(): bool
    {
        \Log::info('SaveTimesheetCommand::validate() starting', [
            'project_id' => $this->projectId,
            'month' => $this->month,
            'year' => $this->year,
            'category' => $this->category,
            'data_count' => count($this->data)
        ]);

        // Validation des données de base
        if ($this->projectId <= 0) {
            \Log::error('SaveTimesheetCommand validation failed: projectId <= 0', ['projectId' => $this->projectId]);
            return false;
        }

        if ($this->month < 1 || $this->month > 12) {
            \Log::error('SaveTimesheetCommand validation failed: invalid month', ['month' => $this->month]);
            return false;
        }

        if ($this->year < 1900 || $this->year > 2099) {
            \Log::error('SaveTimesheetCommand validation failed: invalid year', ['year' => $this->year]);
            return false;
        }

        if (!in_array($this->category, ['day', 'night'])) {
            \Log::error('SaveTimesheetCommand validation failed: invalid category', ['category' => $this->category]);
            return false;
        }

        if (empty($this->data)) {
            \Log::error('SaveTimesheetCommand validation failed: empty data');
            return false;
        }

        // Validation des données de pointage
        foreach ($this->data as $index => $entry) {
            \Log::info('Validating entry', ['index' => $index, 'entry' => $entry]);
            
            if (!isset($entry['id'], $entry['model_type'], $entry['days'])) {
                \Log::error('SaveTimesheetCommand validation failed: missing required fields', [
                    'index' => $index,
                    'has_id' => isset($entry['id']),
                    'has_model_type' => isset($entry['model_type']),
                    'has_days' => isset($entry['days']),
                    'entry' => $entry
                ]);
                return false;
            }

            if (!in_array($entry['model_type'], ['worker', 'interim'])) {
                \Log::error('SaveTimesheetCommand validation failed: invalid model_type', [
                    'index' => $index,
                    'model_type' => $entry['model_type']
                ]);
                return false;
            }

            if (!is_array($entry['days'])) {
                \Log::error('SaveTimesheetCommand validation failed: days is not array', [
                    'index' => $index,
                    'days_type' => gettype($entry['days'])
                ]);
                return false;
            }

            foreach ($entry['days'] as $day => $hours) {
                if ($day < 1 || $day > 31) {
                    \Log::error('SaveTimesheetCommand validation failed: invalid day', [
                        'index' => $index,
                        'day' => $day
                    ]);
                    return false;
                }

                if ($hours !== null && ($hours < 0 || $hours > 12)) {
                    \Log::error('SaveTimesheetCommand validation failed: invalid hours', [
                        'index' => $index,
                        'day' => $day,
                        'hours' => $hours
                    ]);
                    return false;
                }
            }
        }

        \Log::info('SaveTimesheetCommand validation passed');
        return true;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'month' => $this->month,
            'year' => $this->year,
            'category' => $this->category,
            'data' => $this->data,
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

    public function getData(): array
    {
        return $this->data;
    }
}