<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth; // 1. Add this import

class TaskAssigned extends Notification
{
    use Queueable;

    public $task;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     * We'll use 'database' for in-app alerts. You can add 'mail' if configured.
     */
    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Add 'broadcast' to the array
        return ['database', 'broadcast']; 
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->task_id,
            'title' => 'New Task Assigned',
            'message' => 'You have been assigned to task: ' . $this->task->task_title,
            'url' => route('tasks.show', $this->task->task_id),
            
            // 2. Updated line: Use Facade and Safe Navigation (?->)
            'assigned_by' => Auth::user()?->user_name ?? 'System',
        ];
    }
}