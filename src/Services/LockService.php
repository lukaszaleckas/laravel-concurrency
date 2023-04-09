<?php

namespace LaravelConcurrency\Services;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use LaravelConcurrency\Configuration;

class LockService
{
    /**
     * @param string   $lockName
     * @param callable $callback
     * @return mixed
     */
    public function block(string $lockName, callable $callback): mixed
    {
        return $this->buildLock($lockName)->block(
            Configuration::getLockTimeoutSeconds(),
            $callback
        );
    }

    /**
     * @param string $name
     * @return Lock
     */
    private function buildLock(string $name): Lock
    {
        return Cache::lock(
            $name,
            Configuration::getLockTimeoutSeconds()
        );
    }
}
