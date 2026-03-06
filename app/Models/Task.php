<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';
    protected $primaryKey = 'task_id';
    public $timestamps = false; // Using custom log datetime

    // Map creation time to your custom column
    const CREATED_AT = 'task_log_datetime';
    const UPDATED_AT = null;

    protected $fillable = [
        'task_title',
        'task_description',
        'task_assign_to',
        'task_user_id', // Creator
        'task_due_date',
        'task_client_id',
        'task_project_id',
        
        // ▼▼▼ NEW FIELDS ADDED HERE ▼▼▼
        'task_system_id',
        'task_category_id',
        'task_type_id',
        // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲
        
        'task_status_id',
        'task_priority_id',
        'task_inactive',
        'task_remarks',
        
        // Time tracking fields
        'task_date_start',
        'task_time_start',
        'task_date_end',
        'task_time_end',

        'task_edit_pending',
        'task_pending_data',
        
    ];

    protected $casts = [
        'task_log_datetime' => 'datetime',
        'task_due_date' => 'date',
        'task_date_start' => 'date',
        'task_date_end' => 'date',
        'task_inactive' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // ▼▼▼ NEW RELATIONSHIPS ▼▼▼
    public function system()
    {
        return $this->belongsTo(System::class, 'task_system_id', 'system_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'task_category_id', 'category_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'task_type_id', 'type_id');
    }
    // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

    public function project()
    {
        return $this->belongsTo(Project::class, 'task_project_id', 'project_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'task_client_id', 'client_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'task_status_id', 'status_id');
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, 'task_priority_id', 'priority_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'task_assign_to', 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'task_user_id', 'user_id');
    }

    // Add this inside your Task class in app/Models/Task.php
    public function user()
    {
        return $this->creator();
    }
}