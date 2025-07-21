<?php

namespace App\CQRS;

/**
 * Interface pour toutes les commandes CQRS
 * 
 * Les commandes représentent les actions qui modifient l'état du système.
 * Elles sont utilisées pour les opérations CREATE, UPDATE, DELETE.
 */
interface CommandInterface
{
    /**
     * Validation des données de la commande
     * 
     * @return bool
     */
    public function validate(): bool;

    /**
     * Obtenir les données de la commande
     * 
     * @return array
     */
    public function toArray(): array;

    /**
     * Obtenir l'identifiant unique de la commande
     * 
     * @return string
     */
    public function getId(): string;
}