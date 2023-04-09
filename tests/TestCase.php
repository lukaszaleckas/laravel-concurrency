<?php

namespace LaravelConcurrency\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelConcurrency\ConcurrencyServiceProvider;
use LaravelConcurrency\Configuration;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    /**
     * @param mixed $app
     * @return void
     */
    protected function defineEnvironment(mixed $app): void
    {
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('cache.default', 'array');

        $app['config']->set(Configuration::CONFIG, [
            Configuration::MAX_WAIT_MILLISECONDS => 10,
            Configuration::POOL_MILLISECONDS     => 10,
            Configuration::LOCK_NAME             => 'test',
            Configuration::LOCK_TIMEOUT_SECONDS  => 10,
        ]);
    }

    /**
     * @param mixed $app
     * @return array
     */
    protected function getPackageProviders(mixed $app): array
    {
        return [
            ConcurrencyServiceProvider::class
        ];
    }
}
