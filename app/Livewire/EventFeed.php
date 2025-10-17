<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Event;

class EventFeed extends Component
{
    public $events;
    public $perPage = 6;

    protected $listeners = ['loadMoreEvents' => 'loadMore'];

    public function mount()
    {
        $this->loadEvents();
    }

    public function loadEvents()
    {
        $this->events = Event::with(['organizer', 'tickets'])
            ->where('start_date', '>=', now())
            ->where('is_active', true)
            ->orderBy('start_date')
            ->take($this->perPage)
            ->get();
    }

    public function loadMore()
    {
        $this->perPage += 6;
        $this->loadEvents();
    }

    public function render()
    {
        return view('livewire.event-feed');
    }
}
