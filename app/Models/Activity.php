<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    // 1. Map to your specific table name
    protected $table = 'activity'; 

    // 2. Map to your specific Primary Key
    protected $primaryKey = 'activity_id'; 
    
    // 3. Disable default timestamps (created_at/updated_at) since you use 'activity_log_datetime'
    public $timestamps = false; 

    protected $fillable = [
        'activity_description',
        'activity_user_id',
        'activity_ip_address',
        'activity_agent',
        'activity_log_datetime'
    ];

    // 4. Ensure the date column is treated as a Date object for formatting
    protected $casts = [
        'activity_log_datetime' => 'datetime',
    ];

    // 5. Relationship to User
    public function user()
    {
        // belongsTo(Model, Foreign Key, Owner Key)
        return $this->belongsTo(User::class, 'activity_user_id', 'user_id');
    }
}