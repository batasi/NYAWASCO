@section('page-title', 'Sessions & Logs')
@section('page-description', 'Monitor user sessions and system activity logs')

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600">
            <div class="absolute inset-0 bg-black/20"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-2 sm:px-4 lg:px-4 py-6">
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-600 rounded-3xl blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-tilt"></div>
                        <div class="relative w-28 h-28 bg-gradient-to-br from-white/95 to-white/85 backdrop-blur-2xl rounded-3xl flex items-center justify-center shadow-2xl transform hover:scale-105 transition-all duration-500">
                            <svg class="w-20 h-20 text-indigo-600 drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <h1 class="text-6xl md:text-4xl font-black text-white mb-6 tracking-tight animate-fade-in-up">
                    Sessions & Logs
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-orange-300 to-pink-300 animate-pulse">
                        Monitoring
                    </span>
                </h1>
            </div>
        </div>
    </div>

    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Search & Filters -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 mb-8 border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                               placeholder="Search sessions, logs, users..."
                               class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-2xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 placeholder-gray-500 text-lg shadow-sm">
                    </div>
                </div>

                <div class="flex flex-wrap gap-4">
                    <select wire:model.live="filterUser" class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 bg-gray-50 focus:bg-white">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                        @endforeach
                    </select>

                    @if($activeTab === 'activity')
                    <select wire:model.live="filterAction" class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 bg-gray-50 focus:bg-white">
                        <option value="">All Actions</option>
                        <option value="login">Login</option>
                        <option value="logout">Logout</option>
                        <option value="password_change">Password Change</option>
                        <option value="profile_update">Profile Update</option>
                    </select>
                    @endif

                    <input type="date" wire:model.live="filterDateFrom" class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 bg-gray-50 focus:bg-white">
                    <input type="date" wire:model.live="filterDateTo" class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 bg-gray-50 focus:bg-white">

                    <button wire:click="clearFilters"
                            class="px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl transition-all duration-200 font-medium">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button wire:click="switchTab('sessions')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'sessions' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Active Sessions
                    </button>
                    <button wire:click="switchTab('activity')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'activity' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Activity Logs
                    </button>
                </nav>
            </div>
        </div>

        <!-- Sessions Tab -->
        @if($activeTab === 'sessions')
        <div class="w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Active User Sessions</h3>
                        <p class="text-gray-600 mt-1">Monitor and manage current user sessions</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button wire:click="exportLogs"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Sessions
                        </button>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">{{ $this->sessionLogs->total() }}</p>
                            <p class="text-sm text-gray-500">Active Sessions</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">IP Address</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Device/Browser</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Last Activity</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Session Started</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($this->sessionLogs as $session)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                        <span class="text-white font-bold text-lg">{{ substr($session->first_name ?? 'U', 0, 1) }}{{ substr($session->last_name ?? 'N', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-gray-900">{{ $session->first_name }} {{ $session->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $session->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $session->ip_address }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $session->user_agent }}">
                                    {{ strlen($session->user_agent) > 30 ? substr($session->user_agent, 0, 30) . '...' : $session->user_agent }}
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->format('M d, Y H:i') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->format('M d, Y H:i') }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-sm font-medium">
                                <button wire:click="terminateSession('{{ $session->id }}')"
                                        class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-4 py-2 rounded-lg hover:from-red-600 hover:to-pink-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    Terminate
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center">
                                <div class="w-32 h-32 bg-gradient-to-r from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-3xl font-bold text-gray-900 mb-4">No Active Sessions Found</h3>
                                <p class="text-xl text-gray-600 mb-6">No user sessions are currently active</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($this->sessionLogs->hasPages())
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-t border-gray-200">
                {{ $this->sessionLogs->links() }}
            </div>
            @endif
        </div>
        @endif

        <!-- Activity Logs Tab -->
        @if($activeTab === 'activity')
        <div class="w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Activity Logs</h3>
                        <p class="text-gray-600 mt-1">View detailed user activity and system events</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button wire:click="exportLogs"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Logs
                        </button>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">{{ $this->activityLogs->total() }}</p>
                            <p class="text-sm text-gray-500">Activity Events</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-green-50 to-emerald-50">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Action</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Description</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">IP Address</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($this->activityLogs as $log)
                        <tr class="hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all duration-200">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                        <span class="text-white font-bold text-lg">{{ substr($log->first_name ?? 'U', 0, 1) }}{{ substr($log->last_name ?? 'N', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-gray-900">{{ $log->first_name }} {{ $log->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $log->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                    @if($log->action === 'login') bg-green-100 text-green-800
                                    @elseif($log->action === 'logout') bg-red-100 text-red-800
                                    @elseif($log->action === 'password_change') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $log->description }}">
                                    {{ Str::limit($log->description, 50) }}
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $log->ip_address }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-16 text-center">
                                <div class="w-32 h-32 bg-gradient-to-r from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-3xl font-bold text-gray-900 mb-4">No Activity Logs Found</h3>
                                <p class="text-xl text-gray-600 mb-6">No activity logs are available</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($this->activityLogs->hasPages())
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-t border-gray-200">
                {{ $this->activityLogs->links() }}
            </div>
            @endif
        </div>
        @endif

        <!-- Flash Messages -->
        @if (session()->has('message'))
        <div class="fixed top-4 right-4 z-50 bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center animate-fade-in-up">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-semibold">{{ session('message') }}</span>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="fixed top-4 right-4 z-50 bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center animate-fade-in-up">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <span class="font-semibold">{{ session('error') }}</span>
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
                    message.style.transition = 'all 0.5s cubic-bezier(0.4, 0.2, 1, 1)';
                    message.style.opacity = '0';
                    message.style.transform = 'translateY(-20px) scale(0.95)';
                    setTimeout(() => message.remove(), 500);
                });
            }, 3000);
        });
        </script>
    </div>
</div>