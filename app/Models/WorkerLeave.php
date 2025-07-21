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
            'intemperies' => 'Intempéries'
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
            'intemperies' => 'INT'
        ];
    }

    /**
     * Couleurs pour les exports Excel
     */
    public static function getTypeColors(): array
    {
        return [
            'conge_paye' => '#006400',           // Vert foncé
            'rtt' => '#0066CC',                 // Bleu
            'conge_sans_solde' => '#FF8C00',    // Orange
            'arret_maladie_accident' => '#DC143C', // Rouge
            'attestation_isolation' => '#DC143C',   // Rouge
            'conge_paternite_maternite' => '#FFB6C1', // Rose
            'activite_partielle' => '#90EE90',      // Vert clair
            'intemperies' => '#90EE90'              // Vert clair
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