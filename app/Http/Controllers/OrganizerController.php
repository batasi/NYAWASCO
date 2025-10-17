<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\VotingContest;
use App\Models\TicketPurchase;
use App\Models\EventCategory;
use App\Models\VotingCategory;
use App\Models\Nominee;
use App\Models\Vote;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class OrganizerController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:organizer')->except(['index', 'show']);
    }

    public function index()
    {
        $organizers = User::where('role', 'organizer')
            ->where('is_active', true)
            ->withCount(['organizedEvents', 'organizedVotingContests'])
            ->paginate(12);

        return view('organizers.index', [
            'organizers' => $organizers,
            'title' => 'Event Organizers - EventSphere'
        ]);
    }

    public function show($id)
    {
        $organizer = User::where('role', 'organizer')
            ->where('is_active', true)
            ->with(['profile'])
            ->withCount(['organizedEvents', 'organizedVotingContests'])
            ->findOrFail($id);

        $events = Event::where('organizer_id', $organizer->id)
            ->where('is_active', true)
            ->where('start_date', '>=', now())
            ->with('category')
            ->orderBy('start_date')
            ->paginate(6);

        $votingContests = VotingContest::where('organizer_id', $organizer->id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('end_date', '>=', now())
                    ->orWhereNull('end_date');
            })
            ->with('category')
            ->orderBy('end_date', 'asc')
            ->paginate(6);

        return view('organizers.show', [
            'organizer' => $organizer,
            'events' => $events,
            'votingContests' => $votingContests,
            'title' => "{$organizer->name} - EventSphere Organizer"
        ]);
    }

    public function dashboard()
    {
        $user = Auth::user();

        $stats = [
            'total_events' => Event::where('organizer_id', $user->id)->count(),
            'active_events' => Event::where('organizer_id', $user->id)
                ->where('is_active', true)
                ->where('start_date', '>=', now())
                ->count(),
            'total_voting_contests' => VotingContest::where('organizer_id', $user->id)->count(),
            'active_voting_contests' => VotingContest::where('organizer_id', $user->id)
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->where('end_date', '>=', now())
                        ->orWhereNull('end_date');
                })
                ->count(),
            'total_ticket_sales' => TicketPurchase::whereIn(
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

        $recentTicketSales = TicketPurchase::with(['event', 'user'])
            ->whereIn('event_id', Event::where('organizer_id', $user->id)->pluck('id'))
            ->where('status', 'paid')
            ->latest()
            ->take(10)
            ->get();

        $upcomingEvents = Event::where('organizer_id', $user->id)
            ->where('is_active', true)
            ->where('start_date', '>=', now())
            ->with('category')
            ->orderBy('start_date')
            ->take(5)
            ->get();

        return view('organizer.dashboard', [
            'stats' => $stats,
            'recentTicketSales' => $recentTicketSales,
            'upcomingEvents' => $upcomingEvents,
            'title' => 'Organizer Dashboard - EventSphere'
        ]);
    }

    // Event Management Methods
    public function events()
    {
        $events = Event::where('organizer_id', Auth::id())
            ->with(['category', 'tickets'])
            ->latest()
            ->paginate(10);

        return view('organizer.events.index', [
            'events' => $events,
            'title' => 'My Events - EventSphere'
        ]);
    }

    public function createEvent()
    {

        $this->authorize('create', Event::class);

        $categories = EventCategory::where('is_active', true)->get();

        return view('organizer.events.create', [
            'categories' => $categories,
            'title' => 'Create New Event - EventSphere'
        ]);
    }

    public function storeEvent(Request $request)
    {

        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:event_categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'featured_image' => 'required|image|max:2048',
            'venue_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'registration_start' => 'nullable|date',
            'registration_end' => 'nullable|date|after:registration_start',
            'max_attendees' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'tags' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('events', 'public');
            $validated['featured_image'] = $path;
        }

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
        $validated['organizer_id'] = Auth::id();
        $validated['currency'] = 'USD';
        $validated['status'] = 'draft';

        if ($request->has('tags')) {
            $validated['tags'] = json_encode(explode(',', $request->tags));
        }

        $event = Event::create($validated);

        return redirect()->route('organizer.events.edit', $event)
            ->with('success', 'Event created successfully! You can now add tickets and other details.');
    }

    public function editEvent(Event $event)
    {
        $this->authorize('update', $event);

        $categories = EventCategory::where('is_active', true)->get();

        return view('organizer.events.edit', [
            'event' => $event,
            'categories' => $categories,
            'title' => "Edit {$event->name} - EventSphere"
        ]);
    }

    public function updateEvent(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:event_categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|max:2048',
            'venue_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_start' => 'nullable|date',
            'registration_end' => 'nullable|date|after:registration_start',
            'max_attendees' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'is_active' => 'boolean',
            'status' => Rule::in(['draft', 'published', 'cancelled']),
            'tags' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('events', 'public');
            $validated['featured_image'] = $path;
        }

        if ($request->has('tags')) {
            $validated['tags'] = json_encode(explode(',', $request->tags));
        }

        $event->update($validated);

        return back()->with('success', 'Event updated successfully!');
    }

    public function destroyEvent(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()->route('organizer.events')
            ->with('success', 'Event deleted successfully!');
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

        return view('organizer.voting.create', [
            'categories' => $categories,
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
        $this->authorize('update', $contest);

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
        $this->authorize('update', $contest);

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
        $this->authorize('delete', $contest);

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
        $this->authorize('view', $event);

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
        $this->authorize('view', $contest);

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
