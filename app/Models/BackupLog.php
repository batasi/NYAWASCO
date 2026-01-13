<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'system_backup_id',
        'type', // manual, scheduled
        'status', // success, failed
        'database_backup',
        'reports_backup',
        'file_path',
        'file_size',
        'execution_time',
        'started_at',
        'completed_at',
        'error_message',
    ];

    protected $casts = [
        'database_backup' => 'boolean',
        'reports_backup' => 'boolean',
        'file_size' => 'integer',
        'execution_time' => 'float',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function systemBackup()
    {
        return $this->belongsTo(SystemBackup::class);
    }

    // Accessors
    public function getDurationAttribute()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInSeconds($this->completed_at);
        }
        return null;
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = $this->file_size;
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}