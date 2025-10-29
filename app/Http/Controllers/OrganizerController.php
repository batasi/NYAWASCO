<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\VotingContest;
use App\Models\TicketPurchase;
use App\Models\EventCategory;
use App\Models\VotingCategory;
use App\Models\NomineeCategory;
use App\Models\Nominee;
use App\Models\Vote;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Booking;
use App\Models\Ticket;


class OrganizerController extends BaseController
{
    use DispatchesJobs, ValidatesRequests;


    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:organizer')->except(['index', 'show']);
    }

    public function index()
    {
        $organizers = User::role('organizer')
            ->where('is_active', true)
            ->withCount(['events', 'votingContests'])
            ->paginate(10);

        return view('organizers.index', [
            'organizers' => $organizers,
            'title' => 'EventSphere',
        ]);
    }

    public function show($id)
    {
        $organizer = User::where('role', 'organizer')
            ->where('id', $id)
            ->where('is_active', true)
            ->withCount(['events', 'votingContests'])
            ->firstOrFail();

        $events = Event::where('organizer_id', $organizer->id)
            ->where('is_active', true)
            ->with('category')
            ->latest()
            ->paginate(6);

        $votingContests = VotingContest::where('organizer_id', $organizer->id)
            ->where('is_active', true)
            ->with('category')
            ->latest()
            ->paginate(6);

        return view('organizers.show', [
            'organizer' => $organizer,
            'events' => $events,
            'votingContests' => $votingContests,
            'title' => $organizer->company_name . ' - EventSphere',
        ]);
    }

    public function approveEvent(Event $event)
    {
        if (!Auth::user()->hasRole('admin')) {
            return back()->with('error', 'Only administrators can approve events.');
        }

        $event->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id()
        ]);

        // You can also send notification to the organizer here
        // Notification::send($event->organizer, new EventApproved($event));

        return back()->with('success', 'Event approved successfully!');
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Organizer's events
        $events = Event::where('organizer_id', $user->id)->get();

        // Core statistics
        $totalEvents = $events->count();
        $activeEvents = $events->where('is_active', true)->count();

        // Voting contests
        $totalVotingContests = VotingContest::where('organizer_id', $user->id)->count();
        $activeContests = VotingContest::where('organizer_id', $user->id)
            ->where('is_active', true)
            ->where('end_date', '>', now())
            ->count();

        // Ticket sales and revenue
        $totalTicketSales = TicketPurchase::whereHas('event', function ($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->where('status', 'paid')->sum('quantity');

        $todaySales = TicketPurchase::whereHas('event', function ($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->where('status', 'paid')
          ->whereDate('created_at', today())
          ->sum('quantity');

        $totalRevenue = TicketPurchase::whereHas('event', function ($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->where('status', 'paid')->sum('final_amount');

        $monthRevenue = TicketPurchase::whereHas('event', function ($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->where('status', 'paid')
          ->whereMonth('created_at', now()->month)
          ->whereYear('created_at', now()->year)
          ->sum('final_amount');

        // Total bookings (polymorphic)
        $totalBookings = Booking::whereHasMorph(
            'bookable',
            [Event::class],
            function ($query) use ($user) {
                $query->where('organizer_id', $user->id);
            }
        )->count();

        // Upcoming events
        $upcomingEvents = Event::where('organizer_id', $user->id)
            ->where('is_active', true)
            ->where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        // Recent bookings
        $recentBookings = Booking::whereHasMorph(
            'bookable',
            [Event::class],
            function ($query) use ($user) {
                $query->where('organizer_id', $user->id);
            }
        )->with(['bookable', 'user'])
         ->latest()
         ->take(5)
         ->get();

        // Recent ticket sales
        $recentTicketSales = TicketPurchase::whereHas('event', function ($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->with(['event', 'ticket', 'user'])
          ->where('status', 'paid')
          ->latest()
          ->take(5)
          ->get();

        // Performance metrics (placeholder calculations)
        $attendanceRate = $totalEvents > 0 ? min(100, round(($totalBookings / max(1, $totalEvents * 100)) * 100)) : 0;
        $conversionRate = $totalEvents > 0 ? min(100, round(($totalTicketSales / max(1, $totalEvents * 50)) * 100)) : 0;
        $satisfactionRate = 95; // Placeholder

        // Compile stats array for the view
        $stats = [
            'total_events' => $totalEvents,
            'active_events' => $activeEvents,
            'total_voting_contests' => $totalVotingContests,
            'active_contests' => $activeContests,
            'total_ticket_sales' => $totalTicketSales,
            'today_sales' => $todaySales,
            'total_revenue' => $totalRevenue,
            'month_revenue' => $monthRevenue,
            'total_bookings' => $totalBookings,
            'attendance_rate' => $attendanceRate,
            'conversion_rate' => $conversionRate,
            'satisfaction_rate' => $satisfactionRate,
        ];

        // Recent activity (combine recent bookings and ticket sales)
        $recentActivity = collect();

        // Add recent ticket sales to activity
        foreach ($recentTicketSales as $sale) {
            $recentActivity->push((object)[
                'type' => 'ticket_sale',
                'type_color' => 'green',
                'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z',
                'message' => "New ticket sale for {$sale->event->title}",
                'created_at' => $sale->created_at,
            ]);
        }

        // Add recent bookings to activity
        foreach ($recentBookings as $booking) {
            $recentActivity->push((object)[
                'type' => 'booking',
                'type_color' => 'blue',
                'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                'message' => "New booking for {$booking->bookable->title}",
                'created_at' => $booking->created_at,
            ]);
        }

        // Sort by creation date and take latest 5
        $recentActivity = $recentActivity->sortByDesc('created_at')->take(5);

        return view('dashboard.organizer', compact(
            'stats',
            'upcomingEvents',
            'recentBookings',
            'recentTicketSales',
            'recentActivity'
        ));
    }

    public function ticketSales(Request $request)
    {
        $user = Auth::user();

        // Get organizer's tickets with sales data
        $ticketsQuery = Ticket::whereHas('event', function($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->with(['event', 'purchases']);

        // Apply filters
        if ($request->has('event') && $request->event) {
            $ticketsQuery->where('event_id', $request->event);
        }

        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $ticketsQuery->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $ticketsQuery->where('is_active', false);
            }
        }

        if ($request->has('start_date') && $request->start_date) {
            $ticketsQuery->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $ticketsQuery->whereDate('created_at', '<=', $request->end_date);
        }

        $ticketSales = $ticketsQuery->paginate(10);

        // Calculate statistics
        $totalSales = TicketPurchase::whereHas('event', function($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->where('status', 'paid')->sum('final_amount');

        $ticketsSold = TicketPurchase::whereHas('event', function($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->where('status', 'paid')->sum('quantity');

        $activeEvents = Event::where('organizer_id', $user->id)
            ->where('is_active', true)
            ->count();

        $pendingPayments = TicketPurchase::whereHas('event', function($query) use ($user) {
            $query->where('organizer_id', $user->id);
        })->where('status', 'pending')->count();

        // Get events for filter dropdown
        $events = Event::where('organizer_id', $user->id)->get();

        return view('organizer.ticket-sales', compact(
            'ticketSales',
            'totalSales',
            'ticketsSold',
            'activeEvents',
            'pendingPayments',
            'events'
        ));
    }

    public function bookings(Request $request)
    {
        $user = Auth::user();

        // Get organizer's bookings for events (using polymorphic relationship)
        $bookingsQuery = Booking::whereHasMorph(
            'bookable',
            [Event::class],
            function($query) use ($user) {
                $query->where('organizer_id', $user->id);
            }
        )->with(['bookable', 'user']);

        // Apply filters
        if ($request->has('event') && $request->event) {
            $bookingsQuery->whereHasMorph('bookable', [Event::class], function($query) use ($request) {
                $query->where('id', $request->event);
            });
        }

        if ($request->has('status') && $request->status) {
            $bookingsQuery->where('status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status) {
            $bookingsQuery->where('payment_status', $request->payment_status);
        }

        if ($request->has('start_date') && $request->start_date) {
            $bookingsQuery->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $bookingsQuery->whereDate('created_at', '<=', $request->end_date);
        }

        $bookings = $bookingsQuery->latest()->paginate(10);

        // Calculate statistics
        $totalBookings = Booking::whereHasMorph(
            'bookable',
            [Event::class],
            function($query) use ($user) {
                $query->where('organizer_id', $user->id);
            }
        )->count();

        $confirmedBookings = Booking::whereHasMorph(
            'bookable',
            [Event::class],
            function($query) use ($user) {
                $query->where('organizer_id', $user->id);
            }
        )->where('status', 'confirmed')->count();

        $pendingBookings = Booking::whereHasMorph(
            'bookable',
            [Event::class],
            function($query) use ($user) {
                $query->where('organizer_id', $user->id);
            }
        )->where('status', 'pending')->count();

        $cancelledBookings = Booking::whereHasMorph(
            'bookable',
            [Event::class],
            function($query) use ($user) {
                $query->where('organizer_id', $user->id);
            }
        )->where('status', 'cancelled')->count();

        // Get events for filter dropdown
        $events = Event::where('organizer_id', $user->id)->get();

        return view('organizer.bookings', compact(
            'bookings',
            'totalBookings',
            'confirmedBookings',
            'pendingBookings',
            'cancelledBookings',
            'events'
        ));
    }

    public function bookingDetails($bookingId)
    {
        $user = Auth::user();

        $booking = Booking::where('id', $bookingId)
            ->whereHasMorph(
                'bookable',
                [Event::class],
                function($query) use ($user) {
                    $query->where('organizer_id', $user->id);
                }
            )
            ->with(['bookable', 'user'])
            ->firstOrFail();

        return view('organizer.booking-details', compact('booking'));
    }

    public function updateBooking(Request $request, Booking $booking)
    {
        $user = Auth::user();

        // Verify the booking belongs to organizer's event (using polymorphic relationship)
        if ($booking->bookable_type !== Event::class || $booking->bookable->organizer_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        $booking->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Booking status updated successfully.');
    }

public function events(Request $request)
{
    $query = Event::with(['organizer', 'category']);

    // ✅ ORGANIZER: Only see their own events (all statuses and dates)
    if (Auth::check() && Auth::user()->role === 'organizer') {
        $query->where('organizer_id', Auth::id());
    }

    // ✅ Optional filters
    if ($request->has('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    if ($request->filled('location')) {
        $query->where('location', 'like', '%' . $request->location . '%');
    }

    // ✅ PUBLIC or REGULAR USER: only approved, active, and future events
    if (
        !Auth::check() ||
        (Auth::check() && !in_array(Auth::user()->role, ['admin', 'organizer']))
    ) {
        $query->where('status', 'approved')
              ->where('is_active', true)
              ->where('start_date', '>', now());
    }

    // ✅ ADMINS: see all events, including past ones
    // (no extra filtering needed – they see everything by default)

    $events = $query->orderBy('start_date', 'desc')->paginate(12);

    $categories = EventCategory::where('is_active', true)->get();

    return view('events.index', [
        'events' => $events,
        'categories' => $categories,
        'title' => Auth::check() && Auth::user()->role === 'organizer'
            ? 'My Events - EventSphere'
            : 'Events - EventSphere',
    ]);
}


    public function createEvent()
    {
        $categories = EventCategory::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();
        $nomineeCategories = NomineeCategory::all();
        $organizers = User::where('role', 'organizer')->paginate(12);

        return view('organizers.events.create', [
            'categories' => $categories,
            'organizers' => $organizers,
            'nomineeCategories' => $nomineeCategories,
            'title' => 'Create New Event - EventSphere'
        ]);
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'category_id' => 'required|exists:event_categories,id',
            'description' => 'nullable|string',
            'location' => 'required|string|max:191',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'ticket_price' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'banner_image' => 'required|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('events', 'public');
            $validated['banner_image'] = $path;
        }

        // Set default values
        $validated['organizer_id'] = Auth::id();
        $validated['status'] = 'draft';
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        $event = Event::create($validated);

        return redirect()->route('organizer.events.edit', $event)
            ->with('success', 'Event created successfully! You can now add tickets and other details.');
    }

    public function storeEventCategory(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('event_categories', 'name')
            ],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'required|string|max:7|regex:/^#[0-9A-F]{6}$/i',
            'is_active' => 'boolean',
        ]);

        try {
            // Create the category
            $category = EventCategory::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'icon' => $validated['icon'] ?? null,
                'color' => $validated['color'],
                'is_active' => $validated['is_active'] ?? true,
                'sort_order' => EventCategory::max('sort_order') + 1,
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Category creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function editEvent(Event $event)
    {
        $categories = EventCategory::where('is_active', true)->get();

        return view('events.edit', [
            'event' => $event,
            'categories' => $categories,
            'title' => "Edit {$event->title} - EventSphere"
        ]);
    }

    public function updateEvent(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'category_id' => 'required|exists:event_categories,id',
            'description' => 'nullable|string',
            'location' => 'required|string|max:191',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'ticket_price' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'banner_image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,pending_approval,approved,cancelled',
        ]);

        // Handle file upload
        if ($request->hasFile('banner_image')) {
            // Delete old banner image if exists
            if ($event->banner_image) {
                Storage::disk('public')->delete($event->banner_image);
            }

            $path = $request->file('banner_image')->store('events', 'public');
            $validated['banner_image'] = $path;
        } elseif ($request->has('remove_banner') && $request->remove_banner == '1') {
            // Remove banner image if requested
            if ($event->banner_image) {
                Storage::disk('public')->delete($event->banner_image);
            }
            $validated['banner_image'] = null;
        }

        // Set boolean values
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');

        $event->update($validated);

        return back()->with('success', 'Event updated successfully!');
    }

    public function updateEventStatus(Request $request, Event $event)
    {
        $request->validate([
            'status' => 'required|in:draft,pending_approval,approved,cancelled'
        ]);

        // Check if user has permission to update this event
        if ($event->organizer_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return back()->with('error', 'You are not authorized to update this event.');
        }

        $oldStatus = $event->status;
        $newStatus = $request->status;

        $event->update([
            'status' => $newStatus,
            'updated_at' => now()
        ]);

        // Log the status change
        Log::info("Event status changed", [
            'event_id' => $event->id,
            'event_title' => $event->title,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'user_id' => Auth::id()
        ]);

        $statusMessages = [
            'draft' => 'Event moved to draft',
            'pending_approval' => 'Event submitted for approval',
            'approved' => 'Event approved and published',
            'cancelled' => 'Event cancelled'
        ];

        return back()->with('success', $statusMessages[$newStatus] . ' successfully!');
    }

    public function destroyEvent(Event $event)
    {
        if ($event->organizer_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return redirect()->route('organizer.events')
                ->with('error', 'You are not authorized to delete this event.');
        }

        $hasBookings = Booking::whereHasMorph(
            'bookable',
            [Event::class],
            function($query) use ($event) {
                $query->where('id', $event->id);
            }
        )->exists();

        if ($hasBookings) {
            return redirect()->route('organizer.events')
                ->with('error', 'Cannot delete event with existing bookings. Please cancel the event instead.');
        }

        $hasTicketPurchases = TicketPurchase::where('event_id', $event->id)->exists();

        if ($hasTicketPurchases) {
            return redirect()->route('organizer.events')
                ->with('error', 'Cannot delete event with existing ticket purchases. Please cancel the event instead.');
        }

        $eventTitle = $event->title;

        if ($event->banner_image) {
            Storage::disk('public')->delete($event->banner_image);
        }

        $event->tickets()->delete();

        $event->delete();

        Log::info("Event deleted", [
            'event_id' => $event->id,
            'event_title' => $eventTitle,
            'user_id' => Auth::id(),
            'deleted_at' => now()
        ]);

        return redirect()->route('organizer.events')
            ->with('success', "Event '{$eventTitle}' deleted successfully!");
    }

    // Voting Management Methods
    public function voting()
    {
        $contests = VotingContest::where('organizer_id', Auth::id())
            ->with(['category', 'nominees'])
            ->latest()
            ->paginate(10);

        return view('organizer.voting.index', [
            'contests' => $contests,
            'title' => 'My Voting Contests - EventSphere'
        ]);
    }

    public function createVoting()
    {
        $categories = VotingCategory::where('is_active', true)->get();
        $nomineeCategories = NomineeCategory::all();

        return view('organizer.voting.create', [
            'categories' => $categories,
            'nomineeCategories' => $nomineeCategories,
            'title' => 'Create Voting Contest - EventSphere'
        ]);
    }

    public function storeVoting(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:voting_categories,id',
            'description' => 'required|string',
            'rules' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'start_date' => 'required|date|after:now',
            'end_date' => 'nullable|date|after:start_date',
            'max_votes_per_user' => 'required|integer|min:1|max:10',
            'nominees' => 'required|array|min:2',
            'nominees.*.name' => 'required|string|max:255',
            'nominees.*.bio' => 'nullable|string',
            'nominees.*.photo' => 'nullable|image|max:1024',
        ]);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('voting-contests', 'public');
            $validated['featured_image'] = $path;
        }

        // Create voting contest
        $contest = VotingContest::create([
            'organizer_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
            'description' => $validated['description'],
            'rules' => $validated['rules'],
            'featured_image' => $validated['featured_image'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'max_votes_per_user' => $validated['max_votes_per_user'],
            'status' => 'published',
        ]);

        // Create nominees
        foreach ($validated['nominees'] as $index => $nomineeData) {
            $nominee = [
                'voting_contest_id' => $contest->id,
                'name' => $nomineeData['name'],
                'bio' => $nomineeData['bio'],
                'position' => $index,
            ];

            // Handle nominee photo upload
            if (isset($nomineeData['photo']) && $nomineeData['photo']->isValid()) {
                $photoPath = $nomineeData['photo']->store('nominees', 'public');
                $nominee['photo'] = $photoPath;
            }

            Nominee::create($nominee);
        }

        return redirect()->route('organizer.voting')
            ->with('success', 'Voting contest created successfully!');
    }

    public function editVoting(VotingContest $contest)
    {

        $categories = VotingCategory::where('is_active', true)->get();
        $contest->load('nominees');

        return view('organizer.voting.edit', [
            'contest' => $contest,
            'categories' => $categories,
            'title' => "Edit {$contest->title} - EventSphere"
        ]);
    }

    public function updateVoting(Request $request, VotingContest $contest)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:voting_categories,id',
            'description' => 'required|string',
            'rules' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_votes_per_user' => 'required|integer|min:1|max:10',
            'is_active' => 'boolean',
        ]);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('voting-contests', 'public');
            $validated['featured_image'] = $path;
        }

        $contest->update($validated);

        return back()->with('success', 'Voting contest updated successfully!');
    }

    public function destroyVoting(VotingContest $contest)
    {

        $contest->delete();

        return redirect()->route('organizer.voting')
            ->with('success', 'Voting contest deleted successfully!');
    }

    // Analytics Methods
    public function analytics()
    {
        $user = Auth::user();

        $eventStats = [
            'total_events' => Event::where('organizer_id', $user->id)->count(),
            'active_events' => Event::where('organizer_id', $user->id)
                ->where('is_active', true)
                ->count(),
            'total_attendees' => TicketPurchase::whereIn(
                'event_id',
                Event::where('organizer_id', $user->id)->pluck('id')
            )
                ->where('status', 'paid')
                ->sum('quantity'),
            'total_revenue' => TicketPurchase::whereIn(
                'event_id',
                Event::where('organizer_id', $user->id)->pluck('id')
            )
                ->where('status', 'paid')
                ->sum('final_amount'),
        ];

        $votingStats = [
            'total_contests' => VotingContest::where('organizer_id', $user->id)->count(),
            'active_contests' => VotingContest::where('organizer_id', $user->id)
                ->where('is_active', true)
                ->count(),
            'total_votes' => Vote::whereIn(
                'voting_contest_id',
                VotingContest::where('organizer_id', $user->id)->pluck('id')
            )
                ->count(),
        ];

        // Recent activity
        $recentEvents = Event::where('organizer_id', $user->id)
            ->withCount(['ticketPurchases as paid_tickets' => function ($query) {
                $query->where('status', 'paid');
            }])
            ->latest()
            ->take(5)
            ->get();

        $recentVoting = VotingContest::where('organizer_id', $user->id)
            ->withCount('votes')
            ->latest()
            ->take(5)
            ->get();

        return view('organizer.analytics.index', [
            'eventStats' => $eventStats,
            'votingStats' => $votingStats,
            'recentEvents' => $recentEvents,
            'recentVoting' => $recentVoting,
            'title' => 'Analytics Dashboard - EventSphere'
        ]);
    }

    public function eventAnalytics(Event $event)
    {

        $ticketSales = TicketPurchase::where('event_id', $event->id)
            ->where('status', 'paid')
            ->selectRaw('DATE(created_at) as date, SUM(quantity) as tickets_sold, SUM(final_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $ticketTypes = \App\Models\Ticket::where('event_id', $event->id)
            ->withCount(['purchases as tickets_sold' => function ($query) {
                $query->where('status', 'paid');
            }])
            ->get();

        $attendeeDemographics = TicketPurchase::where('event_id', $event->id)
            ->where('status', 'paid')
            ->with('user')
            ->get()
            ->groupBy('user.city')
            ->map(function ($group, $city) {
                return [
                    'city' => $city ?: 'Unknown',
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('count')
            ->take(10);

        return view('organizer.analytics.event', [
            'event' => $event,
            'ticketSales' => $ticketSales,
            'ticketTypes' => $ticketTypes,
            'attendeeDemographics' => $attendeeDemographics,
            'title' => "Analytics for {$event->name} - EventSphere"
        ]);
    }

    public function votingAnalytics(VotingContest $contest)
    {

        $voteDistribution = $contest->nominees()
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->get();

        $votesOverTime = Vote::where('voting_contest_id', $contest->id)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as votes_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('organizer.analytics.voting', [
            'contest' => $contest,
            'voteDistribution' => $voteDistribution,
            'votesOverTime' => $votesOverTime,
            'title' => "Analytics for {$contest->title} - EventSphere"
        ]);
    }
}
