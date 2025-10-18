@extends('layouts.app')

@section('title', 'Manage Users - Javent')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                <button onclick="openUserModal()"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Add New User
                </button>
            </div>

            <!-- Users Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                        <!-- Users will be loaded here via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include the modals -->
@include('admin.users.modals.user-management-modal')
@include('admin.users.modals.role-management-modal')
@endsection

@push('scripts')
<script>
    // Load users when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadUsersTable();
    });

    async function loadUsersTable() {
        try {
            const response = await fetch('/admin/users/data');
            const data = await response.json();

            const tbody = document.getElementById('usersTableBody');
            tbody.innerHTML = '';

            data.users.forEach(user => {
                const row = `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                ${user.avatar ?
                                    `<img class="h-10 w-10 rounded-full" src="${user.avatar}" alt="${user.name}">` :
                                    `<div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-600">${user.name.charAt(0).toUpperCase()}</span>
                                    </div>`
                                }
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${user.name}</div>
                                <div class="text-sm text-gray-500">${user.email}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            ${user.role === 'admin' ? 'bg-red-100 text-red-800' :
                               user.role === 'organizer' ? 'bg-blue-100 text-blue-800' :
                               user.role === 'vendor' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                            ${user.role}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            ${user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${user.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${new Date(user.created_at).toLocaleDateString()}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="openUserModal(${user.id})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                        <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
            `;
                tbody.innerHTML += row;
            });

        } catch (error) {
            console.error('Error loading users:', error);
        }
    }
</script>
@endpush