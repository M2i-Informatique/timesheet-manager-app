<?php

namespace App\Services\Tracking;

interface TrackingServiceInterface
{
    /**
     * Récupérer toutes les données nécessaires pour l'affichage de la page de tracking
     *
     * @param array $params [project_id, month, year, category]
     * @return array Données formatées pour la vue
     */
    public function getTrackingData(array $params): array;

    /**
     * Construire les données d'entrée pour Handsontable
     *
     * @param \App\Models\Project $project
     * @param int $month
     * @param int $year
     * @param string $category
     * @return array
     */
    public function buildEntriesData(\App\Models\Project $project, int $month, int $year, string $category): array;

    /**
     * Construire les données de récapitulatif mensuel
     *
     * @param \App\Models\Project $project
     * @param int $month
     * @param int $year
     * @return array
     */
    public function buildRecapData(\App\Models\Project $project, int $month, int $year): array;

    /**
     * Calculer les KPIs (heures totales, coûts)
     *
     * @param \App\Models\Project $project
     * @param int $month
     * @param int $year
     * @return array
     */
    public function calculateKPIs(\App\Models\Project $project, int $month, int $year): array;

    /**
     * Préparer les données de navigation (mois précédent/suivant)
     *
     * @param int $month
     * @param int $year
     * @return array
     */
    public function buildNavigationData(int $month, int $year): array;

    /**
     * Récupérer les workers/interims disponibles (non assignés au projet)
     *
     * @param int $projectId
     * @return array
     */
    public function getAvailableEmployees(int $projectId): array;

    /**
     * Récupérer les jours non travaillés pour un mois donné
     *
     * @param int $month
     * @param int $year
     * @return array
     */
    public function getNonWorkingDays(int $month, int $year): array;
}