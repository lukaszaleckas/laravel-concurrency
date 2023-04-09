<?php

namespace LaravelConcurrency;

use LaravelConcurrency\Contracts\TaskInterface;
use LaravelConcurrency\Exceptions\TimeoutException;
use LaravelConcurrency\Models\Task;
use LaravelConcurrency\Repositories\TaskRepository;

class ConcurrencyService
{
    /**
     * @var int
     */
    private int $timeElapsed;

    /**
     * @param TaskRepository $taskRepository
     */
    public function __construct(private TaskRepository $taskRepository)
    {
        $this->resetTimeElapsed();
    }

    /**
     * @param TaskInterface ...$tasks
     * @return array
     */
    public function wait(TaskInterface ...$tasks): array
    {
        $this->resetTimeElapsed();

        $taskModels = $this->createTasks($tasks);

        try {
            $this->waitUntilProcessedOrFail($taskModels);
        } catch (TimeoutException $exception) {
            return $this->runSynchronouslyAndGetResult(...$tasks);
        }

        return $this->getResult(...$taskModels);
    }

    /**
     * @param Task ...$tasks
     * @return array
     */
    private function getResult(Task ...$tasks): array
    {
        $tasks = $this->taskRepository->refresh(true, ...$tasks);

        return array_map(
            fn(Task $task) => $task->getOriginalResult(),
            $tasks->all()
        );
    }

    /**
     * @param TaskInterface ...$tasks
     * @return array
     */
    private function runSynchronouslyAndGetResult(TaskInterface ...$tasks): array
    {
        return array_map(
            fn(TaskInterface $task) => $task->run(),
            $tasks
        );
    }

    /**
     * @param TaskInterface[] $tasks
     * @return Task[]
     */
    private function createTasks(array $tasks): array
    {
        return $this->taskRepository->create(...$tasks);
    }

    /**
     * @param Task[] $tasks
     * @return void
     * @throws TimeoutException
     */
    private function waitUntilProcessedOrFail(array $tasks): void
    {
        while (!$this->taskRepository->wereTasksProcessed($tasks)) {
            $this->sleepOrTimeout();
        }
    }

    /**
     * @return void
     * @throws TimeoutException
     */
    private function sleepOrTimeout(): void
    {
        if ($this->timeElapsed > Configuration::getMaxWaitMilliseconds()) {
            throw new TimeoutException();
        }

        usleep(
            Configuration::getPoolMilliseconds() * 1000
        );

        $this->pooled();
    }

    /**
     * @return void
     */
    private function resetTimeElapsed(): void
    {
        $this->timeElapsed = 0;
    }

    /**
     * @return void
     */
    private function pooled(): void
    {
        $this->timeElapsed += Configuration::getPoolMilliseconds();
    }
}
