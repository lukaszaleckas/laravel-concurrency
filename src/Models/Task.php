<?php

namespace LaravelConcurrency\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use LaravelConcurrency\Contracts\TaskInterface;

/**
 * @property string $id
 * @property string $payload
 * @property string $result
 * @property bool $is_locked
 * @property bool $is_processed
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Task extends Model
{
    /** @var bool */
    public $incrementing = false;

    /** @var string[] */
    protected $guarded = [];

    /** @var array<string, string> */
    protected $casts = [
        'is_locked'    => 'boolean',
        'is_processed' => 'boolean',
    ];

    /**
     * @return TaskInterface
     */
    public function getOriginalTask(): TaskInterface
    {
        return unserialize($this->payload);
    }

    /**
     * @return mixed
     */
    public function getOriginalResult(): mixed
    {
        return unserialize($this->result);
    }
}
