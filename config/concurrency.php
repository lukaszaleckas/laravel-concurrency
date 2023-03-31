<?php

use LaravelConcurrency\Configuration;

return [
    Configuration::POOL_MILLISECONDS     => 20,
    Configuration::MAX_WAIT_MILLISECONDS => 2 * 1000,

    Configuration::LOCK_NAME             => 'laravel_concurrency',
    Configuration::LOCK_TIMEOUT_SECONDS  => 10
];
