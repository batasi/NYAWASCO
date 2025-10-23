<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class VotingContest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organizer_id',
        'category_id',
        'title',
        'slug',
        'description',
        'rules',
        'featured_image',
        'start_date',
        'end_date',
        'is_active',
        'is_featured',
        'requires_approval',
        'max_votes_per_user',
        'total_votes',
        'views_count',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'requires_approval' => 'boolean',
        'max_votes_per_user' => 'integer',
        'total_votes' => 'integer',
        'views_count' => 'integer',
        'metadata' => 'array',
    ];

    protected $attributes = [
        'is_active' => true,
        'is_featured' => false,
        'requires_approval' => false,
        'max_votes_per_user' => 1,
        'total_votes' => 0,
        'views_count' => 0,
    ];

    // Relationships
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function category()
    {
        return $this->belongsTo(VotingCategory::class);
    }

    public function nominees()
    {
        return $this->hasMany(Nominee::class, 'voting_contest_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'contest_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeEnded($query)
    {
        return $query->where('end_date', '<', now());
    }

    // Helper Methods
 

    public function isUpcoming()
    {
        return $this->start_date > now();
    }

    public function hasEnded()
    {
        return $this->end_date && $this->end_date < now();
    }
    public function isEnded()
    {
        return $this->end_date && $this->end_date < now();
    }
    public function canVote()
    {
        return $this->is_active && $this->isOngoing();
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function getLeadingNominee()
    {
        return $this->nominees()->orderByDesc('votes_count')->first();
    }
    public function isOngoing(): bool
    {
        $now = Carbon::now();
        return $this->is_active && $this->start_date <= $now && (!$this->end_date || $this->end_date >= $now);
    }

    public function userCanVote($user): bool
    {
        // Make sure user is provided
        if (!$user) {
            return false;
        }

        // Count how many votes this user has cast in this contest
        $voteCount = \App\Models\Vote::where('user_id', $user->id)
            ->where('voting_contest_id', $this->id)
            ->count();

        // If below the limit, allow voting
        return $voteCount < $this->max_votes_per_user;
    }

    public function isOrganizer($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->organizer_id === $userId;
    }


}
