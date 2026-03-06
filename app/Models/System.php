<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    use HasFactory;

    protected $table = 'systems';
    protected $primaryKey = 'system_id';
    public $timestamps = false;

    const CREATED_AT = 'system_log_datetime';
    const UPDATED_AT = null;

    protected $fillable = [
        'system_name',
        'system_user_id',
        'system_inactive',
    ];

    protected $casts = [
        'system_log_datetime' => 'datetime',
        'system_inactive' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'system_user_id', 'user_id');
    }
}