<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketPurchase;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AttendeeController extends Controller
{
    public function index()
    {
        return view('admin.attendees.index');
    }

    public function getAttendeesData(Request $request)
    {
        $query = User::role('attendee') // Using Spatie role scope
            ->withCount(['ticketPurchases as tickets_count' => function ($q) {
                $q->where('status', 'paid');
            }])
            ->withSum(['ticketPurchases as total_spent' => function ($q) {
                $q->where('status', 'paid');
            }], 'final_amount');

        return DataTables::of($query)
            ->addColumn('avatar_url', fn($user) => $user->avatar ?? null)
            ->addColumn('formatted_total_spent', fn($user) => number_format($user->total_spent ?? 0, 2))
            ->addColumn('formatted_created_at', fn($user) => $user->created_at->format('M j, Y g:i A'))
            ->filter(function ($query) use ($request) {
                // Status filter
                if ($request->filled('status')) {
                    $query->where('is_active', $request->status === 'active');
                }

                // Search filter
                if (!empty($request->search['value'])) {
                    $search = $request->search['value'];
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                }
            })
            ->make(true);
    }

    public function getAttendeesStats()
    {
        $totalAttendees = User::role('attendee')->count();
        $activeAttendees = User::role('attendee')->where('is_active', true)->count();
        $totalTickets = TicketPurchase::where('status', 'paid')->count();
        $totalRevenue = TicketPurchase::where('status', 'paid')->sum('final_amount');

        return response()->json([
            'total_attendees' => $totalAttendees,
            'active_attendees' => $activeAttendees,
            'total_tickets' => $totalTickets,
            'total_revenue' => $totalRevenue,
        ]);
    }

    public function show($id)
    {
        $attendee = User::role('attendee')
            ->with(['ticketPurchases.event', 'ticketPurchases.ticket'])
            ->findOrFail($id);

        // Compute totals for view
        $totalTickets = $attendee->ticketPurchases->where('status', 'paid')->count();
        $totalSpent = $attendee->ticketPurchases->where('status', 'paid')->sum('final_amount');

        return view('admin.attendees.show', compact('attendee', 'totalTickets', 'totalSpent'));
    }

    public function ticketPurchases($userId)
    {
        $attendee = User::role('attendee')->findOrFail($userId);

        $purchases = TicketPurchase::with(['event', 'ticket'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(15);

        // Compute totals for the ticket purchases view
        $totalTickets = $purchases->where('status', 'paid')->count();
        $totalSpent = $purchases->where('status', 'paid')->sum('final_amount');

        return view('admin.attendees.ticket-purchases', compact('attendee', 'purchases', 'totalTickets', 'totalSpent'));
    }
}
