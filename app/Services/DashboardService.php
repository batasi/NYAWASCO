<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use App\Models\VotingContest;
use App\Models\TicketPurchase;

class DashboardService
{
    public static function getAdminData(): array
    {
        return [
            'total_users' => User::count(),
            'total_events' => Event::count(),
            'total_voting_contests' => VotingContest::count(),
            'total_ticket_sales' => TicketPurchase::where('status', 'paid')->count(),
            'recent_users' => User::latest()->take(10)->get(),
            'pending_approvals' =>
            Event::where('status', 'draft')->count() +
                VotingContest::where('requires_approval', true)->count(),
        ];
    }
}
