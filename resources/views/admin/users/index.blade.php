@extends('layouts.app')

@section('title', 'User Management - Javent')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">
                        User Management
                    </h1>
                    <p class="mt-2 text-lg text-gray-600">
                        Manage all users and their permissions.
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                    <button id="createUserBtn"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New User
                    </button>
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Users Table -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">All Users</h3>
                <p class="mt-1 text-sm text-gray-600">Manage user accounts, roles, and permissions.</p>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table id="usersTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the modals -->
@include('admin.partials.user-modals')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Initialize DataTable
        const table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.users.data") }}',
            columns: [{
                    data: 'name',
                    name: 'name',
                    render: function(data, type, row) {
                        return '<div class="flex items-center"><div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center mr-3"><span class="text-sm font-medium text-gray-600">' + data.charAt(0).toUpperCase() + '</span></div><div><div class="text-sm font-medium text-gray-900">' + data + '</div><div class="text-sm text-gray-500">' + row.email + '</div></div></div>';
                    }
                },
                {
                    data: 'role',
                    name: 'role'
                },
                {
                    data: 'status',
                    name: 'is_active',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at_formatted',
                    name: 'created_at'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [3, 'desc']
            ]
        });

        // Create User Modal
        const createUserBtn = document.getElementById('createUserBtn');
        const createUserModal = document.getElementById('createUserModal');
        const closeCreateModal = document.getElementById('closeCreateModal');
        const createUserSubmit = document.getElementById('createUserSubmit');

        if (createUserBtn && createUserModal) {
            createUserBtn.addEventListener('click', function() {
                createUserModal.classList.remove('hidden');
            });

            closeCreateModal.addEventListener('click', function() {
                createUserModal.classList.add('hidden');
                document.getElementById('createUserForm').reset();
            });

            createUserSubmit.addEventListener('click', function() {
                const formData = new FormData(document.getElementById('createUserForm'));

                fetch('{{ route("admin.users.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('User created successfully', 'success');
                            createUserModal.classList.add('hidden');
                            document.getElementById('createUserForm').reset();
                            table.ajax.reload();
                        } else {
                            showNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while creating the user', 'error');
                    });
            });
        }

        // Edit User Modal
        const editUserModal = document.getElementById('editUserModal');
        const closeEditModal = document.getElementById('closeEditModal');
        const updateUserBtn = document.getElementById('updateUserBtn');

        function openEditModal(userId, userName, userEmail, userRole) {
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_name').value = userName;
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_role').value = userRole;
            editUserModal.classList.remove('hidden');
        }

        closeEditModal.addEventListener('click', function() {
            editUserModal.classList.add('hidden');
        });

        updateUserBtn.addEventListener('click', function() {
            const userId = document.getElementById('edit_user_id').value;
            const formData = new FormData(document.getElementById('editUserForm'));

            fetch(`/admin/users/${userId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('User updated successfully', 'success');
                        editUserModal.classList.add('hidden');
                        table.ajax.reload();
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
        const closeDeleteModal = document.getElementById('closeDeleteModal');

        function openDeleteModal(userId, userName) {
            document.getElementById('delete_user_id').value = userId;
            document.getElementById('deleteUserName').textContent = userName;
            deleteUserModal.classList.remove('hidden');
        }

        closeDeleteModal.addEventListener('click', function() {
            deleteUserModal.classList.add('hidden');
        });

        document.getElementById('deleteUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const userId = document.getElementById('delete_user_id').value;
            const formData = new FormData(this);

            fetch(`/admin/users/${userId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('User deleted successfully', 'success');
                        deleteUserModal.classList.add('hidden');
                        table.ajax.reload();
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
        const closeRolesPermissionsModal = document.getElementById('closeRolesPermissionsModal');
        const updateRolesPermissionsBtn = document.getElementById('updateRolesPermissionsBtn');

        function openPermissionsModal(userId, userName, userRole) {
            document.getElementById('rp_user_id').value = userId;
            document.getElementById('rp_user_name').textContent = userName;
            document.getElementById('rp_role').value = userRole;
            loadUserPermissions(userId);
            rolesPermissionsModal.classList.remove('hidden');
        }

        closeRolesPermissionsModal.addEventListener('click', function() {
            rolesPermissionsModal.classList.add('hidden');
        });

        updateRolesPermissionsBtn.addEventListener('click', function() {
            const userId = document.getElementById('rp_user_id').value;
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('role', document.getElementById('rp_role').value);

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
                        table.ajax.reload();
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

        // Add event listeners for dynamically created buttons
        $(document).on('click', '.edit-user-btn', function() {
            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            const userEmail = $(this).data('user-email');
            const userRole = $(this).data('user-role');
            openEditModal(userId, userName, userEmail, userRole);
        });

        $(document).on('click', '.delete-user-btn', function() {
            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            openDeleteModal(userId, userName);
        });

        $(document).on('click', '.roles-permissions-btn', function() {
            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            const userRole = $(this).data('user-role');
            openPermissionsModal(userId, userName, userRole);
        });

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === createUserModal) {
                createUserModal.classList.add('hidden');
                document.getElementById('createUserForm').reset();
            }
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
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg text-white ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                'bg-blue-500'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    });
</script>
@endsection