<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemBackup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
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
        'file_path',
        'file_size',
        'backup_type',
        'created_by',
        'completed_at',
        'error_message',
        'last_run_at',
        'last_run_message',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'file_size' => 'integer',
        'last_run_at' => 'datetime',
        'database_backup' => 'boolean',
        'reports_backup' => 'boolean',
        'schedule_enabled' => 'boolean',
    ];

    const BACKUP_TYPES = [
        'full' => 'Full System Backup',
        'database' => 'Database Only',
        'files' => 'Files Only',
        'incremental' => 'Incremental Backup',
    ];

    const STATUSES = [
        'pending' => 'Pending',
        'running' => 'Running',
        'completed' => 'Completed',
        'failed' => 'Failed',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('backup_type', $type);
    }

    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return 'N/A';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->file_size;
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getBackupTypeLabelAttribute()
    {
        return self::BACKUP_TYPES[$this->backup_type] ?? 'Unknown';
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }
}