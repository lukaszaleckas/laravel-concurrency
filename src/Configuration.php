<?php

namespace LaravelConcurrency;

use Illuminate\Support\Facades\Config;

class Configuration
{
    public const CONFIG = 'concurrency';

    public const POOL_MILLISECONDS     = 'pool_milliseconds';
    public const MAX_WAIT_MILLISECONDS = 'max_wait_milliseconds';

    public const LOCK_NAME            = 'lock_name';
    public const LOCK_TIMEOUT_SECONDS = 'lock_timeout';

    /**
     * @return array
     */
    public static function getConfiguration(): array
    {
        return Config::get(self::CONFIG);
    }

    /**
     * @return int
     */
    public static function getPoolMilliseconds(): int
    {
        return self::getConfiguration()[self::POOL_MILLISECONDS];
    }

    /**
     * @return int
     */
    public static function getMaxWaitMilliseconds(): int
    {
        return self::getConfiguration()[self::MAX_WAIT_MILLISECONDS];
    }

    /**
     * @return string
     */
    public static function getLockName(): string
    {
        return self::getConfiguration()[self::LOCK_NAME];
    }

    /**
     * @return int
     */
    public static function getLockTimeoutSeconds(): int
    {
        return self::getConfiguration()[self::LOCK_TIMEOUT_SECONDS];
    }
}
