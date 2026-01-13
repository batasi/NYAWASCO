@section('page-title', 'System Analysis & Insights')
@section('page-description', 'Comprehensive analytics and system performance monitoring')

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
                <!-- 3D Icon with Glow Effect -->
                <div class="flex justify-center mb-6">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-yellow-400 via-pink-500 to-purple-600 rounded-3xl blur opacity-75 group-hover:opacity-100 transition duration-1000 group-hover:duration-200 animate-tilt"></div>
                        <div class="relative w-28 h-28 bg-gradient-to-br from-white/95 to-white/85 backdrop-blur-2xl rounded-3xl flex items-center justify-center shadow-2xl transform hover:scale-105 transition-all duration-500">
                            <svg class="w-20 h-20 text-indigo-600 drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <!-- Success Badge -->
                        <div class="absolute -top-3 -right-3 w-10 h-10 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-xl animate-bounce">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Animated Title -->
                <h1 class="text-6xl md:text-4xl font-black text-white mb-6 tracking-tight animate-fade-in-up">
                    System Analysis
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-orange-300 to-pink-300 animate-pulse">
                        & Insights
                    </span>
                </h1>

                <!-- Time Range Selector -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16 animate-fade-in-up animation-delay-400">
                    <select wire:model.live="timeRange" class="px-6 py-3 bg-white/10 backdrop-blur-xl text-white rounded-2xl border-2 border-white/30 hover:bg-white/20 transition-all duration-300">
                        <option value="7" class="text-gray-900">Last 7 Days</option>
                        <option value="30" class="text-gray-900">Last 30 Days</option>
                        <option value="90" class="text-gray-900">Last 90 Days</option>
                        <option value="365" class="text-gray-900">Last Year</option>
                    </select>
                </div>
            </div>

            <!-- Ultra-Advanced Stats Cards -->
            <div class="mt-20 grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- System Health -->
                <div class="group relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-green-600 to-emerald-600 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white/95 backdrop-blur-2xl rounded-3xl p-8 border border-white/20 shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-500">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl font-black text-gray-800 mb-1">{{ $system['health_score'] }}%</div>
                                <div class="text-sm text-gray-500 font-medium">System Health</div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Uptime</span>
                                <span class="text-green-600 font-semibold">{{ $system['uptime_percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $system['health_score'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="group relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white/95 backdrop-blur-2xl rounded-3xl p-8 border border-white/20 shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-500">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl font-black text-gray-800 mb-1">KES {{ number_format($revenue['total_revenue'], 0) }}</div>
                                <div class="text-sm text-gray-500 font-medium">Total Revenue</div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Avg Transaction</span>
                                <span class="text-blue-600 font-semibold">KES {{ number_format($revenue['avg_transaction'], 0) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-1000 ease-out" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="group relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white/95 backdrop-blur-2xl rounded-3xl p-8 border border-white/20 shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-500">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl font-black text-gray-800 mb-1">{{ $overview['active_users'] }}</div>
                                <div class="text-sm text-gray-500 font-medium">Active Users</div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Total Users</span>
                                <span class="text-purple-600 font-semibold">{{ $overview['total_users'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-purple-500 to-pink-600 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $overview['total_users'] > 0 ? ($overview['active_users'] / $overview['total_users']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Meters -->
                <div class="group relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-yellow-600 to-orange-600 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white/95 backdrop-blur-2xl rounded-3xl p-8 border border-white/20 shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-500">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl font-black text-gray-800 mb-1">{{ $overview['active_meters'] }}</div>
                                <div class="text-sm text-gray-500 font-medium">Active Meters</div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Total Meters</span>
                                <span class="text-orange-600 font-semibold">{{ $overview['total_meters'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-yellow-500 to-orange-600 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $overview['total_meters'] > 0 ? ($overview['active_meters'] / $overview['total_meters']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Analytics Tabs -->
        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button wire:click="$set('selectedMetric', 'overview')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $selectedMetric === 'overview' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Overview
                    </button>
                    <button wire:click="$set('selectedMetric', 'revenue')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $selectedMetric === 'revenue' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Revenue Analytics
                    </button>
                    <button wire:click="$set('selectedMetric', 'users')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $selectedMetric === 'users' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        User Analytics
                    </button>
                    <button wire:click="$set('selectedMetric', 'business')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $selectedMetric === 'business' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Business Insights
                    </button>
                    <button wire:click="$set('selectedMetric', 'system')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $selectedMetric === 'system' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        System Health
                    </button>
                </nav>
            </div>
        </div>

        <!-- Overview Dashboard -->
        @if($selectedMetric === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Trend Chart -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Revenue Trend</h3>
                        <p class="text-gray-600">Monthly revenue performance over time</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Revenue (KES)</span>
                    </div>
                </div>
                <canvas id="revenueTrendChart" width="400" height="300"
                        data-trend="{{ json_encode($revenue['monthly_trend']) }}"></canvas>
            </div>

            <!-- Revenue by Stream -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Revenue by Stream</h3>
                        <p class="text-gray-600">Distribution across revenue sources</p>
                    </div>
                </div>
                <div class="space-y-4">
                    @foreach($revenue['by_stream'] as $stream)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-indigo-500 rounded-full mr-3"></div>
                            <span class="text-sm font-semibold text-gray-900">{{ $stream['name'] }}</span>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900">KES {{ number_format($stream['revenue'], 0) }}</div>
                            <div class="text-xs text-gray-500">{{ $stream['count'] }} transactions</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- User Activity Heatmap -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Login Patterns</h3>
                        <p class="text-gray-600">User activity by hour</p>
                    </div>
                </div>
                <div class="grid grid-cols-6 gap-2">
                    @for($hour = 0; $hour < 24; $hour++)
                    @php
                        $logins = $users['login_patterns']->where('hour', $hour)->first();
                        $count = $logins ? $logins->logins : 0;
                        $intensity = $users['total_logins'] > 0 ? ($count / $users['total_logins']) * 100 : 0;
                    @endphp
                    <div class="aspect-square rounded-lg flex items-center justify-center text-xs font-semibold transition-all duration-300 hover:scale-110"
                         style="background-color: rgba(59, 130, 246, {{ $intensity / 100 }}); color: {{ $intensity > 50 ? 'white' : 'gray-900' }}">
                        {{ $hour }}
                    </div>
                    @endfor
                </div>
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600">Peak hour: {{ $users['peak_hour'] ? $users['peak_hour']->hour . ':00' : 'N/A' }}</p>
                </div>
            </div>

            <!-- System Health Metrics -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">System Health</h3>
                        <p class="text-gray-600">Real-time system metrics</p>
                    </div>
                </div>
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Response Time</span>
                            <span class="text-gray-900 font-semibold">{{ $system['avg_response_time'] }}ms</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ min($system['avg_response_time'] / 1000 * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Active Connections</span>
                            <span class="text-gray-900 font-semibold">{{ $system['active_connections'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min($system['active_connections'] / 100 * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Storage Usage</span>
                            <span class="text-gray-900 font-semibold">{{ $system['storage_usage']['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $system['storage_usage']['percentage'] }}%"></div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-red-600">{{ $system['security']['failed_logins'] }}</div>
                                <div class="text-xs text-gray-500">Failed Logins</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-orange-600">{{ $system['security']['suspicious_activities'] }}</div>
                                <div class="text-xs text-gray-500">Suspicious Activities</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Revenue Analytics -->
        @if($selectedMetric === 'revenue')
        <div class="space-y-8">
            <!-- Revenue Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold">KES {{ number_format($revenue['total_revenue'], 0) }}</div>
                            <div class="text-green-100">Total Revenue</div>
                        </div>
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold">{{ number_format($revenue['by_stream']->sum('count')) }}</div>
                            <div class="text-blue-100">Total Transactions</div>
                        </div>
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold">KES {{ number_format($revenue['avg_transaction'], 0) }}</div>
                            <div class="text-purple-100">Avg Transaction</div>
                        </div>
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold">{{ $revenue['by_stream']->count() }}</div>
                            <div class="text-yellow-100">Revenue Streams</div>
                        </div>
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Revenue Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Monthly Revenue Trend</h3>
                    <canvas id="monthlyRevenueChart" width="400" height="300"
                            data-monthly="{{ json_encode($revenue['monthly_trend']) }}"></canvas>
                </div>

                <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Daily Revenue (Last 7 Days)</h3>
                    <canvas id="dailyRevenueChart" width="400" height="300"
                            data-daily="{{ json_encode($revenue['daily_trend']) }}"></canvas>
                </div>
            </div>
        </div>
        @endif

        <!-- User Analytics -->
        @if($selectedMetric === 'users')
        <div class="space-y-8">
            <!-- User Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-6 text-white">
                    <div class="text-3xl font-bold mb-2">{{ $users['login_patterns']->sum('logins') }}</div>
                    <div class="text-blue-100">Total Logins</div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white">
                    <div class="text-3xl font-bold mb-2">{{ $users['registrations']->count() }}</div>
                    <div class="text-green-100">New Registrations</div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl p-6 text-white">
                    <div class="text-3xl font-bold mb-2">{{ $users['by_department']->count() }}</div>
                    <div class="text-purple-100">Active Departments</div>
                </div>
            </div>

            <!-- User Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">User Registration Trend</h3>
                    <canvas id="userRegistrationChart" width="400" height="300"
                            data-registrations="{{ json_encode($users['registrations']) }}"></canvas>
                </div>

                <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Department Activity</h3>
                    <div class="space-y-4">
                        @foreach($users['by_department'] as $dept)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $dept['department'] }}</div>
                                <div class="text-xs text-gray-500">{{ $dept['users'] }} users</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900">{{ $dept['total_activity'] }}</div>
                                <div class="text-xs text-gray-500">activities</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Business Analytics -->
        @if($selectedMetric === 'business')
        <div class="space-y-8">
            <!-- Business Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white">
                    <div class="text-3xl font-bold mb-2">{{ $business['total_businesses'] }}</div>
                    <div class="text-green-100">Total Businesses</div>
                </div>

                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-6 text-white">
                    <div class="text-3xl font-bold mb-2">{{ $business['active_businesses'] }}</div>
                    <div class="text-blue-100">Active Businesses</div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl p-6 text-white">
                    <div class="text-3xl font-bold mb-2">{{ $business['by_category']->count() }}</div>
                    <div class="text-purple-100">Business Categories</div>
                </div>

                <div class="bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl p-6 text-white">
                    <div class="text-3xl font-bold mb-2">{{ $business['registrations']->count() }}</div>
                    <div class="text-yellow-100">Recent Registrations</div>
                </div>
            </div>

            <!-- Business Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Business Categories</h3>
                    <canvas id="businessCategoryChart" width="400" height="300"
                            data-categories="{{ json_encode($business['by_category']) }}"></canvas>
                </div>

                <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Business Registration Trend</h3>
                    <canvas id="businessRegistrationChart" width="400" height="300"
                            data-registrations="{{ json_encode($business['registrations']) }}"></canvas>
                </div>
            </div>
        </div>
        @endif

        <!-- System Health -->
        @if($selectedMetric === 'system')
        <div class="space-y-8">
            <!-- System Health Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white">
                    <div class="text-3xl font-bold mb-2">{{ $system['uptime_percentage'] }}%</div>
                    <div class="text-green-100">System Uptime</div>
                </div>

                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-6 text-white">
                    <div class="text-3xl font-bold mb-2">{{ $system['avg_response_time'] }}ms</div>
                    <div class="text-blue-100">Avg Response Time</div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl p-6 text-white">
                    <div class="text-3xl font-bold mb-2">{{ $system['active_connections'] }}</div>
                    <div class="text-purple-100">Active Connections</div>
                </div>

                <div class="bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl p-6 text-white">
                    <div class="text-3xl font-bold mb-2">{{ $system['storage_usage']['percentage'] }}%</div>
                    <div class="text-yellow-100">Storage Usage</div>
                </div>
            </div>

            <!-- System Health Details -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Security Overview</h3>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-xl">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Failed Login Attempts</div>
                                    <div class="text-xs text-gray-500">Last {{ $timeRange }} days</div>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-red-600">{{ $system['security']['failed_logins'] }}</div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-orange-50 rounded-xl">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Suspicious Activities</div>
                                    <div class="text-xs text-gray-500">Security alerts</div>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-orange-600">{{ $system['security']['suspicious_activities'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Performance Metrics</h3>
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Database Connections</span>
                                <span class="text-gray-900 font-semibold">{{ $system['active_connections'] }}/100</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-500 h-3 rounded-full" style="width: {{ min($system['active_connections'] / 100 * 100, 100) }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Storage Usage</span>
                                <span class="text-gray-900 font-semibold">{{ $system['storage_usage']['used'] }}GB / {{ $system['storage_usage']['total'] }}GB</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-purple-500 h-3 rounded-full" style="width: {{ $system['storage_usage']['percentage'] }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">System Load</span>
                                <span class="text-gray-900 font-semibold">Low</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-500 h-3 rounded-full" style="width: 25%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

        // Handle logout to prevent polling conflicts
        const logoutLinks = document.querySelectorAll('a[href*="logout"], form[action*="logout"]');
        logoutLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                // Stop polling immediately on logout
                const pollingElements = document.querySelectorAll('[wire\\:poll]');
                pollingElements.forEach(el => {
                    el.removeAttribute('wire:poll');
                });
            });
        });

        // Also listen for form submissions that might be logout
        document.addEventListener('submit', (e) => {
            if (e.target.action && e.target.action.includes('logout')) {
                const pollingElements = document.querySelectorAll('[wire\\:poll]');
                pollingElements.forEach(el => {
                    el.removeAttribute('wire:poll');
                });
            }
        });

        // Initialize charts based on selected metric
        function initializeCharts() {
            const selectedMetric = @js($selectedMetric);

            if (selectedMetric === 'overview' || selectedMetric === 'revenue') {
                // Revenue Trend Chart
                const revenueTrendCtx = document.getElementById('revenueTrendChart');
                if (revenueTrendCtx) {
                    const trendData = JSON.parse(revenueTrendCtx.dataset.trend || '[]');

                    new Chart(revenueTrendCtx, {
                        type: 'line',
                        data: {
                            labels: trendData.map(item => item.month),
                            datasets: [{
                                label: 'Revenue (KES)',
                                data: trendData.map(item => item.revenue),
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'KES ' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Monthly Revenue Chart
                const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart');
                if (monthlyRevenueCtx) {
                    const monthlyData = JSON.parse(monthlyRevenueCtx.dataset.monthly || '[]');

                    new Chart(monthlyRevenueCtx, {
                        type: 'bar',
                        data: {
                            labels: monthlyData.map(item => item.month),
                            datasets: [{
                                label: 'Revenue (KES)',
                                data: monthlyData.map(item => item.revenue),
                                backgroundColor: '#3B82F6',
                                borderColor: '#2563EB',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'KES ' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Daily Revenue Chart
                const dailyRevenueCtx = document.getElementById('dailyRevenueChart');
                if (dailyRevenueCtx) {
                    const dailyData = JSON.parse(dailyRevenueCtx.dataset.daily || '[]');

                    new Chart(dailyRevenueCtx, {
                        type: 'line',
                        data: {
                            labels: dailyRevenueCtx.map(item => new Date(item.date).toLocaleDateString()),
                            datasets: [{
                                label: 'Revenue (KES)',
                                data: dailyData.map(item => item.revenue),
                                borderColor: '#F59E0B',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'KES ' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            if (selectedMetric === 'users') {
                // User Registration Chart
                const userRegistrationCtx = document.getElementById('userRegistrationChart');
                if (userRegistrationCtx) {
                    const registrationData = JSON.parse(userRegistrationCtx.dataset.registrations || '[]');

                    new Chart(userRegistrationCtx, {
                        type: 'line',
                        data: {
                            labels: registrationData.map(item => new Date(item.date).toLocaleDateString()),
                            datasets: [{
                                label: 'New Registrations',
                                data: registrationData.map(item => item.count),
                                borderColor: '#8B5CF6',
                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            }

            if (selectedMetric === 'business') {
                // Business Category Chart
                const businessCategoryCtx = document.getElementById('businessCategoryChart');
                if (businessCategoryCtx) {
                    const categoryData = JSON.parse(businessCategoryCtx.dataset.categories || '[]');

                    new Chart(businessCategoryCtx, {
                        type: 'doughnut',
                        data: {
                            labels: categoryData.map(item => item.category),
                            datasets: [{
                                data: categoryData.map(item => item.count),
                                backgroundColor: [
                                    '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                                    '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6B7280'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }

                // Business Registration Chart
                const businessRegistrationCtx = document.getElementById('businessRegistrationChart');
                if (businessRegistrationCtx) {
                    const registrationData = JSON.parse(businessRegistrationCtx.dataset.registrations || '[]');

                    new Chart(businessRegistrationCtx, {
                        type: 'bar',
                        data: {
                            labels: registrationData.map(item => new Date(item.date).toLocaleDateString()),
                            datasets: [{
                                label: 'Business Registrations',
                                data: registrationData.map(item => item.count),
                                backgroundColor: '#10B981',
                                borderColor: '#059669',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            }
            // Peak Login Hours Chart
            const peakLoginCtx = document.getElementById('peakLoginHoursChart');
            if (peakLoginCtx) {
                const patternsData = JSON.parse(peakLoginCtx.dataset.patterns || '[]');
                const labels = Array.from({length: 24}, (_, i) => `${i}:00`);
                const data = new Array(24).fill(0);
                patternsData.forEach(item => {
                    data[parseInt(item.hour)] = parseInt(item.logins);
                });
                new Chart(peakLoginCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Logins',
                            data: data,
                            backgroundColor: '#3B82F6',
                            borderColor: '#2563EB',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
            // Login Duration Chart
            const durationCtx = document.getElementById('loginDurationChart');
            if (durationCtx) {
                const durationData = JSON.parse(durationCtx.dataset.durations || '[]');
                const labels = Array.from({length: 24}, (_, i) => `${i}:00`);
                const data = new Array(24).fill(0);
                durationData.forEach(item => {
                    data[parseInt(item.hour)] = parseFloat(item.avg_duration) / 60;
                });
                new Chart(durationCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Avg Duration (minutes)',
                            data: data,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value + ' min';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Initialize charts on load
        initializeCharts();

        // Re-initialize charts when metric changes
        Livewire.on('metricChanged', () => {
            setTimeout(initializeCharts, 100);
        });
    });
    </script>
</div>