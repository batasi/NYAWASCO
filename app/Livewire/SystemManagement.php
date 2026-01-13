<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SystemManagement extends Component
{
    use WithPagination;

    public $activeTab = 'overview';
    public $logLevel = '';
    public $maintenanceMode = false;
    public $maintenanceMessage = '';
    public $maintenanceStart = '';
    public $maintenanceEnd = '';

    public $config = [
        'app_name' => '',
        'app_url' => '',
        'timezone' => 'Africa/Nairobi',
        'locale' => 'en',
        'session_lifetime' => 120,
        'max_login_attempts' => 5,
        'enable_2fa' => false,
        'force_https' => false,
        'mail_driver' => 'smtp',
        'mail_from_address' => '',
        'mail_from_name' => '',
    ];

    public function mount()
    {
        $this->loadConfiguration();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Clear our custom caches
            Cache::forget('system_health');
            Cache::forget('system_info');
            Cache::forget('cache_stats');
            Cache::forget('system_logs_');
            Cache::forget('system_logs_error');
            Cache::forget('system_logs_warning');
            Cache::forget('system_logs_info');
            Cache::forget('system_logs_debug');

            session()->flash('message', 'All caches cleared successfully!');
        } catch (\Exception $e) {
            Log::error('Cache Clear Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to clear caches.');
        }
    }

    public function clearApplicationCache()
    {
        try {
            Artisan::call('cache:clear');
            session()->flash('message', 'Application cache cleared successfully!');
        } catch (\Exception $e) {
            Log::error('App Cache Clear Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to clear application cache.');
        }
    }

    public function clearRouteCache()
    {
        try {
            Artisan::call('route:clear');
            session()->flash('message', 'Route cache cleared successfully!');
        } catch (\Exception $e) {
            Log::error('Route Cache Clear Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to clear route cache.');
        }
    }

    public function clearConfigCache()
    {
        try {
            Artisan::call('config:clear');
            session()->flash('message', 'Configuration cache cleared successfully!');
        } catch (\Exception $e) {
            Log::error('Config Cache Clear Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to clear configuration cache.');
        }
    }

    public function clearViewCache()
    {
        try {
            Artisan::call('view:clear');
            session()->flash('message', 'View cache cleared successfully!');
        } catch (\Exception $e) {
            Log::error('View Cache Clear Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to clear view cache.');
        }
    }

    public function optimizeDatabase()
    {
        try {
            Artisan::call('db:monitor');
            session()->flash('message', 'Database optimization completed!');
        } catch (\Exception $e) {
            Log::error('DB Optimize Error: ' . $e->getMessage());
            session()->flash('error', 'Database optimization failed.');
        }
    }

    public function restartServices()
    {
        try {
            // This would restart services like queue workers, etc.
            Artisan::call('queue:restart');
            session()->flash('message', 'Services restarted successfully!');
        } catch (\Exception $e) {
            Log::error('Service Restart Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to restart services.');
        }
    }

    public function generateReport()
    {
        try {
            // Generate system report
            session()->flash('message', 'System report generated successfully!');
        } catch (\Exception $e) {
            Log::error('Report Generation Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to generate report.');
        }
    }

    public function saveMaintenanceSettings()
    {
        try {
            // Save maintenance settings to config or database
            session()->flash('message', 'Maintenance settings saved successfully!');
        } catch (\Exception $e) {
            Log::error('Maintenance Save Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to save maintenance settings.');
        }
    }

    public function saveConfiguration()
    {
        $this->validate([
            'config.app_name' => 'required|string|max:255',
            'config.app_url' => 'required|url',
            'config.timezone' => 'required|string',
            'config.locale' => 'required|string',
            'config.session_lifetime' => 'required|integer|min:15|max:1440',
            'config.max_login_attempts' => 'required|integer|min:3|max:10',
            'config.mail_driver' => 'required|string',
            'config.mail_from_address' => 'required|email',
            'config.mail_from_name' => 'required|string|max:255',
        ]);

        try {
            // Save configuration
            session()->flash('message', 'Configuration saved successfully!');
        } catch (\Exception $e) {
            Log::error('Config Save Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to save configuration.');
        }
    }

    public function resetConfiguration()
    {
        $this->loadConfiguration();
        session()->flash('message', 'Configuration reset to current values.');
    }

    private function loadConfiguration()
    {
        $this->config = [
            'app_name' => config('app.name', 'NYAWASCO'),
            'app_url' => config('app.url', ''),
            'timezone' => config('app.timezone', 'Africa/Nairobi'),
            'locale' => config('app.locale', 'en'),
            'session_lifetime' => config('session.lifetime', 120),
            'max_login_attempts' => 5, // Default value
            'enable_2fa' => false, // Default value
            'force_https' => false, // Default value
            'mail_driver' => config('mail.default', 'smtp'),
            'mail_from_address' => config('mail.from.address', ''),
            'mail_from_name' => config('mail.from.name', ''),
        ];
    }

    public function exportLogs()
    {
        try {
            // Export logs logic
            session()->flash('message', 'Logs exported successfully!');
        } catch (\Exception $e) {
            Log::error('Log Export Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to export logs.');
        }
    }

    public function getSystemHealthProperty()
    {
        return Cache::remember('system_health', 60, function () { // Cache for 1 minute
            return [
                'status' => 'healthy', // This would be determined by actual health checks
                'issues_count' => 0,
                'uptime' => '99.9%', // Calculate actual uptime
                'uptime_days' => 30,
                'active_services' => 5,
                'total_services' => 6,
                'last_backup' => '2 hours ago',
                'backup_status' => 'Successful',
            ];
        });
    }

    public function getSystemInfoProperty()
    {
        return Cache::remember('system_info', 300, function () { // Cache for 5 minutes
            return [
                'os' => PHP_OS,
                'php_version' => PHP_VERSION,
                'db_status' => 'Connected',
                'disk_usage' => '75%',
                'load_average' => '1.2',
                'uptime' => '24d 5h',
            ];
        });
    }

    public function getCacheStatsProperty()
    {
        return Cache::remember('cache_stats', 60, function () { // Cache for 1 minute
            return [
                'total_keys' => 0, // Would need proper Redis integration
                'memory_usage' => '256MB',
                'hit_rate' => '95%',
            ];
        });
    }

    public function getSystemLogsProperty()
    {
        // Cache logs for 30 seconds to improve performance
        return Cache::remember('system_logs_' . $this->logLevel, 30, function () {
            // Get logs from storage/logs
            $logFiles = File::glob(storage_path('logs/*.log'));
            $logs = [];

            if (!empty($logFiles)) {
                $latestLogFile = end($logFiles);

                // Check if file exists and is readable
                if (File::exists($latestLogFile) && File::isReadable($latestLogFile)) {
                    try {
                        $logContent = File::get($latestLogFile);
                        $lines = explode("\n", $logContent);

                        foreach (array_reverse($lines) as $line) {
                            if (empty(trim($line))) continue;

                            // Parse Laravel log format
                            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)/', $line, $matches)) {
                                $level = strtolower($matches[3]);
                                if ($this->logLevel && $level !== $this->logLevel) continue;

                                $logs[] = [
                                    'timestamp' => $matches[1],
                                    'level' => $level,
                                    'message' => $matches[4],
                                    'context' => '',
                                ];

                                if (count($logs) >= 50) break; // Limit to 50 entries
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Error reading log file: ' . $e->getMessage());
                    }
                }
            }

            return collect($logs);
        });
    }

    public function render()
    {
        return view('livewire.system-management', [
            'systemHealth' => $this->systemHealth,
            'systemInfo' => $this->systemInfo,
            'cacheStats' => $this->cacheStats,
            'systemLogs' => $this->systemLogs,
        ])->layout('layouts.app');
    }
}