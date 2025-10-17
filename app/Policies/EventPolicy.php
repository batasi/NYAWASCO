<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Event $event)
    {
        return $event->organizer_id === $user->id || $user->isAdmin();
    }

    public function update(User $user, Event $event)
    {
        return $event->organizer_id === $user->id || $user->isAdmin();
    }

    public function delete(User $user, Event $event)
    {
        return $event->organizer_id === $user->id || $user->isAdmin();
    }
}
