<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contest_id',
        'voting_contest_id',
        'nominee_id',
        'voted_at',
        'ip_address',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    protected $attributes = [
        'voted_at' => null,
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contest()
    {
        return $this->belongsTo(VotingContest::class, 'contest_id');
    }

    public function nominee()
    {
        return $this->belongsTo(Nominee::class);
    }

    // Scopes
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    // Helper Methods
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vote) {
            if (!$vote->voted_at) {
                $vote->voted_at = now();
            }
        });

        static::created(function ($vote) {
            // Increment nominee votes count
            $vote->nominee->incrementVotes();

            // Update user voting statistics
            $vote->user->increment('total_votes_cast');
            $vote->user->update(['last_vote_at' => now()]);
        });
    }
}
