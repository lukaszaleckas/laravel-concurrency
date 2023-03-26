<?php

namespace LaravelConcurrency;

use Illuminate\Support\ServiceProvider;
use LaravelMysqlQueue\MysqlQueueConnector;

class ConcurrencyServiceProvider extends ServiceProvider
{
    public const CONNECTOR = 'mysql';

    /**
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }

//    /**
//     * @return void
//     */
//    private function publishConfig(): void
//    {
//        $this->publishes(
//            [
//                __DIR__ . '/Config/concurrency.php' => config_path('concurrency.php'),
//            ],
//            'concurrency'
//        );
//    }
}