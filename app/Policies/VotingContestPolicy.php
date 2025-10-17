<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VotingContest;
use Illuminate\Auth\Access\HandlesAuthorization;

class VotingContestPolicy
{
    use HandlesAuthorization;

    public function view(User $user, VotingContest $contest)
    {
        return $contest->organizer_id === $user->id || $user->isAdmin();
    }

    public function update(User $user, VotingContest $contest)
    {
        return $contest->organizer_id === $user->id || $user->isAdmin();
    }

    public function delete(User $user, VotingContest $contest)
    {
        return $contest->organizer_id === $user->id || $user->isAdmin();
    }
}
