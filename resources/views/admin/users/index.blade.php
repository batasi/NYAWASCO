@extends('layouts.app')
@props(['title' => 'User Management', 'subtitle' => 'Manage system users, roles, and permissions'])

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
@endphp

<style>
/* Professional styling consistent with your dashboard */
.professional-bg {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%);
    background-attachment: fixed;
}

.professional-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(255, 255, 255, 0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 1rem;
}

.professional-card:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04), 0 0 0 1px rgba(59, 130, 246, 0.1);
    transform: translateY(-2px);
}

.table-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 2px solid rgba(226, 232, 240, 0.8);
    backdrop-filter: blur(5px);
}

.table-row {
    transition: all 0.2s ease;
}

.table-row:hover {
    background: linear-gradient(135deg, rgba(248, 250, 252, 0.8) 0%, rgba(241, 245, 249, 0.6) 100%);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in {
    animation: fadeIn 0.3s ease-out;
}
</style>

<div class="min-h-screen professional-bg">
    <!-- Modern Header -->
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 backdrop-blur-xl border-b border-white/20 shadow-2xl shadow-blue-900/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">{{ $title }}</h1>
                    <p class="text-base text-blue-100 font-medium leading-relaxed">{{ $subtitle }}</p>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-right">
                        <p class="text-xs text-blue-200 uppercase tracking-wide font-semibold">Welcome</p>
                        <p class="text-lg font-semibold text-white">{{ Auth::user()->name ?? 'Admin' }}</p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-xl shadow-indigo-500/25 ring-2 ring-white/50">
                        <span class="text-white font-bold text-lg">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="professional-card p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0c-.181.028-.362.056-.543.086A14.995 14.995 0 0118 18.001v1m6-5h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-600">Total Users</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $users->total() }}</dd>
                    </div>
                </div>
            </div>

            <div class="professional-card p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-600">Active Users</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $users->where('is_active', 1)->count() }}</dd>
                    </div>
                </div>
            </div>

            <div class="professional-card p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-600">Roles</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $roles->count() }}</dd>
                    </div>
                </div>
            </div>

            <div class="professional-card p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <dt class="text-sm font-medium text-gray-600">Permissions</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $permissions->count() }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main User Management Table -->
        <div class="professional-card rounded-lg overflow-hidden mb-8 fade-in">
            <div class="px-6 py-4 table-header">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">System Users</h3>
                        <p class="text-sm text-gray-600 mt-1">Manage all system user accounts and permissions</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <input type="text" id="userSearch" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 w-full sm:w-64">
                            <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <button id="addUserBtn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add User
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                        @forelse($users as $user)
                        @php
                            $roleName = $user->roles->first()?->name ?? 'No Role';
                            $status = $user->is_active ? 'Active' : 'Inactive';
                            $statusColor = $user->is_active ? 'green' : 'red';
                        @endphp
                        <tr class="table-row" data-user-id="{{ $user->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover border border-gray-200">
                                    @else
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border border-gray-200">
                                        <span class="text-sm font-semibold text-blue-700">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->username }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="role-badge {{
                                    $roleName === 'admin' ? 'bg-red-100 text-red-800' :
                                    ($roleName === 'organizer' ? 'bg-blue-100 text-blue-800' :
                                    ($roleName === 'vendor' ? 'bg-purple-100 text-purple-800' :
                                    ($roleName === 'manager' ? 'bg-purple-100 text-purple-800' :
                                    ($roleName === 'ict' ? 'bg-green-100 text-green-800' :
                                    ($roleName === 'ceo' ? 'bg-yellow-100 text-yellow-800' :
                                    ($roleName === 'chief' ? 'bg-indigo-100 text-indigo-800' :
                                    ($roleName === 'registrar' ? 'bg-pink-100 text-pink-800' :
                                    ($roleName === 'report' ? 'bg-orange-100 text-orange-800' :
                                    ($roleName === 'biller' ? 'bg-teal-100 text-teal-800' :
                                    'bg-gray-100 text-gray-800'))))))))) }}">
                                    {{ ucfirst($roleName) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge {{
                                    $status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <button class="edit-user-btn inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-username="{{ $user->username }}"
                                            data-user-email="{{ $user->email }}"
                                            data-user-role="{{ $roleName }}"
                                            data-user-active="{{ $user->is_active }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    <button class="permissions-btn inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-role="{{ $roleName }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Permissions
                                    </button>
                                    @if($user->id !== Auth::id())
                                    <button class="delete-user-btn inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                No users found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing <span class="font-medium">{{ $users->firstItem() }}</span>
                        to <span class="font-medium">{{ $users->lastItem() }}</span>
                        of <span class="font-medium">{{ $users->total() }}</span> users
                    </div>
                    <div class="flex space-x-2">
                        @if ($users->onFirstPage())
                        <span class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed">
                            Previous
                        </span>
                        @else
                        <a href="{{ $users->previousPageUrl() }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Previous
                        </a>
                        @endif

                        @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Next
                        </a>
                        @else
                        <span class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed">
                            Next
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Role and Permission Management Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Roles List -->
            <div class="professional-card rounded-lg overflow-hidden">
                <div class="px-6 py-4 table-header">
                    <h3 class="text-lg font-semibold text-gray-900">System Roles</h3>
                    <p class="text-sm text-gray-600 mt-1">Manage user roles and their permissions</p>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-3">
                        @foreach($roles as $role)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <div>
                                <span class="text-sm font-medium text-gray-900">{{ ucfirst($role->name) }}</span>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $role->permissions->count() }} permissions •
                                    {{ User::whereHas('roles', function($q) use ($role) { $q->where('name', $role->name); })->count() }} users
                                </p>
                            </div>
                            <button class="edit-role-btn inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                    data-role-id="{{ $role->id }}"
                                    data-role-name="{{ $role->name }}">
                                Edit
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Permissions List -->
            <div class="professional-card rounded-lg overflow-hidden">
                <div class="px-6 py-4 table-header">
                    <h3 class="text-lg font-semibold text-gray-900">System Permissions</h3>
                    <p class="text-sm text-gray-600 mt-1">Available permissions in the system</p>
                </div>
                <div class="px-6 py-4 max-h-96 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($permissions as $permission)
                        <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <span class="text-sm font-medium text-gray-900 capitalize">
                                {{ str_replace(['_', '-'], ' ', $permission->name) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $permission->roles->count() }} roles have this permission
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the same modals from your dashboard -->
@include('admin.partials.user-modals')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize modals and functionality (copy from your existing dashboard JS)
    // You can move the JavaScript from your dashboard into a separate file
    // and include it here, or copy the relevant parts

    // For now, I'll show you how to structure the search functionality
    const userSearch = document.getElementById('userSearch');
    if (userSearch) {
        userSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTableBody tr');

            rows.forEach(row => {
                const userName = row.querySelector('td:first-child .text-gray-900').textContent.toLowerCase();
                const userEmail = row.querySelector('td:first-child .text-gray-500').textContent.toLowerCase();
                const username = row.querySelector('td:nth-child(2) .text-gray-900').textContent.toLowerCase();
                const userRole = row.querySelector('td:nth-child(3) span').textContent.toLowerCase();

                if (userName.includes(searchTerm) ||
                    userEmail.includes(searchTerm) ||
                    username.includes(searchTerm) ||
                    userRole.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // You'll need to add your existing modal JavaScript here
    // or include it from a separate file
});
</script>
@endsection
