<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['organizer', 'tickets', 'category'])
            ->where('is_active', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->paginate(12);

        $categories = EventCategory::where('is_active', true)->get();

        return view('events.index', [
            'events' => $events,
            'categories' => $categories,
            'title' => 'Discover Events - EventSphere'
        ]);
    }

    public function byCategory(EventCategory $category)
    {
        $events = Event::with(['organizer', 'tickets'])
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->paginate(12);

        $categories = EventCategory::where('is_active', true)->get();

        return view('events.index', [
            'events' => $events,
            'category' => $category,
            'categories' => $categories,
            'title' => "{$category->name} Events - EventSphere"
        ]);
    }

    public function show(Event $event)
    {
        if (!$event->is_active && !Gate::allows('view_inactive_events')) {
            abort(404);
        }

        $event->load(['organizer', 'tickets', 'category']);

        return view('events.show', [
            'event' => $event,
            'title' => "{$event->title} - EventSphere"
        ]);
    }

    public function tickets(Event $event)
    {
        if (!$event->is_active) {
            abort(404);
        }

        $event->load(['tickets', 'seatingPlan']);

        return view('events.tickets', [
            'event' => $event,
            'title' => "Tickets for {$event->name} - EventSphere"
        ]);
    }

    public function featured()
    {
        $events = Event::with(['organizer', 'tickets'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(8)
            ->get();

        return response()->json($events);
    }
}
