<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // legacy field if needed for simple role checks
        'phone',
        'bio',
        'avatar',
        'is_active',
        'preferences',
        'current_team_id',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'preferences' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    protected $guard_name = 'web';

    // ---------------------------
    // Relationships
    // ---------------------------

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function organizedEvents()
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    public function organizedVotingContests()
    {
        return $this->hasMany(VotingContest::class, 'organizer_id');
    }

    public function ticketPurchases()
    {
        return $this->hasMany(TicketPurchase::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // ---------------------------
    // Scopes
    // ---------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ---------------------------
    // Role Helpers (optional)
    // ---------------------------

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isOrganizer(): bool
    {
        return $this->hasRole('organizer');
    }

    public function isVendor(): bool
    {
        return $this->hasRole('vendor');
    }

    public function isAttendee(): bool
    {
        return $this->hasRole('attendee');
    }

    // ---------------------------
    // Accessors
    // ---------------------------

    public function getProfilePhotoUrlAttribute(): string
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : asset('default-avatar.png');
    }
}
