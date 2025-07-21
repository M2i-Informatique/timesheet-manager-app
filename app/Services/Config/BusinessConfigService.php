<?php

namespace App\Services\Config;

use Illuminate\Support\Facades\Config;

/**
 * Service pour accéder à la configuration métier de façon centralisée
 */
class BusinessConfigService
{
    /**
     * Obtenir la configuration des coûts
     */
    public function getCostsConfig(): array
    {
        return Config::get('business.costs', []);
    }

    /**
     * Obtenir le taux de charge par défaut
     */
    public function getDefaultChargeRate(): float
    {
        return (float) Config::get('business.costs.default_charge_rate', 70);
    }

    /**
     * Obtenir la valeur du panier repas par défaut
     */
    public function getDefaultBasketValue(): float
    {
        return (float) Config::get('business.costs.default_basket_value', 11);
    }

    /**
     * Obtenir le nombre de semaines par année
     */
    public function getWeeksPerYear(): int
    {
        return (int) Config::get('business.costs.weeks_per_year', 52);
    }

    /**
     * Obtenir le multiplicateur de nuit
     */
    public function getNightMultiplier(): float
    {
        return (float) Config::get('business.costs.night_multiplier', 2.0);
    }

    /**
     * Obtenir la configuration des feuilles de temps
     */
    public function getTimesheetConfig(): array
    {
        return Config::get('business.timesheet', []);
    }

    /**
     * Obtenir le nombre maximum d'heures par jour
     */
    public function getMaxHoursPerDay(): int
    {
        return (int) Config::get('business.timesheet.max_hours_per_day', 12);
    }

    /**
     * Obtenir les catégories autorisées
     */
    public function getAllowedCategories(): array
    {
        return Config::get('business.timesheet.allowed_categories', ['day', 'night']);
    }

    /**
     * Obtenir les types d'employés autorisés
     */
    public function getAllowedEmployeeTypes(): array
    {
        return Config::get('business.timesheet.allowed_employee_types', ['worker', 'interim']);
    }

    /**
     * Obtenir la configuration des projets
     */
    public function getProjectsConfig(): array
    {
        return Config::get('business.projects', []);
    }

    /**
     * Obtenir les catégories de projets autorisées
     */
    public function getAllowedProjectCategories(): array
    {
        return Config::get('business.projects.allowed_categories', ['mh', 'go', 'other']);
    }

    /**
     * Obtenir les statuts de projets autorisés
     */
    public function getAllowedProjectStatuses(): array
    {
        return Config::get('business.projects.allowed_statuses', ['active', 'inactive', 'completed', 'cancelled']);
    }

    /**
     * Obtenir la configuration des workers
     */
    public function getWorkersConfig(): array
    {
        return Config::get('business.workers', []);
    }

    /**
     * Obtenir les catégories de workers autorisées
     */
    public function getAllowedWorkerCategories(): array
    {
        return Config::get('business.workers.allowed_categories', ['worker', 'etam']);
    }

    /**
     * Obtenir les heures contractuelles minimales
     */
    public function getMinContractHours(): int
    {
        return (int) Config::get('business.workers.min_contract_hours', 20);
    }

    /**
     * Obtenir les heures contractuelles maximales
     */
    public function getMaxContractHours(): int
    {
        return (int) Config::get('business.workers.max_contract_hours', 48);
    }

    /**
     * Vérifier si les workers ETAM reçoivent une compensation zone
     */
    public function isEtamZoneCompensationEnabled(): bool
    {
        return (bool) Config::get('business.workers.etam_zone_compensation', false);
    }

    /**
     * Obtenir la configuration des zones
     */
    public function getZonesConfig(): array
    {
        return Config::get('business.zones', []);
    }

    /**
     * Obtenir le taux de zone par défaut
     */
    public function getDefaultZoneRate(): float
    {
        return (float) Config::get('business.zones.default_rate', 0);
    }

    /**
     * Obtenir la configuration des exports
     */
    public function getExportsConfig(): array
    {
        return Config::get('business.exports', []);
    }

    /**
     * Obtenir les formats d'export autorisés
     */
    public function getAllowedExportFormats(): array
    {
        return Config::get('business.exports.allowed_formats', ['xlsx', 'csv', 'pdf']);
    }

    /**
     * Obtenir le nombre maximum de lignes pour les exports
     */
    public function getMaxExportRows(): int
    {
        return (int) Config::get('business.exports.max_export_rows', 10000);
    }

    /**
     * Obtenir la configuration de performance
     */
    public function getPerformanceConfig(): array
    {
        return Config::get('business.performance', []);
    }

    /**
     * Obtenir le TTL du cache pour les données de pointage
     */
    public function getTrackingCacheTtl(): int
    {
        return (int) Config::get('business.performance.tracking_cache_ttl', 1800);
    }

    /**
     * Obtenir le TTL du cache pour les coûts
     */
    public function getCostsCacheTtl(): int
    {
        return (int) Config::get('business.performance.costs_cache_ttl', 7200);
    }

    /**
     * Obtenir la taille de pagination par défaut
     */
    public function getDefaultPaginationSize(): int
    {
        return (int) Config::get('business.performance.default_pagination_size', 50);
    }

    /**
     * Obtenir la configuration de validation
     */
    public function getValidationConfig(): array
    {
        return Config::get('business.validation', []);
    }

    /**
     * Vérifier si la validation stricte est activée
     */
    public function isStrictValidationEnabled(): bool
    {
        return (bool) Config::get('business.validation.strict_validation', true);
    }

    /**
     * Vérifier si la validation des heures cohérentes est activée
     */
    public function isCoherentHoursValidationEnabled(): bool
    {
        return (bool) Config::get('business.validation.validate_coherent_hours', true);
    }

    /**
     * Obtenir la tolérance pour les erreurs de calcul
     */
    public function getCalculationTolerance(): float
    {
        return (float) Config::get('business.validation.calculation_tolerance', 0.01);
    }

    /**
     * Obtenir la configuration de sécurité
     */
    public function getSecurityConfig(): array
    {
        return Config::get('business.security', []);
    }

    /**
     * Vérifier si l'audit trail est activé
     */
    public function isAuditTrailEnabled(): bool
    {
        return (bool) Config::get('business.security.enable_audit_trail', true);
    }

    /**
     * Obtenir la configuration des notifications
     */
    public function getNotificationsConfig(): array
    {
        return Config::get('business.notifications', []);
    }

    /**
     * Vérifier si les notifications email sont activées
     */
    public function areEmailNotificationsEnabled(): bool
    {
        return (bool) Config::get('business.notifications.email_notifications', true);
    }

    /**
     * Obtenir l'email de l'administrateur
     */
    public function getAdminEmail(): string
    {
        return Config::get('business.notifications.admin_email', 'admin@example.com');
    }

    /**
     * Obtenir la configuration de développement
     */
    public function getDevelopmentConfig(): array
    {
        return Config::get('business.development', []);
    }

    /**
     * Vérifier si le mode debug des calculs est activé
     */
    public function isDebugCalculationsEnabled(): bool
    {
        return (bool) Config::get('business.development.debug_calculations', false);
    }

    /**
     * Vérifier si le mode démo est activé
     */
    public function isDemoMode(): bool
    {
        return (bool) Config::get('business.development.demo_mode', false);
    }

    /**
     * Obtenir toute la configuration métier
     */
    public function getAllBusinessConfig(): array
    {
        return Config::get('business', []);
    }

    /**
     * Vérifier si une valeur de configuration existe
     */
    public function hasConfig(string $key): bool
    {
        return Config::has("business.{$key}");
    }

    /**
     * Obtenir une valeur de configuration avec une valeur par défaut
     */
    public function getConfig(string $key, mixed $default = null): mixed
    {
        return Config::get("business.{$key}", $default);
    }

    /**
     * Définir une valeur de configuration (runtime seulement)
     */
    public function setConfig(string $key, mixed $value): void
    {
        Config::set("business.{$key}", $value);
    }

    /**
     * Obtenir les métadonnées de configuration
     */
    public function getConfigMetadata(): array
    {
        return [
            'config_file' => 'config/business.php',
            'last_loaded' => now()->toISOString(),
            'sections' => [
                'costs' => 'Paramètres de calcul des coûts',
                'timesheet' => 'Paramètres de pointage',
                'projects' => 'Paramètres des projets',
                'workers' => 'Paramètres des workers',
                'zones' => 'Paramètres des zones',
                'exports' => 'Paramètres des exports',
                'performance' => 'Paramètres de performance',
                'validation' => 'Paramètres de validation',
                'security' => 'Paramètres de sécurité',
                'notifications' => 'Paramètres de notification',
                'development' => 'Paramètres de développement'
            ]
        ];
    }
}