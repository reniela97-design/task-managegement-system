<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';
    protected $primaryKey = 'client_id'; // Custom ID
    public $timestamps = false; // Stop Laravel from looking for 'created_at'

    const CREATED_AT = 'client_log_datetime'; // Your custom time column
    const UPDATED_AT = null; // You don't have an update time column

    protected $fillable = [
        'client_name',
        'client_contact_person',
        'client_contact_number',
        'client_user_id',
        'client_inactive',
    ];
}