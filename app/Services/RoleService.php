<?php

namespace App\Services;

use App\Models\User;
use App\Models\Profiles\Organizer;
use App\Models\Profiles\Vendor;
use App\Models\Profiles\Attendee;

class RoleService
{
    public static function assignRoleProfile(User $user, string $role, array $profileData = []): void
    {
        $user->assignRole($role);

        match ($role) {
            'organizer' => $user->organizerProfile()->create($profileData),
            'vendor' => $user->vendorProfile()->create($profileData),
            'attendee' => $user->attendeeProfile()->create($profileData),
            default => null
        };
    }
}
