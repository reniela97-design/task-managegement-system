<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status'; // Your specific table name
    protected $primaryKey = 'status_id';
    public $timestamps = false;

    const CREATED_AT = 'status_log_datetime';
    const UPDATED_AT = null;

    protected $fillable = [
        'status_name',
        'status_color',
        'status_user_id',
        'status_inactive',
    ];
}