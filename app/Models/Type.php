<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $table = 'types';
    protected $primaryKey = 'type_id';
    public $timestamps = false;

    const CREATED_AT = 'type_log_datetime';
    const UPDATED_AT = null;

    protected $fillable = [
        'type_name',
        'type_user_id',
        'type_inactive',
    ];

    protected $casts = [
        'type_log_datetime' => 'datetime',
        'type_inactive' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'type_user_id', 'user_id');
    }
}