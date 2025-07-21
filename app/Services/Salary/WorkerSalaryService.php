<?php

namespace App\Services\Salary;

use App\Models\Worker;
use App\Models\Setting;
use App\Services\Config\BusinessConfigService;
use Illuminate\Support\Facades\Log;

class WorkerSalaryService implements WorkerSalaryServiceInterface
{
    private BusinessConfigService $configService;

    public function __construct(BusinessConfigService $configService)
    {
        $this->configService = $configService;
    }
    /**
     * Calculer le taux horaire de base d'un worker
     * LOGIQUE EXTRAITE IDENTIQUE de Worker::getHourlyRateAttribute()
     */
    public function calculateHourlyRate(Worker $worker): ?float
    {
        if (!$this->validateWorkerData($worker)) {
            return null;
        }

        // Formule exacte du modèle actuel (lignes 24-25 de Worker.php)
        $monthlyHours = $worker->contract_hours * ($this->configService->getWeeksPerYear() / 12);
        $hourlyRate = $worker->monthly_salary / $monthlyHours;
        
        Log::info("Worker {$worker->id} ({$worker->first_name} {$worker->last_name}) hourly rate calculated: {$hourlyRate}");
        
        return $hourlyRate;
    }

    /**
     * Calculer le taux horaire chargé d'un worker
     * LOGIQUE EXTRAITE IDENTIQUE de Worker::getHourlyRateChargedAttribute()
     */
    public function calculateChargedRate(Worker $worker, float $chargePercentage): ?float
    {
        $hourlyRate = $this->calculateHourlyRate($worker);
        if (!$hourlyRate) {
            return null;
        }

        // Formule exacte du modèle actuel (lignes 47-48 de Worker.php)
        $factor = 1 + ($chargePercentage / 100);
        $hourlyRateCharged = $hourlyRate * $factor;
        
        Log::info("Worker {$worker->id} ({$worker->first_name} {$worker->last_name}) hourly rate charged calculated: {$hourlyRateCharged} (base: {$hourlyRate}, factor: {$factor})");

        return $hourlyRateCharged;
    }

    /**
     * Calculer le taux horaire chargé avec les settings de la base
     * (Compatible avec l'accesseur actuel)
     */
    public function calculateChargedRateFromSettings(Worker $worker): ?float
    {
        $chargePercentage = (float) Setting::getValue('rate_charged', config('business.default_charge_rate', 70));
        return $this->calculateChargedRate($worker, $chargePercentage);
    }

    /**
     * Calculer le salaire mensuel théorique basé sur les heures contractuelles
     */
    public function calculateTheoreticalMonthlySalary(Worker $worker): ?float
    {
        if (!$this->validateWorkerData($worker)) {
            return null;
        }

        // Le salaire mensuel est déjà stocké, mais on peut le recalculer théoriquement
        // si on avait un taux horaire de base
        return $worker->monthly_salary;
    }

    /**
     * Calculer le coût annuel employeur
     */
    public function calculateYearlyCost(Worker $worker, float $chargePercentage): ?float
    {
        $chargedRate = $this->calculateChargedRate($worker, $chargePercentage);
        if (!$chargedRate) {
            return null;
        }

        // Coût annuel = taux chargé * heures contractuelles * 52 semaines
        $annualHours = $worker->contract_hours * config('business.weeks_per_year', 52);
        return $chargedRate * $annualHours;
    }

    /**
     * Valider les données du worker pour les calculs
     */
    public function validateWorkerData(Worker $worker): bool
    {
        return $worker->contract_hours > 0 && $worker->monthly_salary > 0;
    }

    /**
     * Calculer les heures mensuelles théoriques
     */
    public function calculateMonthlyHours(Worker $worker): ?float
    {
        if (!$worker->contract_hours) {
            return null;
        }

        return $worker->contract_hours * (config('business.weeks_per_year', 52) / 12);
    }

    /**
     * Obtenir un résumé complet des calculs salariaux
     */
    public function getSalaryBreakdown(Worker $worker): ?array
    {
        if (!$this->validateWorkerData($worker)) {
            return null;
        }

        $chargePercentage = (float) Setting::getValue('rate_charged', config('business.default_charge_rate', 70));
        
        $monthlyHours = $this->calculateMonthlyHours($worker);
        $hourlyRate = $this->calculateHourlyRate($worker);
        $chargedRate = $this->calculateChargedRate($worker, $chargePercentage);
        $yearlyCost = $this->calculateYearlyCost($worker, $chargePercentage);

        return [
            'worker_id' => $worker->id,
            'worker_name' => $worker->first_name . ' ' . $worker->last_name,
            'contract_hours' => $worker->contract_hours,
            'monthly_salary' => $worker->monthly_salary,
            'monthly_hours' => $monthlyHours,
            'hourly_rate' => $hourlyRate,
            'charge_percentage' => $chargePercentage,
            'charged_rate' => $chargedRate,
            'yearly_cost' => $yearlyCost,
            'category' => $worker->category
        ];
    }
}