<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour les données de pointage
 */
class TrackingDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'project' => [
                'id' => $this->resource['project']->id,
                'code' => $this->resource['project']->code,
                'name' => $this->resource['project']->name,
                'category' => $this->resource['project']->category,
                'status' => $this->resource['project']->status,
                'zone' => $this->resource['project']->zone ? [
                    'id' => $this->resource['project']->zone->id,
                    'name' => $this->resource['project']->zone->name,
                    'rate' => $this->resource['project']->zone->rate
                ] : null
            ],
            'period' => [
                'month' => $this->resource['month'],
                'year' => $this->resource['year'],
                'category' => $this->resource['category'],
                'days_in_month' => $this->resource['daysInMonth']
            ],
            'entries' => $this->resource['entriesData'],
            'summary' => [
                'total_hours_current_month' => $this->resource['totalHoursCurrentMonth'],
                'cost_worker_total' => $this->resource['costWorkerTotal'],
                'recap' => $this->resource['recap']
            ],
            'navigation' => [
                'previous_month' => $this->resource['previousMonth'] ?? null,
                'next_month' => $this->resource['nextMonth'] ?? null
            ],
            'available_employees' => [
                'workers' => $this->resource['availableWorkers'] ?? [],
                'interims' => $this->resource['availableInterims'] ?? []
            ],
            'non_working_days' => $this->resource['nonWorkingDays'] ?? [],
            'meta' => [
                'generated_at' => now()->toISOString(),
                'version' => 'v1'
            ]
        ];
    }
}