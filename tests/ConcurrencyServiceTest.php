<?php

namespace LaravelConcurrency\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use LaravelConcurrency\ConcurrencyService;
use LaravelConcurrency\Facades\Concurrency;
use LaravelConcurrency\Models\Task;
use LaravelConcurrency\Repositories\TaskRepository;
use LaravelConcurrency\Tests\Helpers\TestTask;
use Mockery\MockInterface;

class ConcurrencyServiceTest extends TestCase
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
    public function testProcessesTasksSynchronously(): void
    {
        $firstExpectedValue  = $this->faker->sentence;
        $secondExpectedValue = $this->faker->rgbColorAsArray;

        $firstTask  = new TestTask($firstExpectedValue);
        $secondTask = new TestTask($secondExpectedValue);

        self::assertEquals(
            [$firstExpectedValue, $secondExpectedValue],
            $this->buildConcurrencyService()->wait($firstTask, $secondTask)
        );
    }

    /**
     * @return void
     */
    public function testProcessesTasksAsynchronously(): void
    {
        $firstExpectedValue  = $this->faker->sentence;
        $secondExpectedValue = $this->faker->rgbColorAsArray;

        $taskModels = [
            $this->fakeCompletedTaskModel($firstExpectedValue),
            $this->fakeCompletedTaskModel($secondExpectedValue)
        ];

        $this->mock(
            TaskRepository::class,
            function (MockInterface $mock) use ($taskModels) {
                $mock->shouldReceive('create')->once()->andReturn($taskModels);

                $mock->shouldReceive('refresh')->once()->andReturn(
                    collect($taskModels)
                );

                $mock->shouldReceive('wereTasksProcessed')->once()->andReturnTrue();
            }
        );

        self::assertEquals(
            [$firstExpectedValue, $secondExpectedValue],
            $this->buildConcurrencyService()->wait()
        );
    }

    /**
     * @return void
     */
    public function testFacadeCallsConcurrencyService(): void
    {
        $this->mock(
            ConcurrencyService::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('wait')->once();
            }
        );

        Concurrency::wait();
    }

    /**
     * @return ConcurrencyService
     */
    private function buildConcurrencyService(): mixed
    {
        return app(ConcurrencyService::class);
    }

    /**
     * @param mixed $result
     * @return Task
     */
    private function fakeCompletedTaskModel(mixed $result): Task
    {
        return Task::create([
            'id'      => Str::orderedUuid()->toString(),
            'payload' => '',
            'result'  => serialize($result),
        ]);
    }
}
