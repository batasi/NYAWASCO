<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\VotingContest;
use App\Models\TicketPurchase;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Base data for all users
        $data = [
            'user' => $user,
            'recent_events' => Event::with('category')
                ->where('is_active', true)
                ->where('start_date', '>=', now())
                ->orderBy('start_date')
                ->take(5)
                ->get(),
            'active_voting' => VotingContest::with('category')
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->where('end_date', '>=', now())
                        ->orWhereNull('end_date');
                })
                ->orderBy('end_date', 'asc')
                ->take(5)
                ->get(),
        ];

        // Role-specific data
        switch ($user->role) {
            case 'admin':
                $data = array_merge($data, $this->getAdminData());
                return view('dashboard.admin', $data);

            case 'organizer':
                $data = array_merge($data, $this->getOrganizerData($user));
                return view('dashboard.organizer', $data);

            case 'vendor':
                $data = array_merge($data, $this->getVendorData($user));
                return view('dashboard.vendor', $data);

            default: // attendee
                $data = array_merge($data, $this->getAttendeeData($user));
                return view('dashboard.attendee', $data);
        }
    }

    private function getAdminData()
    {
        return [
            'total_users' => \App\Models\User::count(),
            'total_events' => Event::count(),
            'total_voting_contests' => VotingContest::count(),
            'total_ticket_sales' => TicketPurchase::where('status', 'paid')->count(),
            'recent_users' => \App\Models\User::latest()->paginate(10),
            'pending_approvals' => Event::where('status', 'draft')->count() +
                VotingContest::where('requires_approval', true)->count(),
        ];
    }

    private function getOrganizerData($user)
    {
        $organizedEvents = Event::where('organizer_id', $user->id);
        $organizedVoting = VotingContest::where('organizer_id', $user->id);

        return [
            'my_events' => $organizedEvents->where('is_active', true)->count(),
            'my_voting_contests' => $organizedVoting->where('is_active', true)->count(),
            'total_ticket_sales' => TicketPurchase::whereIn('event_id', $organizedEvents->pluck('id'))
                ->where('status', 'paid')
                ->sum('quantity'),
            'total_revenue' => TicketPurchase::whereIn('event_id', $organizedEvents->pluck('id'))
                ->where('status', 'paid')
                ->sum('final_amount'),
            'upcoming_events' => $organizedEvents->with('category')
                ->where('start_date', '>=', now())
                ->where('is_active', true)
                ->orderBy('start_date')
                ->take(5)
                ->get(),
            'active_voting_contests' => $organizedVoting->with('category')
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->where('end_date', '>=', now())
                        ->orWhereNull('end_date');
                })
                ->orderBy('end_date', 'asc')
                ->take(5)
                ->get(),
            'recent_ticket_sales' => TicketPurchase::with(['event', 'user'])
                ->whereIn('event_id', $organizedEvents->pluck('id'))
                ->where('status', 'paid')
                ->latest()
                ->take(10)
                ->get(),
        ];
    }

    private function getVendorData($user)
    {
        return [
            'total_events' => 0, // Would be based on vendor services
            'upcoming_bookings' => collect([]), // Vendor-specific bookings
            'revenue' => 0, // Vendor-specific revenue
            'rating' => 4.5, // Vendor rating
        ];
    }

    private function getAttendeeData($user)
    {
        return [
            'upcoming_tickets' => TicketPurchase::with(['event', 'ticket'])
                ->where('user_id', $user->id)
                ->where('status', 'paid')
                ->whereHas('event', function ($query) {
                    $query->where('start_date', '>=', now());
                })
                ->orderBy('created_at')
                ->take(5)
                ->get(),
            'past_events' => TicketPurchase::with(['event', 'ticket'])
                ->where('user_id', $user->id)
                ->where('status', 'paid')
                ->whereHas('event', function ($query) {
                    $query->where('start_date', '<', now());
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'my_votes' => Vote::with(['contest', 'nominee'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get(),
            'total_tickets' => TicketPurchase::where('user_id', $user->id)
                ->where('status', 'paid')
                ->count(),
            'total_votes' => Vote::where('user_id', $user->id)->count(),
        ];
    }
    public function notifications()
    {
        $user = Auth::user();
        $notifications = DatabaseNotification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dashboard.notifications', [
            'notifications' => $notifications,

            'title' => 'My Notifications - EventSphere'
        ]);
    }
    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        $notification = DatabaseNotification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $user->id)
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }
    }
    public function markAllNotificationsAsRead()
    {
        $user = Auth::user();
        DatabaseNotification::where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
