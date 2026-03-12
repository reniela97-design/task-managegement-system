<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import the Task model
use App\Models\Task;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';
    protected $primaryKey = 'project_id';
    public $timestamps = false;

    const CREATED_AT = 'project_log_datetime';
    const UPDATED_AT = null;

    protected $fillable = [
        'project_name',
        'project_client_id',
        'project_branch',
        'project_address',
        'project_user_id',
        'project_inactive',
    ];
    
    /**
     * Get the tasks for the project.
     */
    public function tasks()
    {
        // Based on your controller logic, the foreign key in 'tasks' table 
        // is 'task_project_id'.
        return $this->hasMany(Task::class, 'task_project_id', 'project_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'project_client_id', 'client_id');
    }
}