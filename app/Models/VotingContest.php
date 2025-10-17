<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'metadata' => 'array',
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
        return $this->hasMany(Nominee::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeEnded($query)
    {
        return $query->where('end_date', '<', now());
    }

    // Methods
    public function isOngoing()
    {
        $now = now();
        return $this->start_date <= $now &&
            (is_null($this->end_date) || $this->end_date >= $now);
    }

    public function hasEnded()
    {
        return $this->end_date && $this->end_date < now();
    }

    public function userCanVote(User $user)
    {
        if (!$this->isOngoing()) {
            return false;
        }

        $userVotesCount = $this->votes()->where('user_id', $user->id)->count();
        return $userVotesCount < $this->max_votes_per_user;
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function getLeadingNominee()
    {
        return $this->nominees()->orderBy('votes_count', 'desc')->first();
    }
}
