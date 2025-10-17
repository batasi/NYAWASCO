<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\VotingContest;

class VotingFeed extends Component
{
    public $contests;
    public $perPage = 6;

    protected $listeners = ['loadMoreContests' => 'loadMore'];

    public function mount()
    {
        $this->loadContests();
    }

    public function loadContests()
    {
        $this->contests = VotingContest::with(['nominees', 'category'])
            ->where(function ($query) {
                $query->where('end_date', '>=', now())
                    ->orWhereNull('end_date');
            })
            ->where('is_active', true)
            ->orderBy('end_date', 'asc')
            ->take($this->perPage)
            ->get();
    }

    public function loadMore()
    {
        $this->perPage += 6;
        $this->loadContests();
    }

    public function render()
    {
        return view('livewire.voting-feed');
    }
}
