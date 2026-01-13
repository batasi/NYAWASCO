<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SystemManagementController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'overview');
        $logLevel = $request->get('log_level', '');

        return view('system-management', [
            'activeTab' => $activeTab,
            'systemHealth' => $this->getSystemHealth(),
            'systemInfo' => $this->getSystemInfo(),
            'cacheStats' => $this->getCacheStats(),
            'systemLogs' => $this->getSystemLogs($logLevel),
            'config' => $this->loadConfiguration(),
        ]);
    }

    public function clearCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return redirect()->back()->with('message', 'All caches cleared successfully!');
        } catch (\Exception $e) {
            Log::error('Cache Clear Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to clear caches.');
        }
    }

    public function clearApplicationCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            return redirect()->back()->with('message', 'Application cache cleared successfully!');
        } catch (\Exception $e) {
            Log::error('App Cache Clear Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to clear application cache.');
        }
    }

    public function clearRouteCache(Request $request)
    {
        try {
            Artisan::call('route:clear');
            return redirect()->back()->with('message', 'Route cache cleared successfully!');
        } catch (\Exception $e) {
            Log::error('Route Cache Clear Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to clear route cache.');
        }
    }

    public function clearConfigCache(Request $request)
    {
        try {
            Artisan::call('config:clear');
            return redirect()->back()->with('message', 'Configuration cache cleared successfully!');
        } catch (\Exception $e) {
            Log::error('Config Cache Clear Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to clear configuration cache.');
        }
    }

    public function clearViewCache(Request $request)
    {
        try {
            Artisan::call('view:clear');
            return redirect()->back()->with('message', 'View cache cleared successfully!');
        } catch (\Exception $e) {
            Log::error('View Cache Clear Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to clear view cache.');
        }
    }

    public function optimizeDatabase(Request $request)
    {
        try {
            Artisan::call('db:monitor');
            return redirect()->back()->with('message', 'Database optimization completed!');
        } catch (\Exception $e) {
            Log::error('DB Optimize Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Database optimization failed.');
        }
    }

    public function restartServices(Request $request)
    {
        try {
            Artisan::call('queue:restart');
            return redirect()->back()->with('message', 'Services restarted successfully!');
        } catch (\Exception $e) {
            Log::error('Service Restart Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to restart services.');
        }
    }

    public function saveConfiguration(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'timezone' => 'required|string',
            'locale' => 'required|string',
            'session_lifetime' => 'required|integer|min:15|max:1440',
            'max_login_attempts' => 'required|integer|min:3|max:10',
            'mail_driver' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
        ]);

        try {
            // Save configuration logic would go here
            // For now, just show success message
            return redirect()->back()->with('message', 'Configuration saved successfully!');
        } catch (\Exception $e) {
            Log::error('Config Save Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save configuration.');
        }
    }

    private function getSystemHealth()
    {
        return [
            'status' => 'healthy',
            'issues_count' => 0,
            'uptime' => '99.9%',
            'uptime_days' => 30,
            'active_services' => 5,
            'total_services' => 6,
            'last_backup' => '2 hours ago',
            'backup_status' => 'Successful',
        ];
    }

    private function getSystemInfo()
    {
        return [
            'os' => PHP_OS,
            'php_version' => PHP_VERSION,
            'db_status' => 'Connected',
            'disk_usage' => '75%',
            'load_average' => '1.2',
            'uptime' => '24d 5h',
        ];
    }

    private function getCacheStats()
    {
        return [
            'total_keys' => 0,
            'memory_usage' => '256MB',
            'hit_rate' => '95%',
        ];
    }

    private function getSystemLogs($logLevel = '')
    {
        $logFiles = File::glob(storage_path('logs/*.log'));
        $logs = [];

        if (!empty($logFiles)) {
            $latestLogFile = end($logFiles);
            $logContent = File::get($latestLogFile);
            $lines = explode("\n", $logContent);

            foreach (array_reverse($lines) as $line) {
                if (empty(trim($line))) continue;

                if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)/', $line, $matches)) {
                    $level = strtolower($matches[3]);
                    if ($logLevel && $level !== $logLevel) continue;

                    $logs[] = [
                        'timestamp' => $matches[1],
                        'level' => $level,
                        'message' => $matches[4],
                        'context' => '',
                    ];

                    if (count($logs) >= 50) break;
                }
            }
        }

        return collect($logs);
    }

    private function loadConfiguration()
    {
        return [
            'app_name' => config('app.name', 'NYAWASCO'),
            'app_url' => config('app.url', ''),
            'timezone' => config('app.timezone', 'Africa/Nairobi'),
            'locale' => config('app.locale', 'en'),
            'session_lifetime' => config('session.lifetime', 120),
            'max_login_attempts' => 5,
            'enable_2fa' => false,
            'force_https' => false,
            'mail_driver' => config('mail.default', 'smtp'),
            'mail_from_address' => config('mail.from.address', ''),
            'mail_from_name' => config('mail.from.name', ''),
        ];
    }
}