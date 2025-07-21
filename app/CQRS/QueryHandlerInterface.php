<?php

namespace App\CQRS;

/**
 * Interface pour tous les handlers de queries
 * 
 * Un handler execute une query et retourne un résultat.
 */
interface QueryHandlerInterface
{
    /**
     * Exécute une query
     * 
     * @param QueryInterface $query
     * @return mixed
     */
    public function handle(QueryInterface $query): mixed;
}