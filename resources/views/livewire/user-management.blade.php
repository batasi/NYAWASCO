
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50"
     wire:poll.10s="refreshOnlineStatus">
    <!-- Ultra-Advanced Hero Section -->
    <div class="relative overflow-hidden">
        <!-- Animated Background -->
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
                

                <!-- Animated Title -->
                <h1 class="text-6xl md:text-5xl font-black text-white mb-8 tracking-tight animate-fade-in-up">
                    User Management
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-orange-300 to-pink-300 animate-pulse">
                        System Control
                    </span>
                </h1>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16 animate-fade-in-up animation-delay-400">
                    <button wire:click="$set('showUserModal', true)"
                            class="group relative px-8 py-4 bg-gradient-to-r from-white/95 to-white/90 backdrop-blur-xl text-indigo-700 font-bold rounded-2xl shadow-2xl hover:shadow-white/25 transform hover:scale-105 transition-all duration-300">
                        <span class="relative z-10 flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Add New User
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-yellow-400/20 to-pink-400/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>

                    <button wire:click="$set('showRoleModal', true)"
                            class="group px-8 py-4 bg-white/10 backdrop-blur-xl text-white font-bold rounded-2xl border-2 border-white/30 hover:bg-white/20 transform hover:scale-105 transition-all duration-300">
                        <span class="flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Create Role
                        </span>
                    </button>

                    @if(auth()->user()->hasPermissionTo('users.manage') || auth()->user()->hasRole('super-admin'))
                    <button wire:click="openForcePasswordChangeModal"
                            class="group px-8 py-4 bg-gradient-to-r from-orange-500 to-red-500 text-white font-bold rounded-2xl hover:from-orange-600 hover:to-red-600 transform hover:scale-105 transition-all duration-300 shadow-lg">
                        <span class="flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Force Password Change
                        </span>
                    </button>
                    @endif
                </div>
            </div>

            <!-- Ultra-Advanced Stats Cards -->
            <div class="mt-20 grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Total Users -->
                <div class="group relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white/95 backdrop-blur-2xl rounded-3xl p-8 border border-white/20 shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-500">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl font-black text-gray-800 mb-1">{{ $users->total() }}</div>
                                <div class="text-sm text-gray-500 font-medium">Total Users</div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Active Users</span>
                                <span class="text-green-600 font-semibold">{{ $users->where('is_active', true)->count() }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $users->total() > 0 ? ($users->where('is_active', true)->count() / $users->total()) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Roles -->
                <div class="group relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-green-600 to-emerald-600 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white/95 backdrop-blur-2xl rounded-3xl p-8 border border-white/20 shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-500">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl font-black text-gray-800 mb-1">{{ $roles->count() }}</div>
                                <div class="text-sm text-gray-500 font-medium">Total Roles</div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">System Roles</span>
                                <span class="text-emerald-600 font-semibold">{{ $roles->where('is_system', true)->count() }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $roles->count() > 0 ? ($roles->where('is_system', true)->count() / $roles->count()) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Sessions -->
                <div class="group relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-yellow-600 to-orange-600 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white/95 backdrop-blur-2xl rounded-3xl p-8 border border-white/20 shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-500">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl font-black text-gray-800 mb-1">{{ $users->where('is_active', true)->count() }}</div>
                                <div class="text-sm text-gray-500 font-medium">Active Sessions</div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Online Now</span>
                                <span class="text-orange-600 font-semibold">{{ $users->where('last_activity_at', '>', now()->subMinutes(15))->count() }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-yellow-500 to-orange-600 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $users->total() > 0 ? ($users->where('last_activity_at', '>', now()->subMinutes(15))->count() / $users->total()) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Score -->
                <div class="group relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white/95 backdrop-blur-2xl rounded-3xl p-8 border border-white/20 shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-500">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <div class="text-4xl font-black text-gray-800 mb-1">98.5%</div>
                                <div class="text-sm text-gray-500 font-medium">Security Score</div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">2FA Enabled</span>
                                <span class="text-purple-600 font-semibold">{{ $users->whereNotNull('two_factor_secret')->count() }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-purple-500 to-pink-600 h-3 rounded-full transition-all duration-1000 ease-out" style="width: 98.5%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-2 sm:px-4 lg:px-6 py-12">
        <!-- Advanced Search & Filters -->
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
                               placeholder="Search users by name, email, phone number, or employee ID..."
                               class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-2xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 placeholder-gray-500 text-lg shadow-sm">
                    </div>
                </div>

                <div class="flex flex-wrap gap-4">
                    <select wire:model.live="filterRole" class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 bg-gray-50 focus:bg-white">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="filterStatus" class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 bg-gray-50 focus:bg-white">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>

                    <select wire:model.live="filterDepartment" class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 bg-gray-50 focus:bg-white">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>

                    <button wire:click="toggleOnlineFilter"
                            class="px-4 py-3 {{ $showOnlineOnly ? 'bg-green-500 hover:bg-green-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' }} rounded-xl transition-all duration-200 font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        {{ $showOnlineOnly ? 'Show All Users' : 'Online Only' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs for Users, Roles, Policies, and Audit -->
        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button wire:click="switchTab('users')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'users' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Users ({{ $users->total() }})
                    </button>
                    <button wire:click="switchTab('roles')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'roles' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Roles & Permissions ({{ $roles->count() }})
                    </button>
                    <button wire:click="switchTab('policies')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'policies' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Password Policies
                    </button>
                    <button wire:click="switchTab('password-audit')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'password-audit' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Password Audit ({{ $passwordAudits->total() ?? 0 }})
                    </button>
                </nav>
            </div>
        </div>

        <!-- Users Table -->
        @if($activeTab === 'users')
        <div class="w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">System Users</h3>
                        <p class="text-gray-600 mt-1">Manage user accounts, roles, and access permissions</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Export/Import Buttons -->
                        <div class="flex items-center space-x-2">
                            <button wire:click="exportCsv"
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export CSV
                            </button>
                            <button wire:click="exportPdf"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export PDF
                            </button>
                            <label class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium cursor-pointer flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Import CSV
                                <input type="file" wire:model="csvFile" wire:change="importCsv" accept=".csv" class="hidden">
                            </label>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">{{ $users->total() }}</p>
                            <p class="text-sm text-gray-500">Total Users</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-indigo-50 to-purple-50">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Name</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date Created</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Employee ID</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Phone Number</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Ward</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Role</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Online Status</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-200">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ trim($user->first_name . ' ' . $user->last_name . ' ' . $user->other_names) }}</div>
                                <div class="text-xs text-gray-500 mt-1">Last Login: {{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never' }}</div>
                                <div class="mt-1">
                                    @if($user->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Active
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Deactivated
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $user->created_at->format('Y-m-d') }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $user->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->employee_id ?? 'N/A' }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->ward->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                    {{ $user->role->name ?? 'No Role' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                @if($user->last_activity_at && $user->last_activity_at->diffInMinutes(now()) <= 15)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Online
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Offline
                                </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-col space-y-2">
                                    <button wire:click="openUserModal({{ $user->id }})"
                                            class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-4 py-2 rounded-lg hover:from-indigo-600 hover:to-purple-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button wire:click="openPasswordModal({{ $user->id }})"
                                            class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                        </svg>
                                        Change Password
                                    </button>
                                    <button wire:click="toggleUserStatus({{ $user->id }})"
                                            class="bg-gradient-to-r {{ $user->is_active ? 'from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600' : 'from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600' }} text-white px-4 py-2 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $user->is_active ? 'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"></path>
                                        </svg>
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    @if(!$user->is_system)
                                    <button wire:click="confirmDelete({{ $user->id }}, 'user')"
                                            class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-4 py-2 rounded-lg hover:from-red-600 hover:to-pink-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-8 py-16 text-center">
                                <div class="w-32 h-32 bg-gradient-to-r from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-3xl font-bold text-gray-900 mb-4">No Users Found</h3>
                                <p class="text-xl text-gray-600 mb-6">Start by creating your first user account</p>
                                <button wire:click="$set('showUserModal', true)"
                                        class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white px-8 py-4 rounded-2xl hover:from-indigo-700 hover:via-purple-700 hover:to-pink-700 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 font-semibold text-lg flex items-center mx-auto">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    Create First User
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Advanced Pagination -->
            @if($users->hasPages())
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-semibold">{{ $users->firstItem() }}</span> to <span class="font-semibold">{{ $users->lastItem() }}</span> of <span class="font-semibold">{{ $users->total() }}</span> results
                    </div>
                    <div class="flex space-x-2">
                        @if($users->onFirstPage())
                        <button disabled class="px-4 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
                            Previous
                        </button>
                        @else
                        <button wire:click="previousPage" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Previous
                        </button>
                        @endif

                        @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        <button wire:click="gotoPage({{ $page }})"
                                class="px-4 py-2 rounded-lg transition-colors {{ $users->currentPage() === $page ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                            {{ $page }}
                        </button>
                        @endforeach

                        @if($users->hasMorePages())
                        <button wire:click="nextPage" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Next
                        </button>
                        @else
                        <button disabled class="px-4 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
                            Next
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Roles & Permissions Table -->
        @if($activeTab === 'roles')
        <div class="space-y-8">
           

             <!-- Roles Table -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-8 py-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Roles Management</h3>
                            <p class="text-gray-600 mt-1">Manage system roles and their associated permissions</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button wire:click="$set('showRoleModal', true)"
                                    class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-6 py-3 rounded-xl hover:from-purple-600 hover:to-pink-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create New Role
                            </button>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-900">{{ $roles->count() }}</p>
                                <p class="text-sm text-gray-500">Total Roles</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-purple-50 to-pink-50">
                            <tr>
                                <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Role Details</th>
                                <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Permissions</th>
                                <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Users Count</th>
                                <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($roles as $role)
                            <tr class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 transition-all duration-200">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                                            <span class="text-white font-bold text-lg">{{ strtoupper(substr($role->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-lg font-bold text-gray-900">{{ ucwords(str_replace('_', ' ', $role->name)) }}</div>
                                            <div class="text-sm text-gray-500">{{ $role->guard_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-wrap gap-2">
                                        @if(is_array($role->permissions))
                                            @foreach(array_slice($role->permissions, 0, 3) as $permission)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $permission }}
                                            </span>
                                            @endforeach
                                            @if(count($role->permissions) > 3)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                +{{ count($role->permissions) - 3 }} more
                                            </span>
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-500">No permissions set</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-gray-900">{{ $role->users->count() }}</div>
                                        <div class="text-sm text-gray-500">assigned users</div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    @if($role->is_system)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        System Role
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Custom Role
                                    </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2">
                                        <button wire:click="openRoleModal({{ $role->id }})"
                                                class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        @if(!$role->is_system)
                                        <button wire:click="confirmDelete({{ $role->id }}, 'role')"
                                                class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-4 py-2 rounded-lg hover:from-red-600 hover:to-pink-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-16 text-center">
                                    <div class="w-32 h-32 bg-gradient-to-r from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                        <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-3xl font-bold text-gray-900 mb-4">No Roles Found</h3>
                                    <p class="text-xl text-gray-600 mb-6">Start by creating your first role</p>
                                    <button wire:click="$set('showRoleModal', true)"
                                            class="bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 text-white px-8 py-4 rounded-2xl hover:from-purple-700 hover:via-pink-700 hover:to-red-700 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 font-semibold text-lg flex items-center mx-auto">
                                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                        Create First Role
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-8 py-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Permissions Management</h3>
                            <p class="text-gray-600 mt-1">Create and manage system permissions</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button wire:click="openCreatePermissionModal"
                                    class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-6 py-3 rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create New Permission
                            </button>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-900">{{ $permissions->count() ?? 0 }}</p>
                                <p class="text-sm text-gray-500">Total Permissions</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-green-50 to-emerald-50">
                            <tr>
                                <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Permission Details</th>
                                <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Guard Name</th>
                                <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Roles Using</th>
                                <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($permissions ?? [] as $permission)
                            <tr class="hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all duration-200">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                                            <span class="text-white font-bold text-lg">{{ strtoupper(substr($permission->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-lg font-bold text-gray-900">{{ ucwords(str_replace(['_', '-'], ' ', $permission->name)) }}</div>
                                            <div class="text-sm text-gray-500">{{ $permission->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                        {{ $permission->guard_name }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-gray-900">{{ $permission->roles->count() }}</div>
                                        <div class="text-sm text-gray-500">assigned roles</div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Active
                                    </span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2">
                                        <button wire:click="openPermissionModal({{ $permission->id }})"
                                                class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-4 py-2 rounded-lg hover:from-green-600 hover:to-emerald-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button wire:click="confirmDelete({{ $permission->id }}, 'permission')"
                                                class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-4 py-2 rounded-lg hover:from-red-600 hover:to-pink-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-8 py-16 text-center">
                                    <div class="w-32 h-32 bg-gradient-to-r from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                        <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-3xl font-bold text-gray-900 mb-4">No Permissions Found</h3>
                                    <p class="text-xl text-gray-600 mb-6">Start by creating your first permission</p>
                                    <button wire:click="$set('showPermissionModal', true)"
                                            class="bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 text-white px-8 py-4 rounded-2xl hover:from-green-700 hover:via-emerald-700 hover:to-teal-700 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 font-semibold text-lg flex items-center mx-auto">
                                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Create First Permission
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

           
        </div>
        @endif

        <!-- Password Policies Tab -->
        @if($activeTab === 'policies')
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Password Policies</h3>
                        <p class="text-gray-600 mt-1">Configure automated password expiry and security settings</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button wire:click="runPasswordExpiryCheck"
                                class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-xl hover:from-orange-600 hover:to-red-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Run Expiry Check
                        </button>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Automated Security</p>
                            <p class="text-lg font-bold text-gray-900">Password Policies</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <form wire:submit.prevent="savePasswordPolicies" class="space-y-8">
                    <!-- Password Expiry Settings -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
                        <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Password Expiry Settings
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="passwordExpiryEnabled" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-3 text-sm font-semibold text-blue-900">Enable Automatic Password Expiry</span>
                                </label>
                                <p class="text-xs text-blue-700 ml-6">When enabled, users will be automatically required to change their passwords after the specified period.</p>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-blue-900">Expiry Period (Days)</label>
                                <input type="number" wire:model="passwordExpiryDays" min="1" max="365"
                                       class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all duration-300 bg-white shadow-sm"
                                       placeholder="90">
                                <p class="text-xs text-blue-700">Number of days after which passwords expire (1-365 days)</p>
                                @error('passwordExpiryDays') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Information Section -->
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 p-6 rounded-xl border border-yellow-200">
                        <h4 class="text-xl font-bold text-orange-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            How It Works
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h5 class="font-semibold text-orange-900 mb-2">Automated Process</h5>
                                <ul class="text-sm text-orange-800 space-y-1">
                                    <li>• Runs daily via scheduled command</li>
                                    <li>• Checks all active user passwords</li>
                                    <li>• Flags expired passwords automatically</li>
                                    <li>• Logs all security actions</li>
                                </ul>
                            </div>

                            <div>
                                <h5 class="font-semibold text-orange-900 mb-2">Manual Controls</h5>
                                <ul class="text-sm text-orange-800 space-y-1">
                                    <li>• Run expiry check on-demand</li>
                                    <li>• Force change for all users</li>
                                    <li>• Individual user password reset</li>
                                    <li>• Complete audit trail</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <button type="button" wire:click="loadPasswordPolicies"
                                class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-300 font-semibold">
                            Reset Changes
                        </button>
                        <button type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white rounded-xl hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Save Password Policies
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif




        <!-- Password Audit Tab -->
        @if($activeTab === 'password-audit')
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-red-50 to-pink-50 px-8 py-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Password Security Audit</h3>
                        <p class="text-gray-600 mt-1">Monitor password changes, expirations, and security events</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button wire:click="exportAuditCsv"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Audit CSV
                        </button>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900">{{ $passwordAudits->total() }}</p>
                            <p class="text-sm text-gray-500">Audit Events</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-red-50 to-pink-50">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Event</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Action</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Details</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Timestamp</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Initiated By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($passwordAudits as $audit)
                        <tr class="hover:bg-gradient-to-r hover:from-red-50 hover:to-pink-50 transition-all duration-200">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-500 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                                        @if($audit->event_type === 'user_management')
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                            </svg>
                                        @elseif($audit->event_type === 'security')
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-gray-900">{{ ucwords(str_replace('_', ' ', $audit->event_type)) }}</div>
                                        <div class="text-sm text-gray-500">{{ $audit->action }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $audit->user ? $audit->user->full_name : 'System' }}</div>
                                <div class="text-xs text-gray-500">{{ $audit->user ? $audit->user->email : 'N/A' }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                    @if($audit->action === 'reset_password') bg-orange-100 text-orange-800
                                    @elseif($audit->action === 'force_password_change_all') bg-red-100 text-red-800
                                    @elseif($audit->action === 'automated_password_expiry') bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucwords(str_replace('_', ' ', $audit->action)) }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $audit->details }}">
                                    {{ Str::limit($audit->details, 50) }}
                                </div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $audit->created_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500">{{ $audit->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $audit->reviewer ? $audit->reviewer->full_name : 'System' }}</div>
                                <div class="text-xs text-gray-500">{{ $audit->reviewer ? $audit->reviewer->email : 'Automated' }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center">
                                <div class="w-32 h-32 bg-gradient-to-r from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-3xl font-bold text-gray-900 mb-4">No Audit Events Found</h3>
                                <p class="text-xl text-gray-600 mb-6">Password security events will appear here</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Password Audit -->
            @if($passwordAudits->hasPages())
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-semibold">{{ $passwordAudits->firstItem() }}</span> to <span class="font-semibold">{{ $passwordAudits->lastItem() }}</span> of <span class="font-semibold">{{ $passwordAudits->total() }}</span> audit events
                    </div>
                    <div class="flex space-x-2">
                        @if($passwordAudits->onFirstPage())
                        <button disabled class="px-4 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
                            Previous
                        </button>
                        @else
                        <button wire:click="previousPage" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Previous
                        </button>
                        @endif

                        @foreach($passwordAudits->getUrlRange(1, $passwordAudits->lastPage()) as $page => $url)
                        <button wire:click="gotoPage({{ $page }})"
                                class="px-4 py-2 rounded-lg transition-colors {{ $passwordAudits->currentPage() === $page ? 'bg-red-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                            {{ $page }}
                        </button>
                        @endforeach

                        @if($passwordAudits->hasMorePages())
                        <button wire:click="nextPage" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Next
                        </button>
                        @else
                        <button disabled class="px-4 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
                            Next
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- User Modal -->
    @if($showUserModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">{{ $editingUserId ? 'Edit User' : 'Create New User' }}</h3>
                                <p class="text-indigo-100">{{ $editingUserId ? 'Update user information and permissions' : 'Add a new user to the system' }}</p>
                            </div>
                        </div>
                        <button wire:click="closeUserModal" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form wire:submit.prevent="saveUser" class="px-8 py-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Personal Information -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Personal Information
                            </h4>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">First Name *</label>
                            <input type="text" wire:model="userForm.first_name"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('userForm.first_name') border-red-300 @enderror"
                                   placeholder="Enter first name">
                            @error('userForm.first_name')
                                <p class="text-red-600 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Last Name *</label>
                            <input type="text" wire:model="userForm.last_name"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('userForm.last_name') border-red-300 @enderror"
                                   placeholder="Enter last name">
                            @error('userForm.last_name')
                                <p class="text-red-600 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Other Names</label>
                            <input type="text" wire:model="userForm.other_names"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm"
                                   placeholder="Enter other names (optional)">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Email Address *</label>
                            <input type="email" wire:model="userForm.email"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('userForm.email') border-red-300 @enderror"
                                   placeholder="Enter email address">
                            @error('userForm.email')
                                <p class="text-red-600 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Phone Number *</label>
                            <input type="text" wire:model="userForm.phone"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('userForm.phone') border-red-300 @enderror"
                                   placeholder="Enter phone number">
                            @error('userForm.phone')
                                <p class="text-red-600 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Identification -->
                        <div class="md:col-span-2 mt-8">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                </svg>
                                Identification & Employment
                            </h4>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">National ID *</label>
                            <input type="text" wire:model="userForm.national_id"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('userForm.national_id') border-red-300 @enderror"
                                   placeholder="Enter national ID number">
                            @error('userForm.national_id')
                                <p class="text-red-600 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Employee ID</label>
                            <input type="text" wire:model="userForm.employee_id"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm"
                                   placeholder="Enter employee ID (optional)">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Staff Number</label>
                            <input type="text" wire:model="userForm.staff_no"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:ring-4 focus:ring-purple-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm"
                                   placeholder="Enter staff number (optional)">
                        </div>

                        <!-- Address Information -->
                        <div class="md:col-span-2 mt-8">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Address Information
                            </h4>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Address</label>
                            <input type="text" wire:model="userForm.address"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm"
                                   placeholder="Enter address (optional)">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">P.O. Box</label>
                            <input type="text" wire:model="userForm.po_box"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm"
                                   placeholder="Enter P.O. Box (optional)">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Ward *</label>
                            <select wire:model="userForm.ward_id"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('userForm.ward_id') border-red-300 @enderror">
                                <option value="">Select Ward</option>
                                @foreach($wards as $ward)
                                <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                @endforeach
                            </select>
                            @error('userForm.ward_id')
                                <p class="text-red-600 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Department</label>
                            <select wire:model="userForm.department_id"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm">
                                <option value="">Select Department (optional)</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Role Assignment -->
                        <div class="md:col-span-2 mt-8">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                Role Assignment
                            </h4>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Role *</label>
                            <select wire:model="userForm.role_id"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('userForm.role_id') border-red-300 @enderror">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('userForm.role_id')
                                <p class="text-red-600 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Status *</label>
                            <select wire:model="userForm.is_active"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-200 transition-all duration-300 bg-gray-50 focus:bg-white text-gray-900 shadow-sm @error('userForm.is_active') border-red-300 @enderror">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('userForm.is_active')
                                <p class="text-red-600 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" wire:click="closeUserModal"
                                class="px-8 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-300 font-semibold">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white rounded-xl hover:from-indigo-700 hover:via-purple-700 hover:to-pink-700 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $editingUserId ? 'Update User' : 'Create User' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
</div>