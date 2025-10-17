<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\VotingContest;

class HomeController extends Controller
{
    public function index()
    {
        $featuredEvents = Event::with(['organizer', 'tickets'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(6)
            ->get();

        $activeVoting = VotingContest::with(['nominees', 'category'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('end_date', '>=', now())
                    ->orWhereNull('end_date');
            })
            ->orderBy('end_date', 'asc')
            ->take(4)
            ->get();

        return view('welcome', [
            'featuredEvents' => $featuredEvents,
            'activeVoting' => $activeVoting,
            'title' => 'EventSphere - Your Event Universe'
        ]);
    }
}
