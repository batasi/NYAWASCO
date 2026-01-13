<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class SessionsLogs extends Component
{
    use WithPagination;

    public $search = '';
    public $filterUser = '';
    public $filterAction = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $activeTab = 'sessions';

    public $users = [];
    protected $sessionLogs = [];
    protected $activityLogs = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->users = User::select('id', 'first_name', 'last_name', 'email')->get();

        if ($this->activeTab === 'sessions') {
            $this->loadSessionLogs();
        } elseif ($this->activeTab === 'activity') {
            $this->loadActivityLogs();
        }
    }

    public function loadSessionLogs()
    {
        $query = DB::table('sessions')
            ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
            ->select(
                'sessions.id',
                'sessions.user_id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.first_name',
                'users.last_name',
                'users.email'
            );

        if ($this->search) {
            $query->where(function($q) {
                $q->where('users.first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('users.last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('users.email', 'like', '%' . $this->search . '%')
                  ->orWhere('sessions.ip_address', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterUser) {
            $query->where('sessions.user_id', $this->filterUser);
        }

        if ($this->filterDateFrom) {
            $query->where('sessions.last_activity', '>=', $this->filterDateFrom . ' 00:00:00');
        }

        if ($this->filterDateTo) {
            $query->where('sessions.last_activity', '<=', $this->filterDateTo . ' 23:59:59');
        }

        $this->sessionLogs = $query->orderBy('sessions.last_activity', 'desc')
                                   ->paginate(15);
    }

    public function loadActivityLogs()
    {
        $query = DB::table('activity_logs')
            ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
            ->select(
                'activity_logs.id',
                'activity_logs.user_id',
                'activity_logs.action',
                'activity_logs.description',
                'activity_logs.ip_address',
                'activity_logs.user_agent',
                'activity_logs.created_at',
                'users.first_name',
                'users.last_name',
                'users.email'
            );

        if ($this->search) {
            $query->where(function($q) {
                $q->where('users.first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('users.last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('users.email', 'like', '%' . $this->search . '%')
                  ->orWhere('activity_logs.action', 'like', '%' . $this->search . '%')
                  ->orWhere('activity_logs.description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterUser) {
            $query->where('activity_logs.user_id', $this->filterUser);
        }

        if ($this->filterAction) {
            $query->where('activity_logs.action', $this->filterAction);
        }

        if ($this->filterDateFrom) {
            $query->where('activity_logs.created_at', '>=', $this->filterDateFrom . ' 00:00:00');
        }

        if ($this->filterDateTo) {
            $query->where('activity_logs.created_at', '<=', $this->filterDateTo . ' 23:59:59');
        }

        $this->activityLogs = $query->orderBy('activity_logs.created_at', 'desc')
                                    ->paginate(15);
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->loadData();
    }

    public function updatedSearch()
    {
        $this->resetPage();
        $this->loadData();
    }

    public function updatedFilterUser()
    {
        $this->resetPage();
        $this->loadData();
    }

    public function updatedFilterAction()
    {
        $this->resetPage();
        $this->loadData();
    }

    public function updatedFilterDateFrom()
    {
        $this->resetPage();
        $this->loadData();
    }

    public function updatedFilterDateTo()
    {
        $this->resetPage();
        $this->loadData();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterUser = '';
        $this->filterAction = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->resetPage();
        $this->loadData();
    }

    public function terminateSession($sessionId)
    {
        try {
            DB::table('sessions')->where('id', $sessionId)->delete();

            // Log the action
            Log::info('Session terminated', [
                'session_id' => $sessionId,
                'terminated_by' => auth()->id(),
                'ip' => request()->ip()
            ]);

            session()->flash('message', 'Session terminated successfully.');
            $this->loadData();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to terminate session: ' . $e->getMessage());
        }
    }

    public function getSessionLogsProperty()
    {
        return $this->sessionLogs;
    }

    public function getActivityLogsProperty()
    {
        return $this->activityLogs;
    }

    public function exportLogs()
    {
        // Implementation for exporting logs
        session()->flash('message', 'Export functionality will be implemented.');
    }

    public function render()
    {
        return view('livewire.sessions-logs');
    }
}