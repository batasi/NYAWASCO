<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'voting_contest_id',
        'nominee_id',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contest()
    {
        return $this->belongsTo(VotingContest::class, 'voting_contest_id');
    }

    public function nominee()
    {
        return $this->belongsTo(Nominee::class);
    }

    protected static function booted()
    {
        static::created(function ($vote) {
            // Update nominee votes count
            $vote->nominee->increment('votes_count');

            // Update contest total votes
            $vote->contest->increment('total_votes');
        });

        static::deleted(function ($vote) {
            // Update nominee votes count
            $vote->nominee->decrement('votes_count');

            // Update contest total votes
            $vote->contest->decrement('total_votes');
        });
    }
}
