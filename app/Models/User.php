<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Annotations as OA;

/**
 * Class User
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="A user entity representing an authenticated user in the system",
 *     @OA\Property(property="id", type="integer", description="The unique identifier of the user", example=1),
 *     @OA\Property(property="name", type="string", description="The user's first name", example="John"),
 *     @OA\Property(property="nachname", type="string", description="The user's last name", example="Doe"),
 *     @OA\Property(property="email", type="string", format="email", description="The user's email address", example="john.doe@example.com"),
 *     @OA\Property(property="password", type="string", description="The user's hashed password (hidden in responses)", example="$2y$10$..."),
 *     @OA\Property(property="roll", type="string", enum={"Admin", "Prof", "Student"}, description="The user's role", example="Student"),
 *     @OA\Property(property="internal_code", type="string", nullable=true, description="Internal code for the user", example="STU12345"),
 *     @OA\Property(property="praefix", type="string", nullable=true, description="Prefix for the user's name (e.g., Dr., Prof.)", example="Dr."),
 *     @OA\Property(property="activation_code", type="string", nullable=true, description="Code for account activation", example="ABC123"),
 *     @OA\Property(property="activation_expires_at", type="string", format="date-time", nullable=true, description="Expiration time of activation code", example="2025-04-10T12:00:00Z"),
 *     @OA\Property(property="is_activated", type="boolean", description="Whether the user's account is activated", example=true),
 *     @OA\Property(property="reset_code", type="string", nullable=true, description="Code for password reset", example="XYZ789"),
 *     @OA\Property(property="reset_expires_at", type="string", format="date-time", nullable=true, description="Expiration time of reset code", example="2025-04-05T14:00:00Z"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, description="Timestamp when email was verified", example="2025-04-01T10:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp", example="2025-04-01T09:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", example="2025-04-02T15:30:00Z")
 * )
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nachname',
        'email',
        'password',
        'roll',
        'internal_code',
        'praefix',
        'activation_code',
        'activation_expires_at',
        'is_activated',
        'reset_code', 
        'reset_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the theses this user is interested in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function interestedTheses()
    {
        return $this->belongsToMany(Thesis::class, 'thesis_user', 'user_id', 'thesis_id')
                    ->withTimestamps()
                    ->withPivot('expires_at');
    }

    /**
     * Boot the model and set up event listeners.
     *
     * This method ensures that when a user is deleted, their interest in theses is also removed.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            $user->interestedTheses()->detach();
        });
    }
}