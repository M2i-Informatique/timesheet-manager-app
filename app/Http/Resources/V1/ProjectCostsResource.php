<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour les coûts de projet
 */
class ProjectCostsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Déterminer si c'est un résumé ou un détail
        $isDetailed = isset($this->resource['data']);

        if ($isDetailed) {
            return [
                'type' => 'detailed',
                'project' => [
                    'id' => $this->resource['data']['id'],
                    'code' => $this->resource['data']['attributes']['code'],
                    'name' => $this->resource['data']['attributes']['name'],
                    'category' => $this->resource['data']['attributes']['category'],
                    'status' => $this->resource['data']['attributes']['status'],
                    'zone' => $this->resource['data']['relationships']['zone']
                ],
                'period' => [
                    'start_date' => $this->resource['data']['start_date'],
                    'end_date' => $this->resource['data']['end_date']
                ],
                'totals' => [
                    'hours' => $this->resource['data']['total_hours'],
                    'worker_hours' => $this->resource['data']['total_worker_hours'],
                    'interim_hours' => $this->resource['data']['total_interim_hours'],
                    'cost' => $this->resource['data']['total_cost']
                ],
                'workers' => $this->resource['data']['relationships']['workers'],
                'interims' => $this->resource['data']['relationships']['interims'],
                'meta' => [
                    'generated_at' => now()->toISOString(),
                    'version' => 'v1'
                ]
            ];
        }

        return [
            'type' => 'summary',
            'totals' => [
                'hours' => $this->resource['hours'],
                'worker_hours' => $this->resource['worker_hours'],
                'interim_hours' => $this->resource['interim_hours'],
                'cost' => $this->resource['cost']
            ],
            'meta' => [
                'generated_at' => now()->toISOString(),
                'version' => 'v1'
            ]
        ];
    }
}