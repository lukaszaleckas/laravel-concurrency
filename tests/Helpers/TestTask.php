<?php

namespace LaravelConcurrency\Tests\Helpers;

use LaravelConcurrency\Contracts\TaskInterface;

class TestTask implements TaskInterface
{
    /**
     * @param mixed $returnValue
     */
    public function __construct(private readonly mixed $returnValue)
    {
    }

    /**
     * @return mixed
     */
    public function run(): mixed
    {
        return $this->returnValue;
    }
}
