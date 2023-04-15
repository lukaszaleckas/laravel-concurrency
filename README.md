# Laravel Concurrency

## Installation

1. Run:

```
composer require lukaszaleckas/laravel-concurrency
```

Service provider should be automatically registered, if not add

```php
LaravelConcurrency\ConcurrencyServiceProvider::class
```

to your application's `app.php`.

2. Publish configuration file:

```
php artisan vendor:publish --provider="LaravelConcurrency\ConcurrencyServiceProvider"
```

3. Run migrations to create `tasks` table:

```
php artisan migrate
```

## Usage Example

### Create a task

Create a new task which implements `LaravelConcurrency\Contracts\TaskInterface`:

```php
use LaravelConcurrency\Contracts\TaskInterface;

class SleepForTwoSeconds implements TaskInterface
{
    public function run(): mixed
    {
        sleep(2);
        
        return null;
    }
}
```

Tasks return result which you want to get after running them.
This can be anything you want, but it needs to be serializable.

In this example we just return `null`.

### Spawn task workers

Tasks are run by tasks workers. To spawn a task worker run:

```
php artisan concurrency:run-task-worker
```

One worker can run only one task at a time. So we'll spawn two of those
for this example.

### Run tasks

Now run a couple of these tasks concurrently:

```php
use LaravelConcurrency\Facades\Concurrency;

Concurrency::wait(
    new SleepForTwoSeconds(),
    new SleepForTwoSeconds()
)
```

If you notice, these tasks were both completed in ~2s,
which means they've run concurrently.

You should also notice task workers logs that indicate which task
ran in which process.