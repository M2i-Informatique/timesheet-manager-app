<?php

namespace App\CQRS;

/**
 * Interface pour toutes les queries CQRS
 * 
 * Les queries représentent les actions qui lisent l'état du système.
 * Elles sont utilisées pour les opérations READ/SELECT.
 */
interface QueryInterface
{
    /**
     * Validation des paramètres de la query
     * 
     * @return bool
     */
    public function validate(): bool;

    /**
     * Obtenir les paramètres de la query
     * 
     * @return array
     */
    public function getParameters(): array;

    /**
     * Obtenir l'identifiant unique de la query
     * 
     * @return string
     */
    public function getId(): string;
}