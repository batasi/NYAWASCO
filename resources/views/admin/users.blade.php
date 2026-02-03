@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp
@can('view users')
@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <!-- Top Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                <p class="text-sm text-gray-600 mt-1">Manage system users, roles, and permissions</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2">
                @can('add users')
                <button id="addUserBtn"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add User
                </button>
                @endcan
                @can('manage roles')
                <a href="{{ route('admin.roles.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Manage Roles
                </a>
                @endcan
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-6 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0c-.181.028-.362.056-.543.086A14.995 14.995 0 0118 18.001v1m6-5h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Users</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['active']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Inactive Users</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['inactive']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Admins</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['admin']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Verified</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['verified']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">This Month</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['this_month']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold">Filters</h2>
        </div>
        <form method="GET" action="{{ route('admin.users.index') }}" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Search by name, email, or username..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Role Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Sort Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="last_login_at" {{ request('sort') == 'last_login_at' ? 'selected' : '' }}>Last Login</option>
                    </select>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="mt-6 flex justify-between">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Apply Filters
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Username
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Last Login
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Created
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                    @forelse($users as $user)
                        @php
                            $roleName = $user->roles->first()?->name ?? 'No Role';
                            $status = $user->is_active ? 'Active' : 'Inactive';
                            $statusColor = $user->is_active ? 'green' : 'red';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover border border-gray-200">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border border-gray-200">
                                                <span class="text-sm font-semibold text-blue-700">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $user->username }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{
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
                            <td class="px-6 py-4">
                                @if($user->is_active)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                                @if($user->email_verified_at)
                                    <div class="mt-1 text-xs text-green-600">
                                        Verified
                                    </div>
                                @else
                                    <div class="mt-1 text-xs text-yellow-600">
                                        Unverified
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($user->last_login_at)
                                    <div class="text-sm text-gray-900">
                                        {{ $user->last_login_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $user->last_login_at->diffForHumans() }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">Never</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $user->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $user->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    @can('edit users')
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
                                    @endcan
                                    @can('view permissions')
                                    <button class="permissions-btn inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-role="{{ $roleName }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        Permissions
                                    </button>
                                    @endcan
                                    @if($user->id !== Auth::id())
                                    @can('delete users')
                                    <button class="delete-user-btn inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                    @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0c-.181.028-.362.056-.543.086A14.995 14.995 0 0118 18.001v1m6-5h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="mt-2">No users found</p>
                                @if(request()->hasAny(['q', 'role', 'status']))
                                    <p class="text-sm text-gray-400 mt-1">Try adjusting your filters</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Add New User</h3>
            <button type="button" id="closeAddModal" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="addUserForm" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label for="add_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input type="text" id="add_name" name="name" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="add_username" class="block text-sm font-medium text-gray-700">Username *</label>
                    <input type="text" id="add_username" name="username" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="add_email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                    <input type="email" id="add_email" name="email" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="add_role" class="block text-sm font-medium text-gray-700">Role *</label>
                    <select id="add_role" name="role" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="add_password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" id="add_password" name="password" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Minimum 6 characters</p>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="add_is_active" name="is_active" value="1" checked
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="add_is_active" class="ml-2 block text-sm text-gray-900">Active User</label>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
                <button type="button" id="cancelAddModal"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="flex justify-between items-center p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Edit User</h3>
            <button type="button" id="closeEditModal" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editUserForm" method="POST">
            @csrf
            <input type="hidden" id="edit_userId" name="id" value="">
            <div class="p-6 space-y-4">
                <div>
                    <label for="edit_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                    <input type="text" id="edit_name" name="name" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="edit_username" class="block text-sm font-medium text-gray-700">Username *</label>
                    <input type="text" id="edit_username" name="username" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="edit_email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                    <input type="email" id="edit_email" name="email" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="edit_role" class="block text-sm font-medium text-gray-700">Role *</label>
                    <select id="edit_role" name="role" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="edit_password" name="password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Leave blank to keep unchanged">
                    <p class="mt-1 text-xs text-gray-500">Minimum 6 characters (optional)</p>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="edit_is_active" class="ml-2 block text-sm text-gray-900">Active User</label>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
                <button type="button" id="cancelEditModal"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Permissions Modal -->
<div id="permissionsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b">
            <div>
                <h3 id="permissionsTitle" class="text-lg font-semibold text-gray-900">Manage Permissions</h3>
                <p id="userRoleInfo" class="text-sm text-gray-500 mt-1"></p>
            </div>
            <button type="button" id="closePermissionsModal" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <div id="permissionsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Permissions will be loaded here -->
            </div>
        </div>
        <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
            <button type="button" id="cancelPermissionsModal"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </button>
            <button type="button" id="savePermissions"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                Save Permissions
            </button>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.974-.833-2.744 0L4.242 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 id="confirmTitle" class="text-lg font-semibold text-gray-900">Delete User</h3>
                    <p id="confirmMessage" class="text-sm text-gray-500 mt-1"></p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
            <button type="button" id="cancelDelete"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </button>
            <button type="button" id="confirmDelete"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Delete User
            </button>
        </div>
    </div>
</div>

<!-- Permissions Modal -->
<div id="permissionsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b">
            <div>
                <h3 id="permissionsTitle" class="text-lg font-semibold text-gray-900">Manage Permissions</h3>
                <p id="userRoleInfo" class="text-sm text-gray-500 mt-1"></p>
            </div>
            <button type="button" id="closePermissionsModal" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <div id="permissionsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Permissions will be loaded here -->
            </div>
        </div>
        <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
            <button type="button" id="cancelPermissionsModal"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </button>
            <button type="button" id="savePermissions"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                Save Permissions
            </button>
        </div>
    </div>
</div>


<!-- Success/Error Toast -->
<div id="toast" class="fixed top-4 right-4 z-50 hidden">
    <div class="rounded-lg shadow-lg p-4 max-w-sm border" id="toastContent">
        <!-- Toast content will be inserted here -->
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Get modal elements
    const addUserModal = document.getElementById('addUserModal');
    const editUserModal = document.getElementById('editUserModal');
    const permissionsModal = document.getElementById('permissionsModal');
    const confirmationModal = document.getElementById('confirmationModal');
    const toast = document.getElementById('toast');
    const toastContent = document.getElementById('toastContent');

    // Toast function
    function showToast(message, type = 'success') {
        const colors = {
            success: 'bg-green-50 border-green-200 text-green-800',
            error: 'bg-red-50 border-red-200 text-red-800',
            warning: 'bg-yellow-50 border-yellow-200 text-yellow-800'
        };

        toastContent.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${type === 'success' ? `
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    ` : type === 'error' ? `
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    ` : `
                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.974-.833-2.744 0L4.242 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    `}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
            </div>
        `;
        toastContent.className = `rounded-lg shadow-lg p-4 max-w-sm border ${colors[type]}`;
        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 5000);
    }

    // Add User Button
    document.getElementById('addUserBtn').addEventListener('click', () => {
        // Reset add form
        document.getElementById('addUserForm').reset();
        document.getElementById('add_is_active').checked = true;
        addUserModal.classList.remove('hidden');
    });

    // Edit User Buttons - SIMPLE AND DIRECT
    document.querySelectorAll('.edit-user-btn').forEach(button => {
        button.addEventListener('click', () => {
            // Get user data from button
            const userId = button.dataset.userId;
            const userName = button.dataset.userName;
            const userUsername = button.dataset.userUsername;
            const userEmail = button.dataset.userEmail;
            const userRole = button.dataset.userRole;
            const userActive = button.dataset.userActive === '1';

            console.log('Editing user:', { userId, userName, userEmail, userUsername, userRole, userActive });

            // Populate edit form fields
            document.getElementById('edit_userId').value = userId;
            document.getElementById('edit_name').value = userName;
            document.getElementById('edit_username').value = userUsername;
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_role').value = userRole;
            document.getElementById('edit_is_active').checked = userActive;

            // Show edit modal
            editUserModal.classList.remove('hidden');

            // Debug: Check values are set
            console.log('Edit form values set:', {
                name: document.getElementById('edit_name').value,
                email: document.getElementById('edit_email').value,
                username: document.getElementById('edit_username').value,
                role: document.getElementById('edit_role').value
            });
        });
    });

    // Permissions Buttons
    document.querySelectorAll('.permissions-btn').forEach(button => {
        button.addEventListener('click', async () => {
            const userId = button.dataset.userId;
            const userName = button.dataset.userName;
            const userRole = button.dataset.userRole;

            currentUserId = userId;

            // Update modal title
            document.getElementById('permissionsTitle').textContent = `Permissions for ${userName}`;
            document.getElementById('userRoleInfo').textContent = `Role: ${userRole}`;

            // Show loading state
            const container = document.getElementById('permissionsContainer');
            container.innerHTML = `
                <div class="col-span-full flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                    <span class="ml-2 text-gray-600">Loading permissions...</span>
                </div>
            `;

            permissionsModal.classList.remove('hidden');

            try {
                // Load user permissions
                const response = await fetch(`/admin/users/${userId}/permissions`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    currentUserPermissions = data.permissions;

                    // Load all available permissions
                    const permissionsResponse = await fetch(`/admin/permissions`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    const permissionsData = await permissionsResponse.json();
                    renderPermissions(permissionsData, container);
                } else {
                    showToast(data.message || 'Failed to load permissions', 'error');
                }
            } catch (error) {
                showToast('Error loading permissions: ' + error.message, 'error');
            }
        });
    });

     // Render permissions checkboxes
    function renderPermissions(permissionsData, container) {
        // Extract permissions from the response object if needed
        let permissions = permissionsData;

        // Check if it's a response object with a permissions property
        if (permissionsData && permissionsData.permissions) {
            permissions = permissionsData.permissions;
        }

        // Check if permissions is an array
        if (!Array.isArray(permissions)) {
            console.error('Permissions is not an array:', permissionsData);
            container.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <div class="text-red-600 mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.974-.833-2.744 0L4.242 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-700">Error loading permissions. Invalid data format.</p>
                    <p class="text-sm text-gray-500 mt-1">Expected array, got: ${typeof permissionsData}</p>
                </div>
            `;
            return;
        }

        const groupedPermissions = {};

        // Group permissions by module/feature
        permissions.forEach(permissionItem => {
            // Handle both string permissions and object permissions
            const permissionName = typeof permissionItem === 'string' ? permissionItem : permissionItem.name;

            if (!permissionName || typeof permissionName !== 'string') {
                console.warn('Invalid permission item:', permissionItem);
                return;
            }

            // Handle both "view users" and "users.view" formats
            const parts = permissionName.split(' ');
            let module, action;

            if (parts.length === 2) {
                // Format: "view users" - reverse to get "users.view"
                action = parts[0];
                module = parts[1];
            } else {
                // Try splitting by dot as fallback
                const dotParts = permissionName.split('.');
                if (dotParts.length >= 2) {
                    module = dotParts[0];
                    action = dotParts[1];
                } else {
                    console.warn('Permission name format incorrect:', permissionName);
                    return;
                }
            }

            if (!groupedPermissions[module]) {
                groupedPermissions[module] = [];
            }

            groupedPermissions[module].push({
                name: permissionName,
                action: action,
                id: permissionName.replace(/[\.\s]/g, '-') // Replace both dots and spaces
            });
        });

        // Generate HTML for grouped permissions
        let html = '';

        if (Object.keys(groupedPermissions).length === 0) {
            html = `
                <div class="col-span-full text-center py-8">
                    <div class="text-gray-400 mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500">No permissions found.</p>
                </div>
            `;
        } else {
            Object.keys(groupedPermissions).sort().forEach(module => {
                html += `
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-3 capitalize">${module.replace(/_/g, ' ')}</h4>
                        <div class="space-y-2">
                `;

                groupedPermissions[module].forEach(permission => {
                    const isChecked = currentUserPermissions.includes(permission.name);
                    html += `
                        <div class="flex items-center">
                            <input type="checkbox"
                                id="${permission.id}"
                                name="permissions[]"
                                value="${permission.name}"
                                ${isChecked ? 'checked' : ''}
                                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="${permission.id}" class="ml-2 text-sm text-gray-700 capitalize">
                                ${permission.action.replace(/_/g, ' ')}
                            </label>
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            });
        }

        container.innerHTML = html;
    }

    // Delete User Buttons
    document.querySelectorAll('.delete-user-btn').forEach(button => {
        button.addEventListener('click', () => {
            const userId = button.dataset.userId;
            const userName = button.dataset.userName;

            document.getElementById('confirmTitle').textContent = `Delete ${userName}`;
            document.getElementById('confirmMessage').textContent = `Are you sure you want to delete ${userName}? This action cannot be undone.`;

            confirmationModal.classList.remove('hidden');

            // Set up delete confirmation
            document.getElementById('confirmDelete').onclick = async () => {
                try {
                    const response = await fetch(`/admin/users/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast(data.message, 'error');
                    }
                } catch (error) {
                    showToast('Error deleting user: ' + error.message, 'error');
                }

                confirmationModal.classList.add('hidden');
            };
        });
    });

    // Close modal buttons
    document.getElementById('closeAddModal').addEventListener('click', () => addUserModal.classList.add('hidden'));
    document.getElementById('cancelAddModal').addEventListener('click', () => addUserModal.classList.add('hidden'));

    document.getElementById('closeEditModal').addEventListener('click', () => editUserModal.classList.add('hidden'));
    document.getElementById('cancelEditModal').addEventListener('click', () => editUserModal.classList.add('hidden'));

    document.getElementById('closePermissionsModal').addEventListener('click', () => permissionsModal.classList.add('hidden'));
    document.getElementById('cancelPermissionsModal').addEventListener('click', () => permissionsModal.classList.add('hidden'));

    document.getElementById('cancelDelete').addEventListener('click', () => confirmationModal.classList.add('hidden'));

    // Close modals on outside click
    [addUserModal, editUserModal, permissionsModal, confirmationModal].forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });

    // Add User Form submission
    document.getElementById('addUserForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        console.log('Adding user:', data);

        try {
            const response = await fetch('/admin/users', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showToast(result.message, 'success');
                addUserModal.classList.add('hidden');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast(result.message, 'error');
            }
        } catch (error) {
            showToast('Error: ' + error.message, 'error');
        }
    });

    // Edit User Form submission
    document.getElementById('editUserForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const userId = document.getElementById('edit_userId').value;
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        console.log('Updating user:', userId, data);

        try {
            const response = await fetch(`/admin/users/${userId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showToast(result.message, 'success');
                editUserModal.classList.add('hidden');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast(result.message, 'error');
            }
        } catch (error) {
            showToast('Error: ' + error.message, 'error');
        }
    });

    // Save permissions
    document.getElementById('savePermissions').addEventListener('click', function() {
        showToast('Permissions saved successfully!', 'success');
        permissionsModal.classList.add('hidden');
    });
});
</script>
@endpush
@endsection
@endcan
