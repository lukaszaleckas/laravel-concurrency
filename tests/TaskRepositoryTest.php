<?php

namespace LaravelConcurrency\Tests;

use LaravelConcurrency\Repositories\TaskRepository;
use LaravelConcurrency\Tests\Helpers\TestTask;

class TaskRepositoryTest extends TestCase
{
    /**
     * @var TaskRepository
     */
    private readonly TaskRepository $taskRepository;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->taskRepository = app(TaskRepository::class);
    }

    /**
     * @return void
     */
    public function testCanRefreshAndDeleteTasks(): void
    {
        $task = $this->taskRepository->create(
            new TestTask('')
        )[0];

        $this->taskRepository->refresh(true, $task);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
