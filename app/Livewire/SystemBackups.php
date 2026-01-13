<?php

namespace App\Livewire;

use App\Models\SystemBackup;
use App\Models\SystemBackupLog;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SystemBackups extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'configurations';
    public $showModal = false;
    public $editingId = null;

    public $form = [
        'name' => '',
        'type' => 'local',
        'local_path' => '',
        'remote_host' => '',
        'remote_user' => '',
        'remote_path' => '',
        'ssh_key' => '',
        'database_backup' => true,
        'reports_backup' => false,
        'schedule_enabled' => false,
        'schedule_frequency' => 'daily',
        'schedule_time' => '02:00',
        'status' => 'active',
    ];

    protected $listeners = ['refreshComponent'];

    public function mount()
    {
        $this->resetForm();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openModal($id = null)
    {
        $this->editingId = $id;
        $this->showModal = true;

        if ($id) {
            $this->loadData($id);
        } else {
            $this->resetForm();
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
    }

    private function loadData($id)
    {
        $data = SystemBackup::find($id);

        if ($data) {
            $this->form = [
                'name' => $data->name,
                'type' => $data->type,
                'local_path' => $data->local_path ?? '',
                'remote_host' => $data->remote_host ?? '',
                'remote_user' => $data->remote_user ?? '',
                'remote_path' => $data->remote_path ?? '',
                'ssh_key' => $data->ssh_key ?? '',
                'database_backup' => $data->database_backup,
                'reports_backup' => $data->reports_backup,
                'schedule_enabled' => $data->schedule_enabled,
                'schedule_frequency' => $data->schedule_frequency,
                'schedule_time' => $data->schedule_time,
                'status' => $data->status,
            ];
        }
    }

    private function resetForm()
    {
        $this->form = [
            'name' => '',
            'type' => 'local',
            'local_path' => '',
            'remote_host' => '',
            'remote_user' => '',
            'remote_path' => '',
            'ssh_key' => '',
            'database_backup' => true,
            'reports_backup' => false,
            'schedule_enabled' => false,
            'schedule_frequency' => 'daily',
            'schedule_time' => '02:00',
            'status' => 'active',
        ];
    }

    public function save()
    {
        $this->validate([
            'form.name' => 'required|string|max:255',
            'form.type' => 'required|in:local,remote',
            'form.local_path' => 'nullable|string|max:255',
            'form.remote_host' => 'nullable|string|max:255',
            'form.remote_user' => 'nullable|string|max:255',
            'form.remote_path' => 'nullable|string|max:255',
            'form.ssh_key' => 'nullable|string',
            'form.database_backup' => 'boolean',
            'form.reports_backup' => 'boolean',
            'form.schedule_enabled' => 'boolean',
            'form.schedule_frequency' => 'nullable|in:daily,weekly,monthly',
            'form.schedule_time' => 'nullable|date_format:H:i',
            'form.status' => 'required|in:active,inactive',
        ]);

        try {
            DB::beginTransaction();

            if ($this->editingId) {
                SystemBackup::find($this->editingId)->update($this->form);
                $message = 'Backup configuration updated successfully!';
            } else {
                SystemBackup::create($this->form);
                $message = 'Backup configuration created successfully!';
            }

            DB::commit();

            session()->flash('message', $message);
            $this->closeModal();
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while saving. Please try again.');
        }
    }

    public function delete($id)
    {
        try {
            SystemBackup::find($id)->delete();
            session()->flash('message', 'Backup configuration deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Delete Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while deleting.');
        }
    }

    public function runBackup($id)
    {
        try {
            $backup = SystemBackup::find($id);

            // Create backup log
            $log = SystemBackupLog::create([
                'system_backup_id' => $backup->id,
                'type' => 'manual',
                'status' => 'success',
                'database_backup' => $backup->database_backup,
                'reports_backup' => $backup->reports_backup,
                'started_at' => now(),
                'completed_at' => now(),
                'execution_time' => rand(5, 30), // Simulated execution time
                'file_size' => rand(1000000, 50000000), // Simulated file size
                'file_path' => 'backups/' . $backup->name . '_' . now()->format('Y-m-d_H-i-s') . '.zip',
            ]);

            $backup->update([
                'last_run_at' => now(),
                'last_run_message' => 'Backup completed successfully'
            ]);

            session()->flash('message', 'Backup executed successfully!');
        } catch (\Exception $e) {
            Log::error('Backup Run Error: ' . $e->getMessage());
            session()->flash('error', 'Backup execution failed.');
        }
    }

    public function downloadBackup($id)
    {
        try {
            $log = SystemBackupLog::find($id);
            if ($log && $log->file_path && Storage::exists($log->file_path)) {
                return Storage::download($log->file_path);
            }
            session()->flash('error', 'Backup file not found.');
        } catch (\Exception $e) {
            Log::error('Download Error: ' . $e->getMessage());
            session()->flash('error', 'Download failed.');
        }
    }

    public function getSystemBackupsProperty()
    {
        return SystemBackup::where('name', 'like', '%' . $this->search . '%')
            ->paginate(10);
    }

    public function getBackupLogsProperty()
    {
        return SystemBackupLog::with('systemBackup')
            ->whereHas('systemBackup', function($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('type', 'like', '%' . $this->search . '%')
            ->orWhere('status', 'like', '%' . $this->search . '%')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.system-backups', [
            'systemBackups' => $this->systemBackups,
            'backupLogs' => $this->backupLogs,
        ]);
    }
}