<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\VotingContest;
use App\Models\Organizer;
use App\Models\OrganizerProfile;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return view('search.index', [
                'query' => '',
                'results' => [],
                'title' => 'Search - EventSphere'
            ]);
        }

        $events = Event::with(['organizer', 'category'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('location', 'like', "%{$query}%");
            })
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->get();

        $voting = VotingContest::with(['category', 'organizer'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->where(function ($q) {
                $q->where('end_date', '>=', now())
                    ->orWhereNull('end_date');
            })
            ->orderBy('end_date', 'asc')
            ->get();

        $organizers = OrganizerProfile::with(['user'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->get();

        return view('search.index', [
            'query' => $query,
            'results' => [
                'events' => $events,
                'voting' => $voting,
                'organizers' => $organizers,
            ],
            'title' => "Search Results for '{$query}' - EventSphere"
        ]);
    }
}
