<?php

namespace LaravelConcurrency;

use LaravelConcurrency\Contracts\TaskInterface;
use LaravelConcurrency\Models\Task;
use LaravelConcurrency\Repositories\TaskRepository;

class ConcurrencyService
{
    /**
     * @param TaskRepository $taskRepository
     */
    public function __construct(private TaskRepository $taskRepository)
    {
    }

    /**
     * @param TaskInterface ...$tasks
     * @return array
     */
    public function wait(TaskInterface ...$tasks): array
    {
        $tasks = $this->taskRepository->create(...$tasks);

        while (!$this->taskRepository->wereTasksProcessed($tasks)) {
            usleep(20000);
        }

        $tasks = $this->taskRepository->refresh(true, ...$tasks);

        return array_map(
            fn(Task $task) => $task->result,
            $tasks->toArray()
        );
    }
}
