<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * Class Thesis
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     schema="Thesis",
 *     type="object",
 *     title="Thesis",
 *     description="A thesis entity representing academic projects",
 *     @OA\Property(property="id", type="integer", description="The unique identifier of the thesis"),
 *     @OA\Property(property="name", type="string", maxLength=255, description="The title of the thesis"),
 *     @OA\Property(property="betreuer", type="string", description="The supervisor's name"),
 *     @OA\Property(property="description", type="string", description="The description of the thesis"),
 *     @OA\Property(property="kenntnisse", type="string", description="Required skills for the thesis"),
 *     @OA\Property(property="status", type="string", enum={"Angebot", "Aktiv", "Fertig", "Idle"}, description="The status of the thesis"),
 *     @OA\Property(property="prof_id", type="integer", description="The ID of the professor supervising the thesis"),
 *     @OA\Property(property="notiz", type="string", nullable=true, description="Additional notes"),
 *     @OA\Property(property="semester", type="string", nullable=true, description="The semester of the thesis (e.g., WS2023)"),
 *     @OA\Property(property="pdf_1", type="string", nullable=true, description="Path to the first PDF file"),
 *     @OA\Property(property="pdf_2", type="string", nullable=true, description="Path to the second PDF file"),
 *     @OA\Property(property="betreuer_id", type="integer", nullable=true, description="The ID of the supervisor (if different from prof_id)"),
 *     @OA\Property(property="projektart", type="array", @OA\Items(type="string", enum={"Teamprojekt", "Studienarbeit", "Bachelorthesis", "Masterthesis"}), description="The type(s) of the project"),
 *     @OA\Property(property="geheim", type="string", enum={"yes", "no"}, description="Whether the thesis is secret"),
 *     @OA\Property(property="startdatum", type="string", format="date", nullable=true, description="Start date of the thesis"),
 *     @OA\Property(property="vortragdatum", type="string", format="date", nullable=true, description="Presentation date of the thesis"),
 *     @OA\Property(property="enddatum", type="string", format="date", nullable=true, description="End date of the thesis"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
 * )
 */
class Thesis extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'betreuer',
        'description',
        'kenntnisse',
        'status',
        'prof_id',
        'notiz',
        'semester',
        'pdf_1',
        'pdf_2',
        'betreuer_id',
        'projektart',
        'geheim',
        'startdatum',    
        'vortragdatum',  
        'enddatum',      
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'projektart' => 'array',
        'startdatum' => 'date',    
        'vortragdatum' => 'date',  
        'enddatum' => 'date',     
    ];

    /**
     * Get the users interested in this thesis.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function interestedUsers()
    {
        return $this->belongsToMany(User::class, 'thesis_user', 'thesis_id', 'user_id')
                    ->withTimestamps()
                    ->withPivot('expires_at');
    }

    /**
     * Get the number of days remaining until interest expires for the authenticated user.
     *
     * @return int|string|null Returns the number of days remaining, "Not found" if no interest exists, or null if not authenticated
     */
    public function getDaysRemainingAttribute()
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }
    
        $pivot = $this->interestedUsers()
            ->where('user_id', $user->id)
            ->first();
    
        if (!$pivot) {
            \Log::debug('No pivot record found for thesis_id: ' . $this->id . ', user_id: ' . $user->id);
            return 'Not found';
        }
    
        if (!$pivot->pivot->expires_at) {
            \Log::debug('expires_at is null for thesis_id: ' . $this->id . ', user_id: ' . $user->id);
            return 'Not found';
        }
    
        $expiresAt = \Carbon\Carbon::parse($pivot->pivot->expires_at);
    
        $now = now();
    
        $daysRemaining = $expiresAt->diffInDays($now, false);
    
        $daysRemaining = abs($daysRemaining);
    
        return $daysRemaining;
    }
}