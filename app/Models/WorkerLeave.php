<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class WorkerLeave extends Model
{
    protected $fillable = [
        'worker_id',
        'type',
        'start_date',
        'end_date',
        'days_count',
        'reason',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_count' => 'integer'
    ];

    /**
     * Types de congés disponibles avec leurs libellés
     */
    public static function getTypes(): array
    {
        return [
            'conge_paye' => 'Congés payés',
            'rtt' => 'RTT',
            'conge_sans_solde' => 'Congé sans solde',
            'arret_maladie_accident' => 'Arrêt maladie - Accident de travail',
            'attestation_isolation' => 'Attestation d\'isolation',
            'conge_paternite_maternite' => 'Congé paternité/maternité',
            'activite_partielle' => 'Activité partielle',
            'intemperies' => 'Intempéries',
            'autre' => 'Autre'
        ];
    }

    /**
     * Codes courts pour l'affichage dans les tableaux
     */
    public static function getTypeCodes(): array
    {
        return [
            'conge_paye' => 'CP',
            'rtt' => 'RTT',
            'conge_sans_solde' => 'CSP',
            'arret_maladie_accident' => 'AM',
            'attestation_isolation' => 'AI',
            'conge_paternite_maternite' => 'PM',
            'activite_partielle' => 'AP',
            'intemperies' => 'INT',
            'autre' => 'AUT'
        ];
    }

    /**
     * Couleurs pour les exports Excel
     */
    public static function getTypeColors(): array
    {
        return [
            'conge_paye' => '#92D050',               // Vert
            'rtt' => '#00B0F0',                     // Bleu ciel
            'conge_sans_solde' => '#FFC000',        // Jaune/Orange
            'arret_maladie_accident' => '#FF0000',  // Rouge
            'attestation_isolation' => '#FF0000',   // Rouge
            'conge_paternite_maternite' => '#FFCCFF', // Rose
            'activite_partielle' => '#FF66FF',      // Magenta
            'intemperies' => '#99FF66',             // Vert lime
            'autre' => '#9370DB'                    // Violet
        ];
    }

    /**
     * Relation avec le worker
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    /**
     * Relation avec l'utilisateur qui a créé le congé
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accesseur pour le libellé du type
     */
    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    /**
     * Accesseur pour le code court du type
     */
    public function getTypeCodeAttribute(): string
    {
        return self::getTypeCodes()[$this->type] ?? $this->type;
    }

    /**
     * Accesseur pour la couleur du type
     */
    public function getTypeColorAttribute(): string
    {
        return self::getTypeColors()[$this->type] ?? '#CCCCCC';
    }

    /**
     * Calcule automatiquement le nombre de jours ouvrés
     */
    public function calculateDaysCount(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $days = 0;

        // Compter uniquement les jours ouvrés (lundi à vendredi)
        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $days++;
            }
            $start->addDay();
        }

        return $days;
    }

    /**
     * Boot method pour calculer automatiquement days_count
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($leave) {
            $leave->days_count = $leave->calculateDaysCount();
        });
    }

    /**
     * Scope pour filtrer par worker
     */
    public function scopeForWorker($query, $workerId)
    {
        return $query->where('worker_id', $workerId);
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    /**
     * Scope pour une date spécifique
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date);
    }
}