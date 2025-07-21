<?php

namespace App\ValueObjects;

use App\Models\Worker;
use App\Services\Salary\WorkerSalaryService;

/**
 * Value Object pour encapsuler les calculs de taux horaire
 * Immuable et thread-safe
 */
readonly class HourlyRate
{
    public function __construct(
        public float $base,
        public float $charged,
        public float $chargeRate
    ) {}

    /**
     * Créer un HourlyRate depuis un Worker
     * Utilise le WorkerSalaryService pour les calculs
     */
    public static function fromWorker(Worker $worker, float $chargeRate): ?self
    {
        $salaryService = app(WorkerSalaryService::class);
        
        $base = $salaryService->calculateHourlyRate($worker);
        if (!$base) {
            return null;
        }
        
        $charged = $salaryService->calculateChargedRate($worker, $chargeRate);
        if (!$charged) {
            return null;
        }
        
        return new self($base, $charged, $chargeRate);
    }

    /**
     * Créer un HourlyRate depuis un Worker avec les settings de la base
     */
    public static function fromWorkerWithSettings(Worker $worker): ?self
    {
        $salaryService = app(WorkerSalaryService::class);
        
        $base = $salaryService->calculateHourlyRate($worker);
        if (!$base) {
            return null;
        }
        
        $charged = $salaryService->calculateChargedRateFromSettings($worker);
        if (!$charged) {
            return null;
        }
        
        // Récupérer le taux de charge depuis les settings
        $chargeRate = (float) \App\Models\Setting::getValue('rate_charged', config('business.default_charge_rate', 70));
        
        return new self($base, $charged, $chargeRate);
    }

    /**
     * Créer un HourlyRate depuis des valeurs directes
     */
    public static function fromValues(float $base, float $chargeRate): self
    {
        $charged = $base * (1 + ($chargeRate / 100));
        return new self($base, $charged, $chargeRate);
    }

    /**
     * Vérifier si le taux horaire est valide
     */
    public function isValid(): bool
    {
        return $this->base > 0 && $this->charged > 0 && $this->chargeRate >= 0;
    }

    /**
     * Obtenir la majoration en valeur absolue
     */
    public function getMarkup(): float
    {
        return $this->charged - $this->base;
    }

    /**
     * Obtenir le facteur de charge (1.7 pour 70%)
     */
    public function getChargeFactor(): float
    {
        return 1 + ($this->chargeRate / 100);
    }

    /**
     * Calculer le coût pour un nombre d'heures donné
     */
    public function calculateCost(float $hours, bool $useChargedRate = true): float
    {
        $rate = $useChargedRate ? $this->charged : $this->base;
        return $rate * $hours;
    }

    /**
     * Comparer deux taux horaires
     */
    public function equals(HourlyRate $other): bool
    {
        return abs($this->base - $other->base) < 0.01 
            && abs($this->charged - $other->charged) < 0.01
            && abs($this->chargeRate - $other->chargeRate) < 0.01;
    }

    /**
     * Représentation string pour debug
     */
    public function __toString(): string
    {
        return sprintf(
            'HourlyRate{base: %.2f€, charged: %.2f€, charge: %.1f%%}',
            $this->base,
            $this->charged,
            $this->chargeRate
        );
    }

    /**
     * Sérialiser en array
     */
    public function toArray(): array
    {
        return [
            'base' => $this->base,
            'charged' => $this->charged,
            'charge_rate' => $this->chargeRate,
            'markup' => $this->getMarkup(),
            'charge_factor' => $this->getChargeFactor()
        ];
    }

    /**
     * Créer depuis un array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['base'],
            $data['charged'],
            $data['charge_rate']
        );
    }
}