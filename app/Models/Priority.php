<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;

    protected $table = 'priorities';
    protected $primaryKey = 'priority_id';
    public $timestamps = false;

    const CREATED_AT = 'priority_log_datetime';
    const UPDATED_AT = null;

    protected $fillable = [
        'priority_name',
        'priority_color', // Optional, useful for badges
        'priority_inactive',
    ];
}