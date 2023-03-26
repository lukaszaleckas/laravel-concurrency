<?php

namespace LaravelConcurrency\Contracts;

interface TaskInterface
{
    /**
     * @return mixed
     */
    public function run(): mixed;
}
