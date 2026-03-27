<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';
    protected $primaryKey = 'client_id';
    
    // Custom timestamp columns
    const CREATED_AT = 'client_log_datetime';
    const UPDATED_AT = null;

    protected $fillable = [
        'client_name',
        'client_contact_person',
        'client_contact_number',
        'client_user_id',
        'client_inactive',
    ];

    protected $casts = [
        'client_inactive' => 'boolean',
    ];

    /**
     * Scope a query to only include active clients.
     */
    public function scopeActive($query)
    {
        return $query->where('client_inactive', false);
    }

    /**
     * Scope a query to only include inactive clients.
     */
    public function scopeInactive($query)
    {
        return $query->where('client_inactive', true);
    }

    /**
     * Get the user that created the client.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }
}