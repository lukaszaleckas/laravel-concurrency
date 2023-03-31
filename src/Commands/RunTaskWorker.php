<?php

namespace LaravelConcurrency\Commands;

use Illuminate\Console\Command;
use LaravelConcurrency\Exceptions\LockServiceException;
use LaravelConcurrency\Models\Task;
use LaravelConcurrency\Repositories\TaskRepository;

class RunTaskWorker extends Command
{
    /** @var string */
    protected $signature = 'concurrency:run-task-worker {--sleep=20} {--once}';

    /** @var string */
    protected $description = 'Run task worker process.';

    /** @var bool */
    private bool $shouldQuit;

    /**
     * @param TaskRepository $taskRepository
     */
    public function __construct(private readonly TaskRepository $taskRepository)
    {
        parent::__construct();

        $this->shouldQuit = false;

        $this->listenForSignals();
    }

    /**
     * @return void
     * @throws LockServiceException
     */
    public function handle(): void
    {
        while (!$this->shouldQuit) {
            if ($this->shouldRunOnlyOnce()) {
                $this->shouldQuit = true;
            }

            $task = $this->taskRepository->pop();

            if ($task === null) {
                $this->sleep();

                continue;
            }

            $this->processTask($task);
        }
    }

    /**
     * @param Task $task
     * @return void
     */
    private function processTask(Task $task): void
    {
        $this->info('Processing: ' . $task->id);

        $this->taskRepository->complete(
            $task,
            $task->getOriginalTask()->run()
        );

        $this->info('Processed: ' . $task->id);
    }

    /**
     * @return bool
     */
    private function shouldRunOnlyOnce(): bool
    {
        return $this->option('once');
    }

    /**
     * @return void
     */
    public function sleep(): void
    {
        usleep(
            (int)$this->option('sleep') * 1000
        );
    }

    /**
     * @return void
     */
    private function listenForSignals(): void
    {
        pcntl_async_signals(true);

        pcntl_signal(SIGQUIT, function () {
            $this->shouldQuit = true;
        });

        pcntl_signal(SIGTERM, function () {
            $this->shouldQuit = true;
        });
    }
}
