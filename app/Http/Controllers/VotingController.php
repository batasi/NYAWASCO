<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VotingContest;
use App\Models\VotingCategory;
use App\Models\Vote;
use App\Models\Nominee;
use App\Models\NomineeCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class VotingController extends Controller
{
    # ---------------------------
    # Public Views
    # ---------------------------

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

    public function show(VotingContest $contest)
    {
        $contest->load(['nominees.nomineeCategory', 'category', 'organizer']);

        $userVote = null;
        if (Auth::check()) {
            $userVote = Vote::where('user_id', Auth::id())
                ->where('voting_contest_id', $contest->id)
                ->first();
        }

        $autoNominee = null;
        if (request()->has('code')) {
            $autoNominee = $contest->nominees()->where('code', request('code'))->first();
        }

        $groupedNominees = $contest->nominees
            ->groupBy(fn($nominee) => optional($nominee->nomineeCategory)->name ?? 'Uncategorized');

        return view('voting.show', [
            'contest' => $contest,
            'groupedNominees' => $groupedNominees,
            'autoNominee' => $autoNominee,
            'userVote' => $userVote,
            'title' => "{$contest->title} - Javent",
        ]);
    }

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

        $userVotesCount = Vote::where('user_id', Auth::id())
            ->where('voting_contest_id', $contest->id)
            ->count();

        if ($contest->max_votes_per_user && $userVotesCount >= $contest->max_votes_per_user) {
            return back()->with('error', 'You have reached the maximum number of votes for this contest.');
        }

        $existingSameNominee = Vote::where('user_id', Auth::id())
            ->where('voting_contest_id', $contest->id)
            ->where('nominee_id', $validated['nominee_id'])
            ->first();

        if ($existingSameNominee) {
            return back()->with('error', 'You have already voted for this nominee in this contest.');
        }

        Vote::create([
            'user_id' => Auth::id(),
            'voting_contest_id' => $contest->id,
            'nominee_id' => $validated['nominee_id'],
            'voted_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        $contest->increment('total_votes');

        return back()->with('success', 'Your vote has been cast successfully!');
    }

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

    # ---------------------------
    # Organizer/Admin Functions
    # ---------------------------

    public function create()
    {
        $categories = VotingCategory::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        $nomineeCategories = NomineeCategory::all();

        return view('voting.create', [
            'categories' => $categories,
            'nomineeCategories' => $nomineeCategories,
            'title' => 'Create Voting Contest'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:voting_categories,id',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_votes_per_user' => 'nullable|integer|min:1',
            'max_votes_option' => 'nullable|in:limited,unlimited',
            'amount' => 'nullable|numeric|min:0',
            'featured_image' => 'nullable|image|max:2048',
            'requires_approval' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'nominees.*.name' => 'nullable|string|max:255',
            'nominees.*.photo' => 'nullable|image|max:2048',
            'nominees.*.description' => 'nullable|string|max:500',
            'nominees.*.category_id' => 'required|exists:nominee_categories,id',
        ]);

        # Handle contest photo
        $photoPath = null;
        if ($request->hasFile('featured_image')) {
            $photoPath = $request->file('featured_image')->store('featured_images', 'public');
        }

        # Set max votes
        $maxVotes = ($validated['votes_limit_type'] ?? 'limited') === 'limited'
        ? $validated['max_votes_per_user'] ?? 1
        : null;


        # Create contest
        $contest = VotingContest::create([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? '',
            'start_date' => $validated['start_date'] ?? now(),
            'end_date' => $validated['end_date'] ?? null,
            'max_votes_per_user' => $maxVotes,
            'amount' => $validated['amount'] ?? 0,
            'photo' => $photoPath,
            'requires_approval' => $request->boolean('requires_approval'),
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured', false),
            'total_votes' => 0,
            'views_count' => 0,
            'organizer_id' => Auth::id(),
        ]);

        # Create nominees
        if ($request->has('nominees')) {
            foreach ($request->nominees as $nomineeData) {
                if (!empty($nomineeData['name'])) {
                    $nomineePhotoPath = null;
                    if (isset($nomineeData['photo']) && $nomineeData['photo'] instanceof \Illuminate\Http\UploadedFile) {
                        $nomineePhotoPath = $nomineeData['photo']->store('nominee_photos', 'public');
                    }

                    Nominee::create([
                        'voting_contest_id' => $contest->id,
                        'name' => $nomineeData['name'],
                        'photo' => $nomineePhotoPath,
                        'description' => $nomineeData['description'] ?? '',
                        'category_id' => $nomineeData['category_id'],
                        'votes_count' => 0,
                    ]);
                }
            }
        }

        return redirect()->route('voting.index')
            ->with('success', "Voting contest '{$contest->title}' created successfully with nominees.");
    }

    public function edit($id)
    {
        $contest = VotingContest::findOrFail($id);
        $categories = VotingCategory::where('is_active', true)->orderBy('sort_order')->get();
        $nomineeCategories = NomineeCategory::all();

        return view('voting.edit', compact('contest', 'categories', 'nomineeCategories'));
    }

    public function update(Request $request, $id)
    {
        $contest = VotingContest::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:voting_categories,id',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_votes_per_user' => 'nullable|integer|min:1',
            'max_votes_option' => 'required|in:limited,unlimited',
            'amount' => 'nullable|numeric|min:0',
            'featured_image' => 'nullable|image|max:2048',
            'requires_approval' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        # Handle contest photo
        if ($request->hasFile('featured_image')) {
            $contest->photo = $request->file('featured_image')->store('featured_images', 'public');
        }

        # Set max votes
        $contest->max_votes_per_user = $validated['max_votes_option'] === 'limited'
            ? $validated['max_votes_per_user'] ?? 1
            : null;

        $contest->update([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? $contest->description,
            'start_date' => $validated['start_date'] ?? $contest->start_date,
            'end_date' => $validated['end_date'] ?? $contest->end_date,
            'amount' => $validated['amount'] ?? $contest->amount,
            'requires_approval' => $request->boolean('requires_approval'),
            'is_active' => $request->boolean('is_active', $contest->is_active),
            'is_featured' => $request->boolean('is_featured', $contest->is_featured),
        ]);

        return redirect()->route('voting.index')->with('success', 'Contest updated successfully.');
    }

    public function destroy($id)
    {
        $contest = VotingContest::findOrFail($id);
        $contest->delete();

        return redirect()->route('voting.index')->with('success', 'Contest deleted successfully.');
    }
}
