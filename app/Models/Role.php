<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'role_id';
    public $timestamps = false;

    const CREATED_AT = 'role_log_datetime';
    const UPDATED_AT = null;

    protected $fillable = [
        'role_name',
        'role_user_id',
        'role_inactive',
    ];
}