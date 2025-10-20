<!-- Create User Modal -->
<div id="createUserModal" class="fixed inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Create New User
                    </h3>
                    <div class="mt-2">
                        <form id="createUserForm">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="create_name" class="block text-sm font-medium text-gray-700 text-left">Name</label>
                                    <input type="text" name="name" id="create_name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="create_email" class="block text-sm font-medium text-gray-700 text-left">Email</label>
                                    <input type="email" name="email" id="create_email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="create_password" class="block text-sm font-medium text-gray-700 text-left">Password</label>
                                    <input type="password" name="password" id="create_password" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="create_password_confirmation" class="block text-sm font-medium text-gray-700 text-left">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="create_password_confirmation" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="create_role" class="block text-sm font-medium text-gray-700 text-left">Role</label>
                                    <select id="create_role" name="role" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="">Select Role</option>
                                        <option value="attendee">Attendee</option>
                                        <option value="organizer">Organizer</option>
                                        <option value="vendor">Vendor</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <button type="button" id="createUserSubmit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                    Create User
                </button>
                <button type="button" id="closeCreateModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </button>
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
                            @method('PUT')
                            <input type="hidden" id="edit_user_id" name="user_id">
                            <div class="space-y-4">
                                <div>
                                    <label for="edit_name" class="block text-sm font-medium text-gray-700 text-left">Name</label>
                                    <input type="text" name="name" id="edit_name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="edit_email" class="block text-sm font-medium text-gray-700 text-left">Email</label>
                                    <input type="email" name="email" id="edit_email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="edit_role" class="block text-sm font-medium text-gray-700 text-left">Role</label>
                                    <select id="edit_role" name="role" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="attendee">Attendee</option>
                                        <option value="organizer">Organizer</option>
                                        <option value="vendor">Vendor</option>
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
<div id="rolesPermissionsModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white" id="modal-title">
                                Manage Roles & Permissions
                            </h3>
                            <p class="text-blue-100 text-sm">Configure user access and capabilities</p>
                        </div>
                    </div>
                    <button type="button" id="closeRolesPermissionsModal" class="text-white hover:text-blue-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-white px-6 py-6 max-h-96 overflow-y-auto">
                <form id="rolesPermissionsForm">
                    @csrf
                    <input type="hidden" id="rp_user_id" name="user_id">

                    <div class="space-y-6">
                        <!-- User Info -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <div class="flex items-center space-x-4">
                                <div id="rp_user_avatar" class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border-2 border-white shadow-sm">
                                    <!-- Avatar will be dynamically set -->
                                </div>
                                <div>
                                    <h4 id="rp_user_name" class="text-lg font-semibold text-gray-900"></h4>
                                    <p id="rp_user_email" class="text-sm text-gray-600"></p>
                                    <p class="text-xs text-gray-500">User ID: <span id="rp_user_id_display"></span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Role Selection -->
                        <div>
                            <label for="rp_role" class="block text-sm font-semibold text-gray-900 mb-3">User Role</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="role" value="attendee" class="sr-only peer">
                                    <div class="w-full p-3 border-2 border-gray-200 rounded-xl text-center transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:ring-2 peer-checked:ring-blue-200 hover:border-gray-300">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <span class="block text-sm font-medium text-gray-900">Attendee</span>
                                        <span class="block text-xs text-gray-500 mt-1">Event Participant</span>
                                    </div>
                                </label>

                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="role" value="organizer" class="sr-only peer">
                                    <div class="w-full p-3 border-2 border-gray-200 rounded-xl text-center transition-all duration-200 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:ring-2 peer-checked:ring-green-200 hover:border-gray-300">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <span class="block text-sm font-medium text-gray-900">Organizer</span>
                                        <span class="block text-xs text-gray-500 mt-1">Event Creator</span>
                                    </div>
                                </label>

                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="role" value="vendor" class="sr-only peer">
                                    <div class="w-full p-3 border-2 border-gray-200 rounded-xl text-center transition-all duration-200 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:ring-2 peer-checked:ring-purple-200 hover:border-gray-300">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                        <span class="block text-sm font-medium text-gray-900">Vendor</span>
                                        <span class="block text-xs text-gray-500 mt-1">Service Provider</span>
                                    </div>
                                </label>

                                <label class="relative flex cursor-pointer">
                                    <input type="radio" name="role" value="admin" class="sr-only peer">
                                    <div class="w-full p-3 border-2 border-gray-200 rounded-xl text-center transition-all duration-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:ring-2 peer-checked:ring-red-200 hover:border-gray-300">
                                        <div class="w-8 h-8 mx-auto mb-2 bg-red-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                        <span class="block text-sm font-medium text-gray-900">Admin</span>
                                        <span class="block text-xs text-gray-500 mt-1">System Administrator</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Permissions Sections -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-semibold text-gray-900">Advanced Permissions</label>
                                <div class="flex items-center space-x-2">
                                    <button type="button" id="selectAllPermissions" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                        Select All
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    <button type="button" id="deselectAllPermissions" class="text-xs text-gray-600 hover:text-gray-700 font-medium">
                                        Deselect All
                                    </button>
                                </div>
                            </div>

                            <div id="permissionsSections" class="space-y-4">
                                <!-- Loading state -->
                                <div class="text-center py-8">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                                    <p class="mt-3 text-sm text-gray-500">Loading permissions configuration...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Warning Note -->
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-amber-800">
                                        Important Note
                                    </h3>
                                    <div class="mt-1 text-sm text-amber-700">
                                        <p>Changing roles will automatically assign default permissions. You can override them by selecting specific permissions above.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <span id="selectedPermissionsCount">0</span> permissions selected
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" id="closeRolesPermissionsModalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="button" id="updateRolesPermissionsBtn" class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-sm hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Update Permissions</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>