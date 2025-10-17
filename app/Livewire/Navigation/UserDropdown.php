<?php

namespace App\Livewire\Navigation;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserDropdown extends Component
{
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function render()
    {
        return view('livewire.navigation.user-dropdown');
    }
}
