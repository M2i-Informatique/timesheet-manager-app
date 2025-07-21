<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Configuration métier de l'application timesheet
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient tous les paramètres métier configurables de 
    | l'application de gestion des feuilles de temps.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Paramètres de calcul des coûts
    |--------------------------------------------------------------------------
    */
    'costs' => [
        // Taux de charge par défaut (en pourcentage)
        'default_charge_rate' => env('BUSINESS_CHARGE_RATE', 70),
        
        // Valeur du panier repas par défaut
        'default_basket_value' => env('BUSINESS_BASKET_VALUE', 11),
        
        // Nombre de semaines par année pour les calculs
        'weeks_per_year' => env('BUSINESS_WEEKS_PER_YEAR', 52),
        
        // Nombre de jours ouvrés par semaine
        'working_days_per_week' => env('BUSINESS_WORKING_DAYS_PER_WEEK', 5),
        
        // Majoration nuit (multiplier appliqué aux heures de nuit)
        'night_multiplier' => env('BUSINESS_NIGHT_MULTIPLIER', 2.0),
        
        // Zone par défaut pour les projets sans zone
        'default_zone_rate' => env('BUSINESS_DEFAULT_ZONE_RATE', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres de pointage
    |--------------------------------------------------------------------------
    */
    'timesheet' => [
        // Nombre maximum d'heures par jour
        'max_hours_per_day' => env('BUSINESS_MAX_HOURS_PER_DAY', 12),
        
        // Nombre maximum d'heures par semaine
        'max_hours_per_week' => env('BUSINESS_MAX_HOURS_PER_WEEK', 60),
        
        // Catégories de pointage autorisées
        'allowed_categories' => ['day', 'night'],
        
        // Statuts autorisés pour les employés
        'allowed_employee_statuses' => ['active', 'inactive'],
        
        // Types d'employés autorisés
        'allowed_employee_types' => ['worker', 'interim'],
        
        // Validation automatique des heures
        'auto_validate_hours' => env('BUSINESS_AUTO_VALIDATE_HOURS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres des projets
    |--------------------------------------------------------------------------
    */
    'projects' => [
        // Catégories de projets autorisées
        'allowed_categories' => ['mh', 'go', 'other'],
        
        // Statuts de projets autorisés
        'allowed_statuses' => ['active', 'inactive', 'completed', 'cancelled'],
        
        // Statut par défaut pour les nouveaux projets
        'default_status' => 'active',
        
        // Distance maximale pour les projets (en km)
        'max_distance' => env('BUSINESS_MAX_PROJECT_DISTANCE', 500),
        
        // Assignation automatique de zone basée sur la distance
        'auto_assign_zone' => env('BUSINESS_AUTO_ASSIGN_ZONE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres des workers
    |--------------------------------------------------------------------------
    */
    'workers' => [
        // Catégories de workers autorisées
        'allowed_categories' => ['worker', 'etam'],
        
        // Statuts autorisés
        'allowed_statuses' => ['active', 'inactive'],
        
        // Heures contractuelles minimales
        'min_contract_hours' => env('BUSINESS_MIN_CONTRACT_HOURS', 20),
        
        // Heures contractuelles maximales
        'max_contract_hours' => env('BUSINESS_MAX_CONTRACT_HOURS', 48),
        
        // Salaire mensuel minimum
        'min_monthly_salary' => env('BUSINESS_MIN_MONTHLY_SALARY', 1500),
        
        // Salaire mensuel maximum
        'max_monthly_salary' => env('BUSINESS_MAX_MONTHLY_SALARY', 8000),
        
        // Les workers ETAM ne reçoivent pas de compensation zone
        'etam_zone_compensation' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres des zones
    |--------------------------------------------------------------------------
    */
    'zones' => [
        // Taux de zone par défaut
        'default_rate' => env('BUSINESS_DEFAULT_ZONE_RATE', 0),
        
        // Distance minimale pour une zone (en km)
        'min_distance' => env('BUSINESS_MIN_ZONE_DISTANCE', 0),
        
        // Distance maximale pour une zone (en km)
        'max_distance' => env('BUSINESS_MAX_ZONE_DISTANCE', 1000),
        
        // Taux maximum pour une zone
        'max_rate' => env('BUSINESS_MAX_ZONE_RATE', 100),
        
        // Calcul automatique des taux basé sur la distance
        'auto_calculate_rates' => env('BUSINESS_AUTO_CALCULATE_ZONE_RATES', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres des exports
    |--------------------------------------------------------------------------
    */
    'exports' => [
        // Formats d'export autorisés
        'allowed_formats' => ['xlsx', 'csv', 'pdf'],
        
        // Format par défaut
        'default_format' => 'xlsx',
        
        // Taille maximale des exports (nombre de lignes)
        'max_export_rows' => env('BUSINESS_MAX_EXPORT_ROWS', 10000),
        
        // Timeout pour les exports (en secondes)
        'export_timeout' => env('BUSINESS_EXPORT_TIMEOUT', 300),
        
        // Mise en forme automatique des exports
        'auto_format_exports' => env('BUSINESS_AUTO_FORMAT_EXPORTS', true),
        
        // Couleur des weekends dans les exports
        'weekend_color' => env('BUSINESS_WEEKEND_COLOR', 'lightgray'),
        
        // Couleur des jours fériés dans les exports
        'holiday_color' => env('BUSINESS_HOLIDAY_COLOR', 'lightblue'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres de performance
    |--------------------------------------------------------------------------
    */
    'performance' => [
        // TTL du cache pour les données de pointage (en secondes)
        'tracking_cache_ttl' => env('BUSINESS_TRACKING_CACHE_TTL', 1800), // 30 minutes
        
        // TTL du cache pour les coûts (en secondes)  
        'costs_cache_ttl' => env('BUSINESS_COSTS_CACHE_TTL', 7200), // 2 heures
        
        // TTL du cache pour les métriques (en secondes)
        'metrics_cache_ttl' => env('BUSINESS_METRICS_CACHE_TTL', 300), // 5 minutes
        
        // Taille maximale du cache (en MB)
        'max_cache_size' => env('BUSINESS_MAX_CACHE_SIZE', 100),
        
        // Pagination par défaut pour les API
        'default_pagination_size' => env('BUSINESS_DEFAULT_PAGINATION_SIZE', 50),
        
        // Pagination maximale pour les API
        'max_pagination_size' => env('BUSINESS_MAX_PAGINATION_SIZE', 500),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres de validation
    |--------------------------------------------------------------------------
    */
    'validation' => [
        // Validation stricte des données
        'strict_validation' => env('BUSINESS_STRICT_VALIDATION', true),
        
        // Validation des heures cohérentes (jour + nuit <= max)
        'validate_coherent_hours' => env('BUSINESS_VALIDATE_COHERENT_HOURS', true),
        
        // Validation des dates futures
        'allow_future_dates' => env('BUSINESS_ALLOW_FUTURE_DATES', false),
        
        // Validation des duplicatas
        'prevent_duplicates' => env('BUSINESS_PREVENT_DUPLICATES', true),
        
        // Tolérance pour les erreurs de calcul (en centimes)
        'calculation_tolerance' => env('BUSINESS_CALCULATION_TOLERANCE', 0.01),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres de sécurité
    |--------------------------------------------------------------------------
    */
    'security' => [
        // Masquer les informations sensibles dans les logs
        'hide_sensitive_data' => env('BUSINESS_HIDE_SENSITIVE_DATA', true),
        
        // Audit trail pour les modifications
        'enable_audit_trail' => env('BUSINESS_ENABLE_AUDIT_TRAIL', true),
        
        // Chiffrement des données sensibles
        'encrypt_sensitive_data' => env('BUSINESS_ENCRYPT_SENSITIVE_DATA', false),
        
        // Timeout de session (en minutes)
        'session_timeout' => env('BUSINESS_SESSION_TIMEOUT', 60),
        
        // Nombre maximum de tentatives de connexion
        'max_login_attempts' => env('BUSINESS_MAX_LOGIN_ATTEMPTS', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres de notification
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        // Notifications par email activées
        'email_notifications' => env('BUSINESS_EMAIL_NOTIFICATIONS', true),
        
        // Notifications pour les erreurs
        'error_notifications' => env('BUSINESS_ERROR_NOTIFICATIONS', true),
        
        // Notifications pour les seuils dépassés
        'threshold_notifications' => env('BUSINESS_THRESHOLD_NOTIFICATIONS', true),
        
        // Email de l'administrateur
        'admin_email' => env('BUSINESS_ADMIN_EMAIL', 'admin@example.com'),
        
        // Fréquence des rapports automatiques
        'report_frequency' => env('BUSINESS_REPORT_FREQUENCY', 'weekly'), // daily, weekly, monthly
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres de développement
    |--------------------------------------------------------------------------
    */
    'development' => [
        // Mode debug pour les calculs
        'debug_calculations' => env('BUSINESS_DEBUG_CALCULATIONS', false),
        
        // Logging détaillé
        'detailed_logging' => env('BUSINESS_DETAILED_LOGGING', false),
        
        // Profiling des performances
        'enable_profiling' => env('BUSINESS_ENABLE_PROFILING', false),
        
        // Données de test
        'use_test_data' => env('BUSINESS_USE_TEST_DATA', false),
        
        // Environnement de démonstration
        'demo_mode' => env('BUSINESS_DEMO_MODE', false),
    ],

];