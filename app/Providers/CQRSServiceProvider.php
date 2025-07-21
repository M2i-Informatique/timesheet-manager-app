<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\CQRS\CommandBus;
use App\CQRS\QueryBus;
use App\CQRS\Commands\SaveTimesheetCommand;
use App\CQRS\Commands\AssignEmployeeCommand;
use App\CQRS\Queries\GetTrackingDataQuery;
use App\CQRS\Queries\GetProjectCostsQuery;
use App\CQRS\Handlers\SaveTimesheetHandler;
use App\CQRS\Handlers\AssignEmployeeHandler;
use App\CQRS\Handlers\GetTrackingDataHandler;
use App\CQRS\Handlers\GetProjectCostsHandler;

class CQRSServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Enregistrement des bus
        $this->app->singleton(CommandBus::class, function ($app) {
            return new CommandBus($app);
        });

        $this->app->singleton(QueryBus::class, function ($app) {
            return new QueryBus($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configuration des mappings Command -> Handler
        $commandBus = $this->app->make(CommandBus::class);
        $commandBus->register(SaveTimesheetCommand::class, SaveTimesheetHandler::class);
        $commandBus->register(AssignEmployeeCommand::class, AssignEmployeeHandler::class);

        // Configuration des mappings Query -> Handler
        $queryBus = $this->app->make(QueryBus::class);
        $queryBus->register(GetTrackingDataQuery::class, GetTrackingDataHandler::class);
        $queryBus->register(GetProjectCostsQuery::class, GetProjectCostsHandler::class);
    }
}