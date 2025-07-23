<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pour PostgreSQL, on doit modifier la contrainte check
        DB::statement("
            ALTER TABLE worker_leaves DROP CONSTRAINT worker_leaves_type_check;
        ");
        
        DB::statement("
            ALTER TABLE worker_leaves ADD CONSTRAINT worker_leaves_type_check 
            CHECK (type IN (
                'conge_paye',
                'rtt', 
                'conge_sans_solde',
                'arret_maladie_accident',
                'attestation_isolation',
                'conge_paternite_maternite',
                'activite_partielle',
                'intemperies',
                'autre'
            ))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // PostgreSQL ne permet pas de supprimer des valeurs d'un enum facilement
        // Il faudrait recréer l'enum complet, mais on laisse comme ça car c'est destructif
    }
};