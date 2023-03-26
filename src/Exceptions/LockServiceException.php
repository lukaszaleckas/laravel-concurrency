<?php

namespace LaravelConcurrency\Exceptions;

use Exception;
use Throwable;

class LockServiceException extends Exception
{
    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message, int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct(
            "Lock service error: $message",
            $code,
            $previous
        );
    }
}
