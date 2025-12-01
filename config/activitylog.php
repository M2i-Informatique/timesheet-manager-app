<?php

return [

    /*
     * Si défini à false, aucune activité ne sera sauvegardée en base de données.
     */
    'enabled' => env('ACTIVITY_LOGGER_ENABLED', true),

    /*
     * Lorsque la commande clean est exécutée, toutes les activités enregistrées plus anciennes que
     * le nombre de jours spécifié ici seront supprimées.
     */
    'delete_records_older_than_days' => 180,

    /*
     * Si aucun nom de log n'est passé à l'helper activity()
     * nous utilisons ce nom de log par défaut.
     */
    'default_log_name' => 'default',

    /*
     * Vous pouvez spécifier un driver d'authentification ici qui récupère les modèles utilisateur.
     * Si ceci est null, nous utiliserons le driver d'authentification Laravel actuel.
     */
    'default_auth_driver' => null,

    /*
     * Si défini à true, le sujet retourne les modèles supprimés en soft delete.
     */
    'subject_returns_soft_deleted_models' => false,

    /*
     * Ce modèle sera utilisé pour enregistrer l'activité.
     * Il doit implémenter l'interface Spatie\Activitylog\Contracts\Activity
     * et étendre Illuminate\Database\Eloquent\Model.
     */
    'activity_model' => \Spatie\Activitylog\Models\Activity::class,

    /*
     * C'est le nom de la table qui sera créée par la migration et
     * utilisée par le modèle Activity fourni avec ce package.
     */
    'table_name' => env('ACTIVITY_LOGGER_TABLE_NAME', 'activity_log'),

    /*
     * C'est la connexion de base de données qui sera utilisée par la migration et
     * le modèle Activity fourni avec ce package. Si elle n'est pas définie,
     * Laravel's database.default sera utilisé à la place.
     */
    'database_connection' => env('ACTIVITY_LOGGER_DB_CONNECTION'),
];
