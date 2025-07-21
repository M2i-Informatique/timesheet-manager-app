<?php

namespace App\Services\Salary;

use App\Models\Worker;

interface WorkerSalaryServiceInterface
{
    /**
     * Calculer le taux horaire de base d'un worker
     * Formule : monthly_salary / ((contract_hours * 52) / 12)
     *
     * @param Worker $worker
     * @return float|null
     */
    public function calculateHourlyRate(Worker $worker): ?float;

    /**
     * Calculer le taux horaire chargé d'un worker
     * Formule : hourly_rate * (1 + charge_percentage/100)
     *
     * @param Worker $worker
     * @param float $chargePercentage
     * @return float|null
     */
    public function calculateChargedRate(Worker $worker, float $chargePercentage): ?float;

    /**
     * Calculer le salaire mensuel théorique basé sur les heures contractuelles
     *
     * @param Worker $worker
     * @return float|null
     */
    public function calculateTheoreticalMonthlySalary(Worker $worker): ?float;

    /**
     * Calculer le coût annuel employeur
     *
     * @param Worker $worker
     * @param float $chargePercentage
     * @return float|null
     */
    public function calculateYearlyCost(Worker $worker, float $chargePercentage): ?float;

    /**
     * Valider les données du worker pour les calculs
     *
     * @param Worker $worker
     * @return bool
     */
    public function validateWorkerData(Worker $worker): bool;
}