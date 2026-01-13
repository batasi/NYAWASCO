<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemBackupConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'local_path',
        'remote_host',
        'remote_user',
        'remote_path',
        'ssh_key',
        'database_backup',
        'reports_backup',
        'schedule_enabled',
        'schedule_frequency',
        'schedule_time',
        'status',
    ];

    protected $casts = [
        'database_backup' => 'boolean',
        'reports_backup' => 'boolean',
        'schedule_enabled' => 'boolean',
    ];

    public function backupLogs(): HasMany
    {
        return $this->hasMany(SystemBackupLog::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>',
            'inactive' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Unknown</span>',
        };
    }

    public function getTypeBadgeAttribute(): string
    {
        return match($this->type) {
            'local' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Local</span>',
            'remote' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Remote</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Unknown</span>',
        };
    }

    public function getScheduleInfoAttribute(): string
    {
        if (!$this->schedule_enabled) {
            return 'Manual only';
        }

        return ucfirst($this->schedule_frequency) . ' at ' . $this->schedule_time;
    }
}