<?php

namespace LaravelConcurrency\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use LaravelConcurrency\Repositories\TaskRepository;
use LaravelConcurrency\Tests\Helpers\TestTask;
use Mockery\MockInterface;

class RunTaskWorkerTest extends TestCase
{
    use WithFaker;

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
    public function testSleepsIfNoTaskIsAvailable(): void
    {
        $this->mock(
            TaskRepository::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('pop')->once()->andReturnNull();
            }
        );

        $this->artisan('concurrency:run-task-worker --once');
    }
    /**
     * @return void
     */
    public function testProcessesTask(): void
    {
        $expectedResult = $this->faker->rgbColorAsArray;

        $taskModel = $this->taskRepository->create(
            new TestTask($expectedResult)
        )[0];

        $this->artisan('concurrency:run-task-worker --once');

        self::assertEquals(
            $expectedResult,
            $taskModel->refresh()->getOriginalResult()
        );
    }
}
