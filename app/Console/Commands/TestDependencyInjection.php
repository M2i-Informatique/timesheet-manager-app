<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Costs\CostCalculatorInterface;
use App\Services\Salary\WorkerSalaryServiceInterface;
use App\Services\Config\BusinessConfigService;
use App\Services\Cache\CacheService;
use App\Services\Monitoring\MetricsService;
use App\CQRS\CommandBus;
use App\CQRS\QueryBus;

class TestDependencyInjection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:di';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test dependency injection setup';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Testing dependency injection setup...');

        try {
            // Test des services principaux
            $costCalculator = app(CostCalculatorInterface::class);
            $this->info('✓ CostCalculatorInterface resolved: ' . get_class($costCalculator));

            $salaryService = app(WorkerSalaryServiceInterface::class);
            $this->info('✓ WorkerSalaryServiceInterface resolved: ' . get_class($salaryService));

            $configService = app(BusinessConfigService::class);
            $this->info('✓ BusinessConfigService resolved: ' . get_class($configService));

            $cacheService = app(CacheService::class);
            $this->info('✓ CacheService resolved: ' . get_class($cacheService));

            $metricsService = app(MetricsService::class);
            $this->info('✓ MetricsService resolved: ' . get_class($metricsService));

            // Test des bus CQRS
            $commandBus = app(CommandBus::class);
            $this->info('✓ CommandBus resolved: ' . get_class($commandBus));

            $queryBus = app(QueryBus::class);
            $this->info('✓ QueryBus resolved: ' . get_class($queryBus));

            // Test de la configuration
            $chargeRate = $configService->getDefaultChargeRate();
            $this->info("✓ Default charge rate from config: {$chargeRate}%");

            $basketValue = $configService->getDefaultBasketValue();
            $this->info("✓ Default basket value from config: {$basketValue}");

            // Test du cache
            $cacheStats = $cacheService->getCacheStats();
            $this->info('✓ Cache driver: ' . $cacheStats['driver']);

            $this->info('');
            $this->info('🎉 All dependency injection tests passed!');
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Dependency injection test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            
            return Command::FAILURE;
        }
    }
}