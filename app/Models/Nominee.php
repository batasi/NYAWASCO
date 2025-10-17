<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nominee extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_contest_id',
        'name',
        'bio',
        'photo',
        'position',
        'votes_count',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function contest()
    {
        return $this->belongsTo(VotingContest::class, 'voting_contest_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPosition($query)
    {
        return $query->orderBy('position')->orderBy('name');
    }

    public function getVotePercentageAttribute()
    {
        if ($this->contest->total_votes === 0) {
            return 0;
        }
        return ($this->votes_count / $this->contest->total_votes) * 100;
    }
}
