<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    public $timestamps = false;

    const CREATED_AT = 'category_log_datetime';
    const UPDATED_AT = null;

    protected $fillable = [
        'category_name',
        'category_user_id',
        'category_inactive',
    ];

    protected $casts = [
        'category_log_datetime' => 'datetime',
        'category_inactive' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'category_user_id', 'user_id');
    }
}