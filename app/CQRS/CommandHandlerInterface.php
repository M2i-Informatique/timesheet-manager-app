<?php

namespace App\CQRS;

/**
 * Interface pour tous les handlers de commandes
 * 
 * Un handler execute une commande et retourne un résultat.
 */
interface CommandHandlerInterface
{
    /**
     * Exécute une commande
     * 
     * @param CommandInterface $command
     * @return mixed
     */
    public function handle(CommandInterface $command): mixed;
}