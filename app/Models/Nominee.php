<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nominee extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_contest_id',
        'nominee_category_id',
        'name',
        'bio',
        'code',
        'photo',
        'affiliation',
        'position',
        'votes_count',
        'is_active',
    ];

    protected $casts = [
        'votes_count' => 'integer',
        'position' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'votes_count' => 0,
        'position' => 0,
        'is_active' => true,
    ];

    // Relationships
    public function votingContest()
    {
        return $this->belongsTo(VotingContest::class, 'voting_contest_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'nominee_id');
    }

    public function votePurchases()
    {
        return $this->hasMany(VotePurchase::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position')->orderBy('name');
    }

    public function scopeLeading($query)
    {
        return $query->orderByDesc('votes_count');
    }

    // Helper Methods
    public function incrementVotes($count = 1)
    {
        $this->increment('votes_count', $count);
        $this->votingContest->increment('total_votes', $count);
    }

    public function getVotePercentageAttribute()
    {
        $totalVotes = $this->votingContest->total_votes;
        return $totalVotes > 0 ? round(($this->votes_count / $totalVotes) * 100, 2) : 0;
    }
    public function nomineeCategory()
    {
        return $this->belongsTo(NomineeCategory::class, 'nominee_category_id');
    }
    public function contest()
    {
        return $this->belongsTo(VotingContest::class, 'voting_contest_id');
    }
    
   
}
