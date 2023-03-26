<?php

namespace LaravelConcurrency\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LaravelConcurrency\Exceptions\LockServiceException;

class LockRepository
{
    /**
     * @param string $name
     * @param int    $timeout
     * @return void
     * @throws LockServiceException
     */
    public function acquireLock(string $name, int $timeout = 10): void
    {
        $result = $this->getLockResult(
            DB::query()->selectRaw(
                'GET_LOCK(:name, :timeout)',
                [
                    'name'    => $name,
                    'timeout' => $timeout
                ]
            )->get()
        );

        if ($result === false) {
            throw new LockServiceException("Failed acquiring $name lock");
        }
    }

    /**
     * @param string $name
     * @return void
     * @throws LockServiceException
     */
    public function releaseLock(string $name): void
    {
        $result = $this->getLockResult(
            DB::query()->selectRaw("RELEASE_LOCK(?)", [$name])->get()
        );

        if ($result === false) {
            throw new LockServiceException("Failed releasing $name lock");
        }
    }
    
    /**
     * @param Collection $result
     * @return bool
     */
    private function getLockResult(Collection $result): bool
    {
        return (bool)Arr::first(
            Arr::first($result)
        );
    }
}
