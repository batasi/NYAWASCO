<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'contest_id',
        'name',
        'photo',
        'affiliation',
        'bio',
        'votes_count'
    ];

    public function contest()
    {
        return $this->belongsTo(VotingContest::class, 'contest_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'candidate_id');
    }
}
