<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestAllServices extends Command
{
    protected $signature = 'test:services';
    protected $description = 'Test all services can be instantiated';

    public function handle(): int
    {
        $this->info('Testing all services instantiation...');
        
        $services = [
            'BusinessConfigService' => \App\Services\Config\BusinessConfigService::class,
            'CacheService' => \App\Services\Cache\CacheService::class,
            'MetricsService' => \App\Services\Monitoring\MetricsService::class,
            'CommandBus' => \App\CQRS\CommandBus::class,
            'QueryBus' => \App\CQRS\QueryBus::class,
            'CostCalculator' => \App\Services\Costs\CostCalculatorInterface::class,
            'TrackingService' => \App\Services\Tracking\TrackingServiceInterface::class,
            'WorkerSalaryService' => \App\Services\Salary\WorkerSalaryServiceInterface::class,
        ];

        $errors = [];

        foreach ($services as $name => $class) {
            try {
                $this->info("Testing {$name}...");
                $service = app($class);
                $this->info("✓ {$name}: " . get_class($service));
            } catch (\Exception $e) {
                $errors[] = "{$name}: {$e->getMessage()}";
                $this->error("✗ {$name}: {$e->getMessage()}");
            }
        }

        if (empty($errors)) {
            $this->info("\n🎉 All services instantiated successfully!");
            return Command::SUCCESS;
        } else {
            $this->error("\n❌ Errors found:");
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
            return Command::FAILURE;
        }
    }
}