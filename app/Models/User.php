<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * 1. Database Configuration
     * Tell Laravel to use your custom table and primary key.
     */
    protected $table = 'users';
    protected $primaryKey = 'user_id'; 
    public $timestamps = false; // Disable default timestamps

    // Map the creation timestamp to your custom column
    const CREATED_AT = 'user_log_datetime';
    // Disable the updated timestamp
    const UPDATED_AT = null;

    /**
     * 2. Mass Assignable Attributes
     */
    protected $fillable = [
        'user_name',
        'user_email',
        'user_password',
        'user_role_id',
        'user_inactive',
    ];

    /**
     * 3. Hidden Attributes
     * Hide your custom password column from array/JSON output.
     */
    protected $hidden = [
        'user_password',
        'remember_token',
    ];

    /**
     * 4. Attribute Casting
     * Automatically hash the custom password column when saving.
     */
    protected function casts(): array
    {
        return [
            'user_password' => 'hashed',
        ];
    }

    /**
     * 5. Authentication Overrides
     * Tell Laravel to use 'user_password' instead of 'password'.
     */
    public function getAuthPassword()
    {
        return $this->user_password;
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Relationship: User belongs to a Role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'user_role_id', 'role_id');
    }

    /**
     * Relationship: User has many Activities.
     * This connects to the Activity model you created.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'activity_user_id', 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the user has a specific role (case-insensitive).
     * Usage: if ($user->hasRole('Administrator')) { ... }
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName): bool
    {
        // Safety check: if role relation isn't loaded or doesn't exist
        if (!$this->role) {
            return false;
        }

        return strtolower($this->role->role_name) === strtolower($roleName);
    }
}