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
     * sur la table time_sheetables
     */
    public function up(): void
    {
        Schema::table('time_sheetables', function (Blueprint $table) {
            // Index composites pour les requêtes les plus fréquentes
            
            // 1. Index pour les requêtes par projet et date
            // Utilisé dans: TrackingService::buildEntriesData() et buildRecapData()
            $table->index(['project_id', 'date'], 'idx_time_sheetables_project_date');
            
            // 2. Index pour les requêtes par date et catégorie
            // Utilisé pour filtrer jour/nuit rapidement
            $table->index(['date', 'category'], 'idx_time_sheetables_date_category');
            
            // 3. Index pour les requêtes polymorphiques
            // Utilisé pour identifier workers vs interims
            $table->index(['timesheetable_type', 'timesheetable_id'], 'idx_time_sheetables_polymorphic');
            
            // 4. Index composite pour les requêtes complexes de calcul
            // Utilisé dans les calculs de coûts et heures
            $table->index(['project_id', 'date', 'category'], 'idx_time_sheetables_project_date_category');
            
            // 5. Index pour les requêtes par employé et projet
            // Utilisé pour récupérer les heures d'un employé sur un projet
            $table->index(['timesheetable_id', 'timesheetable_type', 'project_id'], 'idx_time_sheetables_employee_project');
            
            // 6. Index pour les requêtes par mois/année
            // Utilise des expressions pour optimiser les requêtes YEAR() et MONTH()
            // Note: Laravel ne supporte pas les index sur expressions directement
            // Ces index seront créés via raw SQL
        });
        
        // Index sur expressions pour optimiser YEAR() et MONTH()
        DB::statement('CREATE INDEX idx_time_sheetables_year_month ON time_sheetables (EXTRACT(YEAR FROM date), EXTRACT(MONTH FROM date))');
        DB::statement('CREATE INDEX idx_time_sheetables_year_month_category ON time_sheetables (EXTRACT(YEAR FROM date), EXTRACT(MONTH FROM date), category)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_sheetables', function (Blueprint $table) {
            // Supprimer les index composites
            $table->dropIndex('idx_time_sheetables_project_date');
            $table->dropIndex('idx_time_sheetables_date_category');
            $table->dropIndex('idx_time_sheetables_polymorphic');
            $table->dropIndex('idx_time_sheetables_project_date_category');
            $table->dropIndex('idx_time_sheetables_employee_project');
        });
        
        // Supprimer les index sur expressions
        DB::statement('DROP INDEX IF EXISTS idx_time_sheetables_year_month');
        DB::statement('DROP INDEX IF EXISTS idx_time_sheetables_year_month_category');
    }
};