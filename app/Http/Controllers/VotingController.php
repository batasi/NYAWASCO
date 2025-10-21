<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VotingContest;
use App\Models\VotingCategory;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class VotingController extends Controller
{
    // List active contests (existing code, preserved and slightly cleaned)
    public function index()
    {
        $contests = VotingContest::with(['nominees', 'category', 'organizer'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('end_date', '>=', now())
                      ->orWhereNull('end_date');
            })
            ->orderBy('end_date', 'asc')
            ->paginate(12);

        $categories = VotingCategory::where('is_active', true)->get();

        return view('voting.index', [
            'contests' => $contests,
            'categories' => $categories,
            'title' => 'Active Voting Contests - EventSphere'
        ]);
    }

    // Filter by category
    public function byCategory(VotingCategory $category)
    {
        $contests = VotingContest::with(['nominees', 'organizer'])
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('end_date', '>=', now())
                      ->orWhereNull('end_date');
            })
            ->orderBy('end_date', 'asc')
            ->paginate(12);

        return view('voting.index', [
            'contests' => $contests,
            'category' => $category,
            'title' => "{$category->name} Voting - EventSphere"
        ]);
    }

    // Show one contest
    public function show(VotingContest $contest)
    {
        if (!$contest->is_active && !Gate::allows('view_inactive_voting')) {
            abort(404);
        }

        $contest->load(['nominees', 'category', 'organizer']);

        $userVote = null;
        if (Auth::check()) {
            $userVote = Vote::where('user_id', Auth::id())
                ->where('voting_contest_id', $contest->id)
                ->first();
        }

        return view('voting.show', [
            'contest' => $contest,
            'userVote' => $userVote,
            'title' => "{$contest->title} - EventSphere"
        ]);
    }

    // Cast a vote
    public function vote(Request $request, VotingContest $contest)
    {
        if (!$contest->is_active) {
            return back()->with('error', 'This voting contest is no longer active.');
        }

        if ($contest->end_date && $contest->end_date->isPast()) {
            return back()->with('error', 'Voting has ended for this contest.');
        }

        $validated = $request->validate([
            'nominee_id' => 'required|exists:nominees,id',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to vote.');
        }

        // Enforce per-user vote limit for this contest
        $userVotesCount = Vote::where('user_id', Auth::id())
            ->where('voting_contest_id', $contest->id)
            ->count();

        if ($contest->max_votes_per_user && $userVotesCount >= $contest->max_votes_per_user) {
            return back()->with('error', 'You have reached the maximum number of votes for this contest.');
        }

        // Optional: prevent duplicate same-nominee votes (if desired)
        $existingSameNominee = Vote::where('user_id', Auth::id())
            ->where('voting_contest_id', $contest->id)
            ->where('nominee_id', $validated['nominee_id'])
            ->first();

        if ($existingSameNominee) {
            return back()->with('error', 'You have already voted for this nominee in this contest.');
        }

        // Create vote
        Vote::create([
            'user_id' => Auth::id(),
            'voting_contest_id' => $contest->id,
            'nominee_id' => $validated['nominee_id'],
            'voted_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        // Update cached counters (nominee and contest). Use DB events or queue in production.
        $contest->increment('total_votes');

        // If nominee model has votes_count cached, increment it via relation
        // $nominee = $contest->nominees()->where('id', $validated['nominee_id'])->first();
        // if ($nominee) { $nominee->increment('votes_count'); }

        return back()->with('success', 'Your vote has been cast successfully!');
    }

    // Show votes by the authenticated user
    public function myVotes()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $votes = Vote::with(['contest', 'nominee'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('voting.my-votes', [
            'votes' => $votes,
            'title' => 'My Votes - EventSphere'
        ]);
    }

    // Featured contests JSON (keeps original behavior)
    public function featured()
    {
        $contests = VotingContest::with(['nominees', 'category'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->where(function ($query) {
                $query->where('end_date', '>=', now())
                      ->orWhereNull('end_date');
            })
            ->orderBy('end_date', 'asc')
            ->take(6)
            ->get();

        return response()->json($contests);
    }

    // ---------------------------
    // Admin / Organizer actions
    // ---------------------------

    // Show create form (organizers/admins only)
    public function create()
    {
        if (!Auth::check() || !Gate::allows('create_voting')) {
            abort(403);
        }

        $categories = VotingCategory::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('voting.create', [
            'categories' => $categories,
            'title' => 'Create Voting Contest'
        ]);
    }

    // Store contest (organizers/admins only)
    public function store(Request $request)
    {
        if (!Auth::check() || !Gate::allows('create_voting')) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:voting_categories,id',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_votes_per_user' => 'nullable|integer|min:1',
            'requires_approval' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $contest = VotingContest::create([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? now(),
            'end_date' => $validated['end_date'] ?? null,
            'max_votes_per_user' => $validated['max_votes_per_user'] ?? 1,
            'requires_approval' => $request->boolean('requires_approval'),
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured', false),
            'total_votes' => 0,
            'views' => 0,
            'organizer_id' => Auth::id(),
        ]);

        return redirect()->route('voting.index')->with('success', "Voting contest '{$contest->title}' created successfully.");
    }

    // Edit form
    public function edit($id)
    {
        if (!Auth::check() || !Gate::allows('edit_voting')) {
            abort(403);
        }

        $contest = VotingContest::findOrFail($id);
        $categories = VotingCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('voting.edit', compact('contest', 'categories'));
    }

    // Update contest
    public function update(Request $request, $id)
    {
        if (!Auth::check() || !Gate::allows('edit_voting')) {
            abort(403);
        }

        $contest = VotingContest::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:voting_categories,id',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_votes_per_user' => 'nullable|integer|min:1',
            'requires_approval' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $contest->update([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? $contest->description,
            'start_date' => $validated['start_date'] ?? $contest->start_date,
            'end_date' => $validated['end_date'] ?? $contest->end_date,
            'max_votes_per_user' => $validated['max_votes_per_user'] ?? $contest->max_votes_per_user,
            'requires_approval' => $request->boolean('requires_approval'),
            'is_active' => $request->boolean('is_active', $contest->is_active),
            'is_featured' => $request->boolean('is_featured', $contest->is_featured),
        ]);

        return redirect()->route('voting.index')->with('success', 'Contest updated successfully.');
    }

    // Delete contest
    public function destroy($id)
    {
        if (!Auth::check() || !Gate::allows('delete_voting')) {
            abort(403);
        }

        $contest = VotingContest::findOrFail($id);
        $contest->delete();

        return redirect()->route('voting.index')->with('success', 'Contest deleted successfully.');
    }
}
