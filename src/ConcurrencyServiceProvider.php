<?php

namespace LaravelConcurrency;

use Illuminate\Support\ServiceProvider;
use LaravelConcurrency\Commands\RunTaskWorker;

class ConcurrencyServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrations();

        $this->registerCommands();

        $this->publishConfig();
    }

    /**
     * @return void
     */
    private function publishConfig(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../config/concurrency.php' => config_path('concurrency.php'),
            ],
            'concurrency'
        );
    }

    /**
     * @return void
     */
    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * @return void
     */
    private function registerCommands(): void
    {
        $this->commands([
            RunTaskWorker::class
        ]);
    }
}
