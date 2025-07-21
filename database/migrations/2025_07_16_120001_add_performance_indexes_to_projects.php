<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Ajouter des index de performance pour optimiser les requêtes fréquentes
     * sur la table projects et tables liées
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Index pour les requêtes par status (projets actifs)
            // Utilisé dans: TrackingController::index() et ProjectRepository
            $table->index(['status'], 'idx_projects_status');
            
            // Index pour les requêtes par code (tri fréquent)
            // Utilisé pour l'affichage ordonné des projets
            $table->index(['code'], 'idx_projects_code');
            
            // Index composite pour les requêtes par status et code
            // Optimise: Project::where('status', 'active')->orderBy('code')
            $table->index(['status', 'code'], 'idx_projects_status_code');
            
            // Index pour les requêtes par catégorie
            // Utilisé dans les rapports par catégorie (mh/go/other)
            $table->index(['category'], 'idx_projects_category');
            
            // Index pour les requêtes par zone
            // Utilisé dans les calculs de coûts avec compensation zone
            $table->index(['zone_id'], 'idx_projects_zone');
        });
        
        Schema::table('workers', function (Blueprint $table) {
            // Index pour les requêtes par status (workers actifs)
            // Utilisé dans: ProjectRepository::findAvailableWorkers()
            $table->index(['status'], 'idx_workers_status');
            
            // Index pour les requêtes par nom (tri fréquent)
            // Utilisé pour l'affichage ordonné des workers
            $table->index(['last_name', 'first_name'], 'idx_workers_name');
            
            // Index composite pour les requêtes par status et nom
            // Optimise: Worker::where('status', 'active')->orderBy('last_name', 'first_name')
            $table->index(['status', 'last_name', 'first_name'], 'idx_workers_status_name');
            
            // Index pour les requêtes par catégorie (worker/etam)
            // Utilisé dans les calculs de coûts (ETAM vs worker)
            $table->index(['category'], 'idx_workers_category');
        });
        
        Schema::table('interims', function (Blueprint $table) {
            // Index pour les requêtes par status (interims actifs)
            // Utilisé dans: ProjectRepository::findAvailableInterims()
            $table->index(['status'], 'idx_interims_status');
            
            // Index pour les requêtes par agence
            // Utilisé pour les regroupements par agence
            $table->index(['agency'], 'idx_interims_agency');
        });
        
        Schema::table('projectables', function (Blueprint $table) {
            // Index pour les requêtes polymorphiques
            // Utilisé dans: Project::workers(), Project::interims()
            $table->index(['projectable_type', 'projectable_id'], 'idx_projectables_polymorphic');
            
            // Index pour les requêtes par projet
            // Utilisé pour récupérer les employés d'un projet
            $table->index(['project_id'], 'idx_projectables_project');
            
            // Index composite pour les requêtes complexes
            // Optimise les requêtes de disponibilité des employés
            $table->index(['project_id', 'projectable_type'], 'idx_projectables_project_type');
        });
        
        Schema::table('non_working_days', function (Blueprint $table) {
            // Index pour les requêtes par date
            // Utilisé dans: TrackingService::getNonWorkingDays()
            $table->index(['date'], 'idx_non_working_days_date');
            
            // Index pour les requêtes par mois/année
            // Utilise des expressions pour optimiser YEAR() et MONTH()
        });
        
        // Index sur expressions pour les non_working_days
        DB::statement('CREATE INDEX idx_non_working_days_year_month ON non_working_days (EXTRACT(YEAR FROM date), EXTRACT(MONTH FROM date))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_status');
            $table->dropIndex('idx_projects_code');
            $table->dropIndex('idx_projects_status_code');
            $table->dropIndex('idx_projects_category');
            $table->dropIndex('idx_projects_zone');
        });
        
        Schema::table('workers', function (Blueprint $table) {
            $table->dropIndex('idx_workers_status');
            $table->dropIndex('idx_workers_name');
            $table->dropIndex('idx_workers_status_name');
            $table->dropIndex('idx_workers_category');
        });
        
        Schema::table('interims', function (Blueprint $table) {
            $table->dropIndex('idx_interims_status');
            $table->dropIndex('idx_interims_agency');
        });
        
        Schema::table('projectables', function (Blueprint $table) {
            $table->dropIndex('idx_projectables_polymorphic');
            $table->dropIndex('idx_projectables_project');
            $table->dropIndex('idx_projectables_project_type');
        });
        
        Schema::table('non_working_days', function (Blueprint $table) {
            $table->dropIndex('idx_non_working_days_date');
        });
        
        // Supprimer les index sur expressions
        DB::statement('DROP INDEX IF EXISTS idx_non_working_days_year_month');
    }
};