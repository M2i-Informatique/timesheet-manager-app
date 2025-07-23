<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('worker_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained('workers')->onDelete('cascade');
            $table->enum('type', [
                'conge_paye',
                'rtt', 
                'conge_sans_solde',
                'arret_maladie_accident',
                'attestation_isolation',
                'conge_paternite_maternite',
                'activite_partielle',
                'intemperies'
            ]);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_count')->default(0);
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index(['worker_id', 'start_date', 'end_date']);
            $table->index(['type', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_leaves');
    }
};