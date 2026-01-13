<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl bg-gradient-to-r from-gray-800 via-blue-600 to-green-600 bg-clip-text text-transparent">
                    {{ __('Dashboard') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Welcome back, manage your operations</p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="hidden md:flex items-center space-x-2 bg-green-50 px-3 py-2 rounded-full">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm font-medium text-green-700">System Online</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Modern Hero Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-green-600 rounded-3xl shadow-2xl">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-white/20 to-transparent"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-24 translate-x-24"></div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
            </div>

            <div class="relative px-8 py-12 md:px-12 md:py-16">
                <div class="flex flex-col lg:flex-row items-center justify-between">
                    <div class="flex-1 text-white mb-8 lg:mb-0">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-blue-100 uppercase tracking-wide">Welcome Back</span>
                        </div>

                        <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
                            Hello, <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">{{ auth()->user()->name }}</span>! 👋
                        </h1>

                        <p class="text-xl text-blue-100 mb-8 leading-relaxed max-w-2xl">
                            @if(auth()->user()->role === 'admin')
                                Take control of your system administration dashboard. Monitor operations, manage users, and optimize performance.
                            @else
                                Access your personalized dashboard to manage your account, view reports, and utilize system features.
                            @endif
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="group inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold rounded-2xl hover:bg-blue-50 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Admin Dashboard
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('profile.edit') }}" class="group inline-flex items-center px-8 py-4 bg-white text-blue-600 font-bold rounded-2xl hover:bg-blue-50 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    My Profile
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </a>
                            @endif

                            <a href="{{ route('reports.index') }}" class="group inline-flex items-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-bold rounded-2xl hover:bg-white/20 transition-all duration-300 border border-white/20">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                View Reports
                            </a>
                        </div>
                    </div>

                    <!-- Animated Illustration -->
                    <div class="flex-1 lg:flex justify-center hidden">
                        <div class="relative">
                            <div class="w-80 h-80 bg-white/10 backdrop-blur-sm rounded-3xl p-8 shadow-2xl">
                                <div class="w-full h-full bg-gradient-to-br from-blue-400 to-green-400 rounded-2xl flex items-center justify-center">
                                    <svg class="w-32 h-32 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <!-- Floating Elements -->
                            <div class="absolute -top-4 -right-4 w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg animate-bounce">
                                <span class="text-2xl">⚡</span>
                            </div>
                            <div class="absolute -bottom-4 -left-4 w-12 h-12 bg-green-400 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                                <span class="text-xl">🚀</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->role === 'admin')
            <!-- Modern Admin Dashboard Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                <!-- System Overview Card -->
                <div class="group relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl p-8 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400/20 to-transparent"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">99.9%</div>
                                <div class="text-sm text-blue-100">Uptime</div>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">System Overview</h3>
                        <p class="text-blue-100 mb-6">Monitor system health, performance metrics, and operational status.</p>
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-semibold text-white hover:text-blue-100 transition-colors group">
                            View Details
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-white/10 rounded-full"></div>
                </div>

                <!-- User Management Card -->
                <div class="group relative bg-gradient-to-br from-green-500 to-green-600 rounded-3xl p-8 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-400/20 to-transparent"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">1,247</div>
                                <div class="text-sm text-green-100">Active Users</div>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">User Management</h3>
                        <p class="text-green-100 mb-6">Manage user accounts, roles, permissions, and access controls.</p>
                        <a href="{{ route('system.user.management') }}" class="inline-flex items-center text-sm font-semibold text-white hover:text-green-100 transition-colors group">
                            Manage Users
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-white/10 rounded-full"></div>
                </div>

                <!-- System Analytics Card -->
                <div class="group relative bg-gradient-to-br from-purple-500 to-purple-600 rounded-3xl p-8 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-400/20 to-transparent"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">24.5K</div>
                                <div class="text-sm text-purple-100">Data Points</div>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-2">System Analytics</h3>
                        <p class="text-purple-100 mb-6">View comprehensive analytics, reports, and performance insights.</p>
                        <a href="{{ route('system.analysis') }}" class="inline-flex items-center text-sm font-semibold text-white hover:text-purple-100 transition-colors group">
                            View Analytics
                            <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-white/10 rounded-full"></div>
                </div>
            </div>

            <!-- Quick Admin Actions -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Quick Actions</h3>
                            <p class="text-gray-600 mt-1">Common administrative tasks and shortcuts</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-green-500 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <a href="{{ route('system.management') }}" class="group bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-blue-200/50 hover:border-blue-300">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">System Settings</h4>
                                    <p class="text-sm text-gray-600">Configure system parameters</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('system.backups') }}" class="group bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-green-200/50 hover:border-green-300">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 group-hover:text-green-600 transition-colors">Backup System</h4>
                                    <p class="text-sm text-gray-600">Manage data backups</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('reports.index') }}" class="group bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-purple-200/50 hover:border-purple-300">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 group-hover:text-purple-600 transition-colors">Reports</h4>
                                    <p class="text-sm text-gray-600">Generate system reports</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Modern User Dashboard Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Account Status Card -->
                <div class="group relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400/20 to-transparent"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="w-3 h-3 rounded-full {{ auth()->user()->is_active ? 'bg-green-400' : 'bg-red-400' }} animate-pulse"></div>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold mb-1">Account Status</h3>
                        <p class="text-blue-100 text-sm mb-3">{{ auth()->user()->is_active ? 'Active & Verified' : 'Inactive' }}</p>
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center text-sm font-medium text-white/90 hover:text-white transition-colors">
                            Manage
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- System Access Card -->
                <div class="group relative bg-gradient-to-br from-green-500 to-green-600 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-400/20 to-transparent"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-green-100 uppercase font-medium">{{ ucfirst(auth()->user()->role ?? 'User') }}</div>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold mb-1">System Access</h3>
                        <p class="text-green-100 text-sm mb-3">Full system access granted</p>
                        <div class="flex items-center text-sm font-medium text-white/90">
                            <span>Role: {{ ucfirst(auth()->user()->role ?? 'user') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Last Activity Card -->
                <div class="group relative bg-gradient-to-br from-purple-500 to-purple-600 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-400/20 to-transparent"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-purple-100">Activity</div>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold mb-1">Last Login</h3>
                        <p class="text-purple-100 text-sm mb-3">
                            {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Never logged in' }}
                        </p>
                        <div class="flex items-center text-sm font-medium text-white/90">
                            <span>Activity tracking enabled</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div class="group relative bg-gradient-to-br from-orange-500 to-orange-600 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-105 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-400/20 to-transparent"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold">100%</div>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold mb-1">System Health</h3>
                        <p class="text-orange-100 text-sm mb-3">All systems operational</p>
                        <div class="flex items-center text-sm font-medium text-white/90">
                            <span>Performance optimal</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Quick Actions -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 px-8 py-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">Quick Actions</h3>
                            <p class="text-gray-600 mt-1">Everything you need at your fingertips</p>
                        </div>
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Profile Management -->
                        <a href="{{ route('profile.edit') }}" class="group relative bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 hover:shadow-2xl transition-all duration-500 transform hover:scale-105 border border-blue-200/50 hover:border-blue-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-400/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="relative flex items-center space-x-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors duration-300">Update Profile</h4>
                                    <p class="text-gray-600 mt-1">Manage your account information and preferences</p>
                                </div>
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>

                        <!-- Reports & Analytics -->
                        <a href="{{ route('reports.index') }}" class="group relative bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 hover:shadow-2xl transition-all duration-500 transform hover:scale-105 border border-green-200/50 hover:border-green-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-green-400/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="relative flex items-center space-x-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 group-hover:text-green-600 transition-colors duration-300">View Reports</h4>
                                    <p class="text-gray-600 mt-1">Access comprehensive system reports and analytics</p>
                                </div>
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-green-500 group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>

                        <!-- Support & Help -->
                        <a href="tel:+254787080455" class="group relative bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-6 hover:shadow-2xl transition-all duration-500 transform hover:scale-105 border border-orange-200/50 hover:border-orange-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-400/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="relative flex items-center space-x-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 group-hover:text-orange-600 transition-colors duration-300">Contact Support</h4>
                                    <p class="text-gray-600 mt-1">Get instant help from our support team</p>
                                </div>
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-orange-500 group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>

                        <!-- Security Settings -->
                        <a href="{{ route('profile.edit') }}#security" class="group relative bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 hover:shadow-2xl transition-all duration-500 transform hover:scale-105 border border-purple-200/50 hover:border-purple-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-400/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="relative flex items-center space-x-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 group-hover:text-purple-600 transition-colors duration-300">Security Settings</h4>
                                    <p class="text-gray-600 mt-1">Manage passwords, 2FA, and security preferences</p>
                                </div>
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-purple-500 group-hover:translate-x-1 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
