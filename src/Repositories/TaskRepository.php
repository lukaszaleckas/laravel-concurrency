<?php

namespace LaravelConcurrency\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelConcurrency\Contracts\TaskInterface;
use LaravelConcurrency\Exceptions\LockServiceException;
use LaravelConcurrency\Models\Task;

class TaskRepository
{
    /**
     * @param LockRepository $lockRepository
     */
    public function __construct(private LockRepository $lockRepository)
    {
    }

    /**
     * @param TaskInterface ...$tasks
     * @return Task[]
     */
    public function create(TaskInterface ...$tasks): array
    {
        $result = [];

        foreach ($tasks as $task) {
            $result[] = Task::create([
                'id'      => Str::orderedUuid()->toString(),
                'payload' => serialize($task)
            ]);
        }

        return $result;
    }

    /**
     * @return Task|null
     * @throws LockServiceException
     */
    public function pop(): ?Task
    {
        $this->lockRepository->acquireLock('async');

        /** @var Task|null $task */
        $task = Task::query()
            ->where('is_locked', false)
            ->where('is_processed', false)
            ->first();

        $task?->update([
            'is_locked' => true
        ]);

        $this->lockRepository->releaseLock('async');

        return $task;
    }

    /**
     * @param Task  $task
     * @param mixed $result
     * @return Task
     */
    public function complete(Task $task, mixed $result): Task
    {
        $task->update([
            'result'       => $result,
            'is_locked'    => false,
            'is_processed' => true
        ]);

        return $task;
    }

    /**
     * @param bool $delete
     * @param Task ...$tasks
     * @return Collection
     */
    public function refresh(bool $delete, Task ...$tasks): Collection
    {
        $ids = array_map(
            fn(Task $task) => $task->id,
            $tasks
        );

        $query = Task::query()->whereIn('id', $ids);

        $result = $query->get();

        if ($delete) {
            $query->delete();
        }

        return $result;
    }

    /**
     * @param Task[] $tasks
     * @return bool
     */
    public function wereTasksProcessed(array $tasks): bool
    {
        return $this->refresh(false, ...$tasks)
            ->where('is_processed', false)
            ->count() === 0;
    }
}
