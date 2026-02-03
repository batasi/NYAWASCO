@extends('layouts.app')

@section('title', 'Role Management')
@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Role Management</h1>
                <p class="text-sm text-gray-600 mt-1">Manage system roles and their permissions</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <button id="addRoleBtn"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Role
                </button>

                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0c-.181.028-.362.056-.543.086A14.995 14.995 0 0118 18.001v1m6-5h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    Manage Users
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Roles</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">With Permissions</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['with_permissions']) }}</p>
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
                        <p class="text-sm font-medium text-gray-600">Without Permissions</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['without_permissions']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0c-.181.028-.362.056-.543.086A14.995 14.995 0 0118 18.001v1m6-5h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Users with Roles</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['users_with_roles']) }}</p>
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
        <form method="GET" action="{{ route('admin.roles.index') }}" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Search by role name..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Sort Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                        <option value="users_count" {{ request('sort') == 'users_count' ? 'selected' : '' }}>User Count</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                    <select name="order" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="mt-6 flex justify-between">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Apply Filters
                </button>
                <a href="{{ route('admin.roles.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Roles Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Guard
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Users
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Permissions
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Created
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($roles as $role)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border border-gray-200">
                                        <span class="text-sm font-semibold text-blue-700">
                                            {{ strtoupper(substr($role->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ ucfirst($role->name) }}
                                        </div>
                                        @if($role->name === 'admin')
                                            <div class="text-xs text-red-600 mt-1">
                                                <span class="px-1 bg-red-100 rounded">Admin Role</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                    {{ $role->guard_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $role->users_count }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Users
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($role->permissions->take(3) as $permission)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            {{ str_replace('.', ' ', $permission->name) }}
                                        </span>
                                    @endforeach
                                    @if($role->permissions->count() > 3)
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                            +{{ $role->permissions->count() - 3 }} more
                                        </span>
                                    @endif
                                    @if($role->permissions->count() === 0)
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                            No permissions
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $role->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $role->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button class="edit-role-btn inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                            data-role-id="{{ $role->id }}"
                                            data-role-name="{{ $role->name }}"
                                            data-role-guard="{{ $role->guard_name }}">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>
                                    @if($role->name !== 'admin')
                                    <button class="delete-role-btn inline-flex items-center px-3 py-1.5 border border-transparent rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                            data-role-id="{{ $role->id }}"
                                            data-role-name="{{ $role->name }}"
                                            data-role-users="{{ $role->users_count }}">
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
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="mt-2">No roles found</p>
                                @if(request()->has('q'))
                                    <p class="text-sm text-gray-400 mt-1">Try adjusting your search</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($roles->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $roles->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add/Edit Role Modal -->
<div id="roleModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add New Role</h3>
            <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="roleForm" method="POST">
            @csrf
            <input type="hidden" id="roleId" name="id" value="">
            <div class="p-6 overflow-y-auto max-h-[60vh] space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Role Name *</label>
                        <input type="text" id="name" name="name" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="guard_name" class="block text-sm font-medium text-gray-700">Guard Name *</label>
                        <select id="guard_name" name="guard_name" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="web">Web</option>
                            <option value="api">API</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
                    <div class="space-y-4">
                        @foreach($permissions as $module => $modulePermissions)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-3 capitalize">{{ str_replace('_', ' ', $module) }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach($modulePermissions as $permission)
                                        <div class="flex items-center">
                                            <input type="checkbox"
                                                   id="perm_{{ $permission->name }}"
                                                   name="permissions[]"
                                                   value="{{ $permission->name }}"
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="perm_{{ $permission->name }}" class="ml-2 text-sm text-gray-700">
                                                {{ ucfirst(str_replace('.', ' ', $permission->name)) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
                <button type="button" id="cancelModal"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span id="submitButtonText">Create Role</span>
                </button>
            </div>
        </form>
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
                    <h3 id="confirmTitle" class="text-lg font-semibold text-gray-900">Delete Role</h3>
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
                Delete Role
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
    let currentRoleId = null;

    // Modal elements
    const roleModal = document.getElementById('roleModal');
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

    // Add Role Button
    document.getElementById('addRoleBtn').addEventListener('click', () => {
        document.getElementById('modalTitle').textContent = 'Add New Role';
        document.getElementById('submitButtonText').textContent = 'Create Role';
        document.getElementById('roleForm').reset();
        document.getElementById('roleId').value = '';
        document.getElementById('guard_name').value = 'web';

        // Uncheck all permissions
        document.querySelectorAll('#roleForm input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
        });

        roleModal.classList.remove('hidden');
    });

    // Edit Role Buttons
    document.querySelectorAll('.edit-role-btn').forEach(button => {
        button.addEventListener('click', async () => {
            const roleId = button.dataset.roleId;
            currentRoleId = roleId;

            document.getElementById('modalTitle').textContent = 'Edit Role';
            document.getElementById('submitButtonText').textContent = 'Update Role';
            document.getElementById('roleForm').reset();
            document.getElementById('roleId').value = roleId;

            // Populate form data
            document.getElementById('name').value = button.dataset.roleName;
            document.getElementById('guard_name').value = button.dataset.roleGuard;

            // Uncheck all permissions first
            document.querySelectorAll('#roleForm input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
            });

            try {
                // Load role permissions
                const response = await fetch(`/admin/roles/${roleId}/permissions`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Check the role's permissions
                    data.permissions.forEach(permissionName => {
                        const checkbox = document.getElementById(`perm_${permissionName}`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading permissions:', error);
            }

            roleModal.classList.remove('hidden');
        });
    });

    // Delete Role Buttons
    document.querySelectorAll('.delete-role-btn').forEach(button => {
        button.addEventListener('click', () => {
            const roleId = button.dataset.roleId;
            const roleName = button.dataset.roleName;
            const userCount = button.dataset.roleUsers;

            currentRoleId = roleId;

            if (parseInt(userCount) > 0) {
                document.getElementById('confirmTitle').textContent = `Cannot Delete ${roleName}`;
                document.getElementById('confirmMessage').textContent =
                    `This role has ${userCount} user(s) assigned. Please reassign users before deleting the role.`;
                document.getElementById('confirmDelete').disabled = true;
                document.getElementById('confirmDelete').classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                document.getElementById('confirmTitle').textContent = `Delete ${roleName}`;
                document.getElementById('confirmMessage').textContent =
                    `Are you sure you want to delete the "${roleName}" role? This action cannot be undone.`;
                document.getElementById('confirmDelete').disabled = false;
                document.getElementById('confirmDelete').classList.remove('opacity-50', 'cursor-not-allowed');
            }

            confirmationModal.classList.remove('hidden');
        });
    });

    // Close modal buttons
    document.getElementById('closeModal').addEventListener('click', () => roleModal.classList.add('hidden'));
    document.getElementById('cancelModal').addEventListener('click', () => roleModal.classList.add('hidden'));
    document.getElementById('cancelDelete').addEventListener('click', () => {
        confirmationModal.classList.add('hidden');
        document.getElementById('confirmDelete').disabled = false;
        document.getElementById('confirmDelete').classList.remove('opacity-50', 'cursor-not-allowed');
    });

    // Close modals on outside click
    [roleModal, confirmationModal].forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });

    // Role form submission
    document.getElementById('roleForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const roleId = document.getElementById('roleId').value;
        const isEdit = !!roleId;
        const url = isEdit ? `/admin/roles/${roleId}` : '/admin/roles';
        const method = isEdit ? 'PUT' : 'POST';

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        // Handle permissions array
        const permissions = formData.getAll('permissions[]');
        data.permissions = permissions;

        try {
            const response = await fetch(url, {
                method: method,
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
                roleModal.classList.add('hidden');
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

    // Confirm delete
    document.getElementById('confirmDelete').addEventListener('click', async () => {
        if (document.getElementById('confirmDelete').disabled) return;

        try {
            const response = await fetch(`/admin/roles/${currentRoleId}`, {
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
            showToast('Error deleting role: ' + error.message, 'error');
        }

        confirmationModal.classList.add('hidden');
    });
});
</script>
@endpush
@endsection
