<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Vote;
use App\Models\Event;

class LiveActivityController extends Controller
{
    public function index()
    {
        // Get recent ticket purchases
        $recentTickets = Ticket::with(['event', 'user'])
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'message' => "<strong>{$ticket->user->name}</strong> just booked a ticket for <strong>{$ticket->event->name}</strong>",
                    'type' => 'ticket',
                    'timestamp' => $ticket->created_at
                ];
            });

        // Get recent votes
        $recentVotes = Vote::with(['contest', 'user'])
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($vote) {
                return [
                    'message' => "A vote was just cast for <strong>{$vote->contest->title}</strong>",
                    'type' => 'vote',
                    'timestamp' => $vote->created_at
                ];
            });

        // Get trending events
        $trendingEvents = Event::where('is_active', true)
            ->where('start_date', '>=', now())
            ->withCount('tickets')
            ->orderBy('tickets_count', 'desc')
            ->take(3)
            ->get()
            ->map(function ($event) {
                return [
                    'message' => "<strong>{$event->name}</strong> is trending with {$event->tickets_count} bookings today",
                    'type' => 'trend',
                    'timestamp' => now()
                ];
            });

        $activities = $recentTickets->merge($recentVotes)->merge($trendingEvents)
            ->sortByDesc('timestamp')
            ->take(5)
            ->values();

        $totalActivities = Ticket::where('created_at', '>=', now()->subHours(24))->count() +
            Vote::where('created_at', '>=', now()->subHours(24))->count();

        return response()->json([
            'activities' => $activities,
            'total' => $totalActivities
        ]);
    }
}
