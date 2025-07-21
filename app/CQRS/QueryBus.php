<?php

namespace App\CQRS;

use Illuminate\Container\Container;
use InvalidArgumentException;

/**
 * Bus pour dispatcher les queries vers leurs handlers
 */
class QueryBus
{
    private Container $container;
    private array $handlers = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Enregistrer un handler pour une query
     * 
     * @param string $queryClass
     * @param string $handlerClass
     * @return void
     */
    public function register(string $queryClass, string $handlerClass): void
    {
        $this->handlers[$queryClass] = $handlerClass;
    }

    /**
     * Dispatcher une query vers son handler
     * 
     * @param QueryInterface $query
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function dispatch(QueryInterface $query): mixed
    {
        $queryClass = get_class($query);
        
        if (!isset($this->handlers[$queryClass])) {
            throw new InvalidArgumentException("No handler registered for query: {$queryClass}");
        }

        if (!$query->validate()) {
            throw new InvalidArgumentException("Invalid query parameters for: {$queryClass}");
        }

        $handlerClass = $this->handlers[$queryClass];
        $handler = $this->container->make($handlerClass);
        
        if (!$handler instanceof QueryHandlerInterface) {
            throw new InvalidArgumentException("Handler must implement QueryHandlerInterface: {$handlerClass}");
        }

        return $handler->handle($query);
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