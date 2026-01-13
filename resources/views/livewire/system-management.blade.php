
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600">
            <div class="absolute inset-0 bg-black/20"></div>
            <!-- Floating Particles -->
            <div class="absolute inset-0">
                <div class="absolute top-20 left-20 w-2 h-2 bg-white/30 rounded-full animate-pulse"></div>
                <div class="absolute top-40 right-32 w-1 h-1 bg-white/40 rounded-full animate-bounce"></div>
                <div class="absolute bottom-32 left-1/4 w-3 h-3 bg-white/20 rounded-full animate-pulse"></div>
                <div class="absolute top-1/3 right-20 w-2 h-2 bg-white/35 rounded-full animate-bounce"></div>
                <div class="absolute bottom-20 right-1/3 w-1 h-1 bg-white/45 rounded-full animate-pulse"></div>
            </div>
        </div>

        <div class="relative max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 py-12">
            <div class="text-center">
                <div class="flex justify-center mb-7">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-600 rounded-3xl blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-tilt"></div>
                        <div class="relative w-28 h-28 bg-gradient-to-br from-white/95 to-white/85 backdrop-blur-2xl rounded-3xl flex items-center justify-center shadow-2xl transform hover:scale-105 transition-all duration-500">
                            <svg class="w-20 h-20 text-indigo-600 drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <h1 class="text-6xl md:text-7xl font-black text-white mb-8 tracking-tight animate-fade-in-up">
                    System Management
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-orange-300 to-pink-300 animate-pulse">
                        & Control Center
                    </span>
                </h1>
            </div>
        </div>
    </div>

    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- System Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-3xl p-6 text-white">
                <div class="text-3xl font-bold mb-2">{{ $systemHealth['status'] === 'healthy' ? 'Healthy' : 'Issues' }}</div>
                <div class="text-green-100">System Status</div>
                <div class="mt-2 text-sm opacity-90">
                    @if($systemHealth['status'] === 'healthy')
                        All systems operational
                    @else
                        {{ $systemHealth['issues_count'] }} issues detected
                    @endif
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-3xl p-6 text-white">
                <div class="text-3xl font-bold mb-2">{{ $systemHealth['uptime'] }}</div>
                <div class="text-blue-100">System Uptime</div>
                <div class="mt-2 text-sm opacity-90">Last {{ $systemHealth['uptime_days'] }} days</div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-3xl p-6 text-white">
                <div class="text-3xl font-bold mb-2">{{ $systemHealth['active_services'] }}</div>
                <div class="text-purple-100">Active Services</div>
                <div class="mt-2 text-sm opacity-90">Out of {{ $systemHealth['total_services'] }} services</div>
            </div>

            <div class="bg-gradient-to-r from-yellow-500 to-orange-600 rounded-3xl p-6 text-white">
                <div class="text-3xl font-bold mb-2">{{ $systemHealth['last_backup'] }}</div>
                <div class="text-yellow-100">Last Backup</div>
                <div class="mt-2 text-sm opacity-90">{{ $systemHealth['backup_status'] }}</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 overflow-x-auto">
                    <button wire:click="switchTab('overview')"
                            class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'overview' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        System Overview
                    </button>
                    <button wire:click="switchTab('maintenance')"
                            class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'maintenance' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Maintenance Mode
                    </button>
                    <button wire:click="switchTab('cache')"
                            class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'cache' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Cache Management
                    </button>
                    <button wire:click="switchTab('logs')"
                            class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'logs' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        System Logs
                    </button>
                    <button wire:click="switchTab('config')"
                            class="py-2 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'config' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Configuration
                    </button>
                </nav>
            </div>
        </div>

        <!-- System Overview Tab -->
        @if($activeTab === 'overview')
        <div class="space-y-8">
            <!-- System Information -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    System Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-blue-900">Server OS</div>
                                <div class="text-xs text-blue-700">Operating System</div>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-blue-900">{{ $systemInfo['os'] ?? 'Linux' }}</div>
                    </div>

                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-green-900">PHP Version</div>
                                <div class="text-xs text-green-700">Runtime Version</div>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-green-900">{{ $systemInfo['php_version'] ?? PHP_VERSION }}</div>
                    </div>

                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-xl border border-purple-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-purple-900">Database</div>
                                <div class="text-xs text-purple-700">Connection Status</div>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-purple-900">{{ $systemInfo['db_status'] ?? 'Connected' }}</div>
                    </div>

                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 p-6 rounded-xl border border-yellow-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-yellow-900">Storage</div>
                                <div class="text-xs text-yellow-700">Disk Usage</div>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-yellow-900">{{ $systemInfo['disk_usage'] ?? '75%' }}</div>
                    </div>

                    <div class="bg-gradient-to-r from-red-50 to-pink-50 p-6 rounded-xl border border-red-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-red-900">Performance</div>
                                <div class="text-xs text-red-700">Load Average</div>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-red-900">{{ $systemInfo['load_average'] ?? '1.2' }}</div>
                    </div>

                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 rounded-xl border border-indigo-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-indigo-900">Uptime</div>
                                <div class="text-xs text-indigo-700">System Runtime</div>
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-indigo-900">{{ $systemInfo['uptime'] ?? '24d 5h' }}</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Quick Actions
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button wire:click="clearCache"
                            class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white p-4 rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex flex-col items-center">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span class="font-semibold">Clear Cache</span>
                    </button>

                    <button wire:click="optimizeDatabase"
                            class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-4 rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex flex-col items-center">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                        </svg>
                        <span class="font-semibold">Optimize DB</span>
                    </button>

                    <button wire:click="restartServices"
                            class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-4 rounded-xl hover:from-yellow-600 hover:to-orange-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex flex-col items-center">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="font-semibold">Restart Services</span>
                    </button>

                    <button wire:click="generateReport"
                            class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-4 rounded-xl hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex flex-col items-center">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="font-semibold">System Report</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Maintenance Mode Tab -->
        @if($activeTab === 'maintenance')
        <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Maintenance Mode Settings
            </h3>

            <div class="space-y-6">
                <div class="bg-gradient-to-r from-orange-50 to-red-50 p-6 rounded-xl border border-orange-200">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-lg font-semibold text-orange-900">Maintenance Mode</h4>
                            <p class="text-sm text-orange-700">Enable maintenance mode to prevent user access</p>
                        </div>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="maintenanceMode" class="sr-only">
                            <div class="relative">
                                <div class="block bg-gray-600 w-14 h-8 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition transform {{ $maintenanceMode ? 'translate-x-6 bg-orange-500' : '' }}"></div>
                            </div>
                        </label>
                    </div>

                    @if($maintenanceMode)
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-orange-900 mb-2">Maintenance Message</label>
                            <textarea wire:model="maintenanceMessage"
                                      class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl focus:border-orange-500 focus:ring-4 focus:ring-orange-200 transition-all duration-300 bg-white shadow-sm"
                                      rows="3" placeholder="Enter maintenance message..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-orange-900 mb-2">Start Time</label>
                                <input type="datetime-local" wire:model="maintenanceStart"
                                       class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl focus:border-orange-500 focus:ring-4 focus:ring-orange-200 transition-all duration-300 bg-white shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-orange-900 mb-2">End Time</label>
                                <input type="datetime-local" wire:model="maintenanceEnd"
                                       class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl focus:border-orange-500 focus:ring-4 focus:ring-orange-200 transition-all duration-300 bg-white shadow-sm">
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="flex justify-end space-x-4">
                    <button wire:click="saveMaintenanceSettings"
                            class="px-8 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl hover:from-orange-700 hover:to-red-700 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Save Settings
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Cache Management Tab -->
        @if($activeTab === 'cache')
        <div class="space-y-8">
            <!-- Cache Statistics -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Cache Statistics
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
                        <div class="text-3xl font-bold text-blue-900 mb-2">{{ $cacheStats['total_keys'] ?? 0 }}</div>
                        <div class="text-blue-700">Total Cache Keys</div>
                    </div>

                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
                        <div class="text-3xl font-bold text-green-900 mb-2">{{ $cacheStats['memory_usage'] ?? '0MB' }}</div>
                        <div class="text-green-700">Memory Usage</div>
                    </div>

                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-xl border border-purple-200">
                        <div class="text-3xl font-bold text-purple-900 mb-2">{{ $cacheStats['hit_rate'] ?? '95%' }}</div>
                        <div class="text-purple-700">Cache Hit Rate</div>
                    </div>
                </div>
            </div>

            <!-- Cache Management Actions -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Cache Management Actions
            </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button wire:click="clearApplicationCache"
                            class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white p-4 rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex flex-col items-center">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span class="font-semibold text-center">Clear App Cache</span>
                    </button>

                    <button wire:click="clearRouteCache"
                            class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-4 rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex flex-col items-center">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="font-semibold text-center">Clear Route Cache</span>
                    </button>

                    <button wire:click="clearConfigCache"
                            class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-4 rounded-xl hover:from-yellow-600 hover:to-orange-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex flex-col items-center">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="font-semibold text-center">Clear Config Cache</span>
                    </button>

                    <button wire:click="clearViewCache"
                            class="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-4 rounded-xl hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex flex-col items-center">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span class="font-semibold text-center">Clear View Cache</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- System Logs Tab -->
        @if($activeTab === 'logs')
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">System Logs</h3>
                        <p class="text-gray-600 mt-1">View system events and error logs</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <select wire:model.live="logLevel" class="px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 bg-gray-50 focus:bg-white">
                            <option value="">All Levels</option>
                            <option value="emergency">Emergency</option>
                            <option value="alert">Alert</option>
                            <option value="critical">Critical</option>
                            <option value="error">Error</option>
                            <option value="warning">Warning</option>
                            <option value="notice">Notice</option>
                            <option value="info">Info</option>
                            <option value="debug">Debug</option>
                        </select>
                        <button wire:click="exportLogs"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Logs
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Timestamp</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Level</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Message</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Context</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($systemLogs as $log)
                        <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 transition-all duration-200">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $log['timestamp'] }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                    @if($log['level'] === 'emergency') bg-red-100 text-red-800
                                    @elseif($log['level'] === 'alert') bg-red-100 text-red-800
                                    @elseif($log['level'] === 'critical') bg-red-100 text-red-800
                                    @elseif($log['level'] === 'error') bg-red-100 text-red-800
                                    @elseif($log['level'] === 'warning') bg-yellow-100 text-yellow-800
                                    @elseif($log['level'] === 'notice') bg-blue-100 text-blue-800
                                    @elseif($log['level'] === 'info') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($log['level']) }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm text-gray-900 max-w-md truncate" title="{{ $log['message'] }}">
                                    {{ Str::limit($log['message'], 100) }}
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-500 max-w-xs truncate" title="{{ $log['context'] }}">
                                    {{ Str::limit($log['context'], 50) }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-16 text-center">
                                <div class="w-32 h-32 bg-gradient-to-r from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-3xl font-bold text-gray-900 mb-4">No Logs Found</h3>
                                <p class="text-xl text-gray-600 mb-6">System logs will appear here</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
        @endif

        <!-- Configuration Tab -->
        @if($activeTab === 'config')
        <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                System Configuration
            </h3>

            <form wire:submit.prevent="saveConfiguration" class="space-y-8">
                <!-- General Settings -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
                    <h4 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        General Settings
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-blue-900">Application Name</label>
                            <input type="text" wire:model="config.app_name"
                                   class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all duration-300 bg-white shadow-sm"
                                   placeholder="NYAWASCO System">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-blue-900">Application URL</label>
                            <input type="url" wire:model="config.app_url"
                                   class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all duration-300 bg-white shadow-sm"
                                   placeholder="https://nyawasco.example.com">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-blue-900">Timezone</label>
                            <select wire:model="config.timezone"
                                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all duration-300 bg-white shadow-sm">
                                <option value="Africa/Nairobi">East Africa Time (EAT)</option>
                                <option value="UTC">UTC</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-blue-900">Default Locale</label>
                            <select wire:model="config.locale"
                                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all duration-300 bg-white shadow-sm">
                                <option value="en">English</option>
                                <option value="sw">Swahili</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="bg-gradient-to-r from-red-50 to-pink-50 p-6 rounded-xl border border-red-200">
                    <h4 class="text-lg font-semibold text-red-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Security Settings
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-red-900">Session Lifetime (minutes)</label>
                            <input type="number" wire:model="config.session_lifetime" min="15" max="1440"
                                   class="w-full px-4 py-3 border-2 border-red-200 rounded-xl focus:border-red-500 focus:ring-4 focus:ring-red-200 transition-all duration-300 bg-white shadow-sm"
                                   placeholder="120">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-red-900">Max Login Attempts</label>
                            <input type="number" wire:model="config.max_login_attempts" min="3" max="10"
                                   class="w-full px-4 py-3 border-2 border-red-200 rounded-xl focus:border-red-500 focus:ring-4 focus:ring-red-200 transition-all duration-300 bg-white shadow-sm"
                                   placeholder="5">
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="config.enable_2fa" class="rounded border-red-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                <span class="ml-3 text-sm font-semibold text-red-900">Enable Two-Factor Authentication</span>
                            </label>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="config.force_https" class="rounded border-red-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                <span class="ml-3 text-sm font-semibold text-red-900">Force HTTPS</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Email Settings -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
                    <h4 class="text-lg font-semibold text-green-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email Configuration
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-green-900">Mail Driver</label>
                            <select wire:model="config.mail_driver"
                                    class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all duration-300 bg-white shadow-sm">
                                <option value="smtp">SMTP</option>
                                <option value="mailgun">Mailgun</option>
                                <option value="ses">Amazon SES</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-green-900">From Email</label>
                            <input type="email" wire:model="config.mail_from_address"
                                   class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all duration-300 bg-white shadow-sm"
                                   placeholder="noreply@nyawasco.go.ke">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-green-900">From Name</label>
                            <input type="text" wire:model="config.mail_from_name"
                                   class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all duration-300 bg-white shadow-sm"
                                   placeholder="NYAWASCO System">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" wire:click="resetConfiguration"
                            class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-300 font-semibold">
                        Reset Changes
                    </button>
                    <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white rounded-xl hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Save Configuration
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
    <div class="fixed top-4 right-4 z-50 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center animate-fade-in-up">
        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="font-semibold">{{ session('message') }}</span>
    </div>
    @endif

    <style>
    @keyframes tilt {
        0%, 50%, 100% {
            transform: rotate(0deg);
        }
        25% {
            transform: rotate(1deg);
        }
        75% {
            transform: rotate(-1deg);
        }
    }

    @keyframes fade-in-up {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-tilt {
        animation: tilt 10s infinite linear;
    }

    .animate-fade-in-up {
        animation: fade-in-up 1s ease-out forwards;
        opacity: 0;
    }

    .animation-delay-200 {
        animation-delay: 0.2s;
    }

    .animation-delay-400 {
        animation-delay: 0.4s;
    }
    </style>

    <script>
    document.addEventListener('livewire:loaded', () => {
        // Auto-hide flash messages
        setTimeout(() => {
            const messages = document.querySelectorAll('[role="alert"]');
            messages.forEach(message => {
                message.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                message.style.opacity = '0';
                message.style.transform = 'translateY(-20px) scale(0.95)';
                setTimeout(() => message.remove(), 500);
            });
        }, 3000);
    });
    </script>