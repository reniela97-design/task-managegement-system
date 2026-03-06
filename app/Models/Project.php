<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    
    public function client()
    {
        return $this->belongsTo(Client::class, 'project_client_id', 'client_id');
    }
}