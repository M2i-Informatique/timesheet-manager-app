<?php

namespace App\CQRS;

use Illuminate\Container\Container;
use InvalidArgumentException;

/**
 * Bus pour dispatcher les commandes vers leurs handlers
 */
class CommandBus
{
    private Container $container;
    private array $handlers = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Enregistrer un handler pour une commande
     * 
     * @param string $commandClass
     * @param string $handlerClass
     * @return void
     */
    public function register(string $commandClass, string $handlerClass): void
    {
        $this->handlers[$commandClass] = $handlerClass;
    }

    /**
     * Dispatcher une commande vers son handler
     * 
     * @param CommandInterface $command
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function dispatch(CommandInterface $command): mixed
    {
        $commandClass = get_class($command);
        
        if (!isset($this->handlers[$commandClass])) {
            throw new InvalidArgumentException("No handler registered for command: {$commandClass}");
        }

        if (!$command->validate()) {
            throw new InvalidArgumentException("Invalid command data for: {$commandClass}");
        }

        $handlerClass = $this->handlers[$commandClass];
        $handler = $this->container->make($handlerClass);
        
        if (!$handler instanceof CommandHandlerInterface) {
            throw new InvalidArgumentException("Handler must implement CommandHandlerInterface: {$handlerClass}");
        }

        return $handler->handle($command);
    }

    /**
     * Obtenir tous les handlers enregistrés
     * 
     * @return array
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }
}