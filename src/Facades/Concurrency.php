<?php

namespace LaravelConcurrency\Facades;

use Illuminate\Support\Facades\Facade;
use LaravelConcurrency\ConcurrencyService;
use LaravelConcurrency\Contracts\TaskInterface;

/**
 * @method static array wait(TaskInterface ...$tasks)
 */
class Concurrency extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ConcurrencyService::class;
    }
}
