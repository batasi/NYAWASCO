@extends('layouts.app')

@section('title', 'Javent')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">
                        Admin Dashboard
                    </h1>
                    <p class="mt-2 text-lg text-gray-600">
                        Manage the Javent platform.
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                    <a href="{{ route('admin.users') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Manage Users
                    </a>
                    <a href=""
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Manage Events
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $total_users }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="{{ route('admin.users') }}" class="font-medium text-blue-600 hover:text-blue-700">
                            View all users
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total Events -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Events</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $total_events }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="" class="font-medium text-green-600 hover:text-green-700">
                            View all events
                        </a>
                    </div>
                </div>
            </div>

            <!-- Voting Contests -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Voting Contests</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $total_voting_contests }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <a href="" class="font-medium text-purple-600 hover:text-purple-700">
                            View all contests
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ticket Sales -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Ticket Sales</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $total_ticket_sales }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-gray-600">
                            Total platform sales
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Users -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Users</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @if($recent_users->count() > 0)
                    @foreach($recent_users as $user)
                    @php
                    $roleName = $user->roles->first()?->name ?? 'attendee';
                    @endphp
                    <div class="p-6 hover:bg-gray-50 transition duration-150 ease-in-out">
                        <div class="flex items-center space-x-4">
                            @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}"
                                alt="{{ $user->name }}"
                                class="h-10 w-10 rounded-full object-cover">
                            @else
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                            </div>

                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $roleName === 'admin' ? 'bg-red-100 text-red-800' :
                                   ($roleName === 'organizer' ? 'bg-blue-100 text-blue-800' :
                                   ($roleName === 'vendor' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($roleName) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 flex space-x-2">
                            <button type="button"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 edit-user-btn"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ $user->name }}"
                                data-user-email="{{ $user->email }}"
                                data-user-role="{{ $roleName }}">
                                Edit
                            </button>

                            <button type="button"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 roles-permissions-btn"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ $user->name }}"
                                data-user-role="{{ $roleName }}">
                                Roles & Permissions
                            </button>

                            <button type="button"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 delete-user-btn"
                                data-user-id="{{ $user->id }}"
                                data-user-name="{{ $user->name }}">
                                Delete
                            </button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="p-6 text-center">
                        <p class="text-sm text-gray-500">No users found.</p>
                    </div>
                    @endif
                </div>
            </div>


            <!-- Pending Approvals -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Pending Approvals</h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $pending_approvals }} items need approval</h3>
                        <p class="mt-1 text-sm text-gray-500">Events and voting contests waiting for review.</p>
                        <div class="mt-6">
                            <a href="" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Review Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Edit User
                    </h3>
                    <div class="mt-2">
                        <form id="editUserForm">
                            @csrf
                            <input type="hidden" id="edit_user_id" name="user_id">
                            <div class="space-y-4">
                                <div>
                                    <label for="edit_name" class="block text-sm font-medium text-gray-700 text-left">Name</label>
                                    <input type="text" name="name" id="edit_name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="edit_email" class="block text-sm font-medium text-gray-700 text-left">Email</label>
                                    <input type="email" name="email" id="edit_email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="edit_role" class="block text-sm font-medium text-gray-700 text-left">Role</label>
                                    <select id="edit_role" name="role" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="user">User</option>
                                        <option value="organizer">Organizer</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <button type="button" id="updateUserBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                    Update User
                </button>
                <button type="button" id="closeEditModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div id="deleteUserModal" class="fixed inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Delete User
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete <span id="deleteUserName" class="font-medium"></span>? This action cannot be undone.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <form id="deleteUserForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="delete_user_id" name="user_id">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                </form>
                <button type="button" id="closeDeleteModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Roles & Permissions Modal -->
<div id="rolesPermissionsModal" class="fixed inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
            <div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Manage Roles & Permissions
                    </h3>
                    <div class="mt-2">
                        <form id="rolesPermissionsForm">
                            @csrf
                            <input type="hidden" id="rp_user_id" name="user_id">
                            <div class="space-y-6">
                                <div>
                                    <label for="rp_user_name" class="block text-sm font-medium text-gray-700 text-left">User</label>
                                    <p id="rp_user_name" class="mt-1 text-sm text-gray-900 font-semibold"></p>
                                </div>
                                <div>
                                    <label for="rp_role" class="block text-sm font-medium text-gray-700 text-left">Role</label>
                                    <select id="rp_role" name="role" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="attendee">Attendee</option>
                                        <option value="organizer">Organizer</option>
                                        <option value="vendor">Vendor</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>

                                <!-- Permissions Sections -->
                                <div id="permissionsSections" class="space-y-4">
                                    <!-- Permissions will be loaded dynamically here -->
                                    <div class="text-center py-4">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                                        <p class="mt-2 text-sm text-gray-500">Loading permissions...</p>
                                    </div>
                                </div>

                                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">
                                                Permission Note
                                            </h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>Changing roles will automatically assign default permissions for that role. You can override them by selecting specific permissions above.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <button type="button" id="updateRolesPermissionsBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                    Update Permissions
                </button>
                <button type="button" id="closeRolesPermissionsModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Edit User Modal
        const editUserModal = document.getElementById('editUserModal');
        const editUserBtns = document.querySelectorAll('.edit-user-btn');
        const closeEditModal = document.getElementById('closeEditModal');
        const updateUserBtn = document.getElementById('updateUserBtn');

        editUserBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                const userEmail = this.getAttribute('data-user-email');
                const userRole = this.getAttribute('data-user-role');

                document.getElementById('edit_user_id').value = userId;
                document.getElementById('edit_name').value = userName;
                document.getElementById('edit_email').value = userEmail;
                document.getElementById('edit_role').value = userRole;

                editUserModal.classList.remove('hidden');
            });
        });

        closeEditModal.addEventListener('click', function() {
            editUserModal.classList.add('hidden');
        });

        updateUserBtn.addEventListener('click', function() {
            const userId = document.getElementById('edit_user_id').value;
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('_method', 'PUT');
            formData.append('name', document.getElementById('edit_name').value);
            formData.append('email', document.getElementById('edit_email').value);
            formData.append('role', document.getElementById('edit_role').value);

            fetch(`/admin/users/${userId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('User updated successfully', 'success');
                        editUserModal.classList.add('hidden');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while updating the user', 'error');
                });
        });

        // Delete User Modal
        const deleteUserModal = document.getElementById('deleteUserModal');
        const deleteUserBtns = document.querySelectorAll('.delete-user-btn');
        const closeDeleteModal = document.getElementById('closeDeleteModal');

        deleteUserBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');

                document.getElementById('delete_user_id').value = userId;
                document.getElementById('deleteUserName').textContent = userName;

                deleteUserModal.classList.remove('hidden');
            });
        });

        closeDeleteModal.addEventListener('click', function() {
            deleteUserModal.classList.add('hidden');
        });

        // Delete form submission
        document.getElementById('deleteUserForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const userId = document.getElementById('delete_user_id').value;
            const formData = new FormData(this);

            fetch(`/admin/users/${userId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('User deleted successfully', 'success');
                        deleteUserModal.classList.add('hidden');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while deleting the user', 'error');
                });
        });

        // Roles & Permissions Modal
        const rolesPermissionsModal = document.getElementById('rolesPermissionsModal');
        const rolesPermissionsBtns = document.querySelectorAll('.roles-permissions-btn');
        const closeRolesPermissionsModal = document.getElementById('closeRolesPermissionsModal');
        const updateRolesPermissionsBtn = document.getElementById('updateRolesPermissionsBtn');

        rolesPermissionsBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                const userRole = this.getAttribute('data-user-role');

                document.getElementById('rp_user_id').value = userId;
                document.getElementById('rp_user_name').textContent = userName;
                document.getElementById('rp_role').value = userRole;

                // Load user permissions and render sections
                loadUserPermissions(userId);

                rolesPermissionsModal.classList.remove('hidden');
            });
        });

        closeRolesPermissionsModal.addEventListener('click', function() {
            rolesPermissionsModal.classList.add('hidden');
        });

        updateRolesPermissionsBtn.addEventListener('click', function() {
            const userId = document.getElementById('rp_user_id').value;
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('role', document.getElementById('rp_role').value);

            // Get selected permissions
            const selectedPermissions = [];
            document.querySelectorAll('input[name="permissions[]"]:checked').forEach(checkbox => {
                selectedPermissions.push(checkbox.value);
            });
            formData.append('permissions', selectedPermissions);

            fetch(`/admin/users/${userId}/permissions`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('User permissions updated successfully', 'success');
                        rolesPermissionsModal.classList.add('hidden');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while updating permissions', 'error');
                });
        });

        // Load user permissions and render permission sections
        function loadUserPermissions(userId) {
            fetch(`/admin/users/${userId}/permissions`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    renderPermissionSections(data.permissions, data.user_permissions);
                })
                .catch(error => {
                    console.error('Error loading permissions:', error);
                    showNotification('Failed to load permissions', 'error');
                });
        }

        // Render permission sections dynamically
        function renderPermissionSections(permissions, userPermissions) {
            const container = document.getElementById('permissionsSections');
            container.innerHTML = '';

            const sectionTitles = {
                'events': 'Event Permissions',
                'voting': 'Voting Permissions',
                'user_management': 'User Management',
                'organizer_specific': 'Organizer Specific',
                'vendor_specific': 'Vendor Specific',
                'system': 'System Access',
                'general': 'General Permissions'
            };

            Object.keys(permissions).forEach(section => {
                const sectionPermissions = permissions[section];
                if (sectionPermissions.length === 0) return;

                const sectionHtml = `
                    <div class="border border-gray-200 rounded-lg">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900">${sectionTitles[section] || section}</h4>
                        </div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                            ${sectionPermissions.map(permission => `
                                <div class="flex items-center">
                                    <input id="perm_${permission.name}"
                                           name="permissions[]"
                                           type="checkbox"
                                           value="${permission.name}"
                                           ${userPermissions.includes(permission.name) ? 'checked' : ''}
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    <label for="perm_${permission.name}" class="ml-2 block text-sm text-gray-900 capitalize">
                                        ${permission.name.replace(/_/g, ' ')}
                                    </label>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;

                container.innerHTML += sectionHtml;
            });
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === editUserModal) {
                editUserModal.classList.add('hidden');
            }
            if (event.target === deleteUserModal) {
                deleteUserModal.classList.add('hidden');
            }
            if (event.target === rolesPermissionsModal) {
                rolesPermissionsModal.classList.add('hidden');
            }
        });

        // Notification function
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg text-white ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                'bg-blue-500'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    });
</script>
@endsection