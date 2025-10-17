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
        if (!$contest->is_active && !Gate::allows('view_inactive_voting')) {
            abort(404);
        }

        $contest->load(['nominees', 'category', 'organizer']);

        // Check if user has already voted
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

        // Check if user has already voted
        $existingVote = Vote::where('user_id', Auth::id())
            ->where('voting_contest_id', $contest->id)
            ->first();

        if ($existingVote) {
            return back()->with('error', 'You have already voted in this contest.');
        }

        // Create the vote
        Vote::create([
            'user_id' => Auth::id(),
            'voting_contest_id' => $contest->id,
            'nominee_id' => $validated['nominee_id'],
        ]);

        return back()->with('success', 'Your vote has been cast successfully!');
    }

    public function myVotes()
    {
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
}
