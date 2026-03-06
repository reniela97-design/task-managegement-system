<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function broadcastOn(): Channel
    {
        // Broadcast on a public channel named 'tasks'
        return new Channel('tasks');
    }

    public function broadcastWith(): array
    {
        // Only send the necessary data to the frontend
        return [
            'id' => $this->task->task_id,
            'status_id' => $this->task->task_status_id,
        ];
    }
}