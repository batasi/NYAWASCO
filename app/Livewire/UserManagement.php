<?php

namespace App\Livewire;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Department;
use App\Models\ActivityLog;
use App\Models\Ward;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'users';
    public $showUserModal = false;
    public $showRoleModal = false;
    public $showForcePasswordModal = false;
    public $editingUserId = null;
    public $editingRoleId = null;

    public $userForm = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'department_id' => '',
        'role_id' => '',
        'is_active' => true,
        'password' => '',
        'password_confirmation' => '',
    ];

    public $roleForm = [
        'name' => '',
        'description' => '',
        'permissions' => [],
    ];

    public $forcePasswordForm = [
        'user_id' => '',
        'new_password' => '',
        'new_password_confirmation' => '',
        'notify_user' => true,
    ];

    public $selectedUsers = [];
    public $selectAll = false;

    public $filterRole = '';
    public $filterStatus = '';
    public $filterDepartment = '';
    public $showOnlineOnly = false;

    public $passwordExpiryEnabled = false;
    public $passwordExpiryDays = 90;
    public $csvFile = null;

    protected $listeners = ['refreshOnlineStatus'];

    public function mount()
    {
        $this->resetUserForm();
        $this->resetRoleForm();
        $this->resetForcePasswordForm();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedUsers = $this->users->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function openUserModal($userId = null)
    {
        $this->editingUserId = $userId;
        $this->showUserModal = true;

        if ($userId) {
            $this->loadUserData($userId);
        } else {
            $this->resetUserForm();
        }
    }

    public function closeUserModal()
    {
        $this->showUserModal = false;
        $this->editingUserId = null;
        $this->resetUserForm();
    }

    public function openRoleModal($roleId = null)
    {
        $this->editingRoleId = $roleId;
        $this->showRoleModal = true;

        if ($roleId) {
            $this->loadRoleData($roleId);
        } else {
            $this->resetRoleForm();
        }
    }

    public function closeRoleModal()
    {
        $this->showRoleModal = false;
        $this->editingRoleId = null;
        $this->resetRoleForm();
    }

    public function openForcePasswordChangeModal()
    {
        $this->showForcePasswordModal = true;
        $this->resetForcePasswordForm();
    }

    public function closeForcePasswordModal()
    {
        $this->showForcePasswordModal = false;
        $this->resetForcePasswordForm();
    }

    private function loadUserData($userId)
    {
        $user = User::with('roles', 'department')->find($userId);

        $this->userForm = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'department_id' => $user->department_id ?? '',
            'role_id' => $user->roles->first()?->id ?? '',
            'is_active' => $user->is_active ?? true,
            'password' => '',
            'password_confirmation' => '',
        ];
    }

    private function loadRoleData($roleId)
    {
        $role = Role::with('permissions')->find($roleId);

        $this->roleForm = [
            'name' => $role->name,
            'description' => $role->description ?? '',
            'permissions' => $role->permissions->pluck('id')->toArray(),
        ];
    }

    private function resetUserForm()
    {
        $this->userForm = [
            'name' => '',
            'email' => '',
            'phone' => '',
            'department_id' => '',
            'role_id' => '',
            'is_active' => true,
            'password' => '',
            'password_confirmation' => '',
        ];
    }

    private function resetRoleForm()
    {
        $this->roleForm = [
            'name' => '',
            'description' => '',
            'permissions' => [],
        ];
    }

    private function resetForcePasswordForm()
    {
        $this->forcePasswordForm = [
            'user_id' => '',
            'new_password' => '',
            'new_password_confirmation' => '',
            'notify_user' => true,
        ];
    }

    public function saveUser()
    {
        $this->validateUserForm();

        try {
            DB::beginTransaction();

            if ($this->editingUserId) {
                $this->updateUser();
            } else {
                $this->createUser();
            }

            DB::commit();

            session()->flash('message', 'User ' . ($this->editingUserId ? 'updated' : 'created') . ' successfully!');
            $this->closeUserModal();
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User Save Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while saving the user. Please try again.');
        }
    }

    private function validateUserForm()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email' . ($this->editingUserId ? ',' . $this->editingUserId : ''),
            'phone' => 'nullable|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'role_id' => 'nullable|exists:roles,id',
            'is_active' => 'boolean',
        ];

        if (!$this->editingUserId) {
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['password_confirmation'] = 'required|string';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
            $rules['password_confirmation'] = 'nullable|string';
        }

        $this->validate($rules, [], [
            'userForm.name' => 'name',
            'userForm.email' => 'email',
            'userForm.phone' => 'phone',
            'userForm.department_id' => 'department',
            'userForm.role_id' => 'role',
            'userForm.is_active' => 'active status',
            'userForm.password' => 'password',
            'userForm.password_confirmation' => 'password confirmation',
        ]);
    }

    private function createUser()
    {
        $user = User::create([
            'name' => $this->userForm['name'],
            'email' => $this->userForm['email'],
            'phone' => $this->userForm['phone'],
            'department_id' => $this->userForm['department_id'] ?: null,
            'is_active' => $this->userForm['is_active'],
            'password' => Hash::make($this->userForm['password']),
            'email_verified_at' => now(),
        ]);

        if ($this->userForm['role_id']) {
            $user->roles()->attach($this->userForm['role_id']);
        }

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'model_type' => 'User',
            'model_id' => $user->id,
            'description' => 'Created new user: ' . $user->name,
            'ip_address' => request()->ip(),
        ]);
    }

    private function updateUser()
    {
        $user = User::find($this->editingUserId);

        $updateData = [
            'name' => $this->userForm['name'],
            'email' => $this->userForm['email'],
            'phone' => $this->userForm['phone'],
            'department_id' => $this->userForm['department_id'] ?: null,
            'is_active' => $this->userForm['is_active'],
        ];

        if ($this->userForm['password']) {
            $updateData['password'] = Hash::make($this->userForm['password']);
        }

        $user->update($updateData);

        // Update role
        $user->roles()->detach();
        if ($this->userForm['role_id']) {
            $user->roles()->attach($this->userForm['role_id']);
        }

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'model_type' => 'User',
            'model_id' => $user->id,
            'description' => 'Updated user: ' . $user->name,
            'ip_address' => request()->ip(),
        ]);
    }

    public function saveRole()
    {
        $this->validateRoleForm();

        try {
            DB::beginTransaction();

            if ($this->editingRoleId) {
                $this->updateRole();
            } else {
                $this->createRole();
            }

            DB::commit();

            session()->flash('message', 'Role ' . ($this->editingRoleId ? 'updated' : 'created') . ' successfully!');
            $this->closeRoleModal();
            $this->dispatch('refreshComponent');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role Save Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while saving the role. Please try again.');
        }
    }

    private function validateRoleForm()
    {
        $this->validate([
            'roleForm.name' => 'required|string|max:255|unique:roles,name' . ($this->editingRoleId ? ',' . $this->editingRoleId : ''),
            'roleForm.description' => 'nullable|string|max:1000',
            'roleForm.permissions' => 'array',
            'roleForm.permissions.*' => 'exists:permissions,id',
        ], [], [
            'roleForm.name' => 'name',
            'roleForm.description' => 'description',
            'roleForm.permissions' => 'permissions',
        ]);
    }

    private function createRole()
    {
        $role = Role::create([
            'name' => $this->roleForm['name'],
            'description' => $this->roleForm['description'],
            'guard_name' => 'web',
        ]);

        if (!empty($this->roleForm['permissions'])) {
            $role->permissions()->attach($this->roleForm['permissions']);
        }

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'model_type' => 'Role',
            'model_id' => $role->id,
            'description' => 'Created new role: ' . $role->name,
            'ip_address' => request()->ip(),
        ]);
    }

    private function updateRole()
    {
        $role = Role::find($this->editingRoleId);

        $role->update([
            'name' => $this->roleForm['name'],
            'description' => $this->roleForm['description'],
        ]);

        $role->permissions()->sync($this->roleForm['permissions'] ?? []);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'model_type' => 'Role',
            'model_id' => $role->id,
            'description' => 'Updated role: ' . $role->name,
            'ip_address' => request()->ip(),
        ]);
    }

    public function forcePasswordChange()
    {
        $this->validate([
            'forcePasswordForm.user_id' => 'required|exists:users,id',
            'forcePasswordForm.new_password' => 'required|string|min:8|confirmed',
            'forcePasswordForm.notify_user' => 'boolean',
        ]);

        try {
            $user = User::find($this->forcePasswordForm['user_id']);

            $user->update([
                'password' => Hash::make($this->forcePasswordForm['new_password']),
                'password_changed_at' => now(),
                'force_password_change' => true,
            ]);

            if ($this->forcePasswordForm['notify_user']) {
                // Send notification email
                Mail::to($user->email)->send(new \App\Mail\PasswordChangedNotification($user));
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'password_changed',
                'model_type' => 'User',
                'model_id' => $user->id,
                'description' => 'Forced password change for user: ' . $user->name,
                'ip_address' => request()->ip(),
            ]);

            session()->flash('message', 'Password changed successfully!');
            $this->closeForcePasswordModal();

        } catch (\Exception $e) {
            Log::error('Force Password Change Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while changing the password.');
        }
    }

    public function deleteUser($userId)
    {
        try {
            $user = User::find($userId);

            // Prevent deletion of super admin or current user
            if ($user->hasRole('super-admin') || $user->id === auth()->id()) {
                session()->flash('error', 'Cannot delete this user.');
                return;
            }

            $user->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'deleted',
                'model_type' => 'User',
                'model_id' => $userId,
                'description' => 'Deleted user: ' . $user->name,
                'ip_address' => request()->ip(),
            ]);

            session()->flash('message', 'User deleted successfully!');

        } catch (\Exception $e) {
            Log::error('User Delete Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while deleting the user.');
        }
    }

    public function deleteRole($roleId)
    {
        try {
            $role = Role::find($roleId);

            // Prevent deletion of system roles
            if ($role->is_system) {
                session()->flash('error', 'Cannot delete system roles.');
                return;
            }

            $role->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'deleted',
                'model_type' => 'Role',
                'model_id' => $roleId,
                'description' => 'Deleted role: ' . $role->name,
                'ip_address' => request()->ip(),
            ]);

            session()->flash('message', 'Role deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Role Delete Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while deleting the role.');
        }
    }

    public function toggleUserStatus($userId)
    {
        try {
            $user = User::find($userId);
            $user->update(['is_active' => !$user->is_active]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'status_changed',
                'model_type' => 'User',
                'model_id' => $user->id,
                'description' => ($user->is_active ? 'Activated' : 'Deactivated') . ' user: ' . $user->name,
                'ip_address' => request()->ip(),
            ]);

            session()->flash('message', 'User status updated successfully!');

        } catch (\Exception $e) {
            Log::error('User Status Toggle Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while updating user status.');
        }
    }

    public function bulkDeleteUsers()
    {
        if (empty($this->selectedUsers)) {
            session()->flash('error', 'No users selected.');
            return;
        }

        try {
            $users = User::whereIn('id', $this->selectedUsers)->get();

            // Prevent deletion of super admins or current user
            $protectedUsers = $users->filter(function ($user) {
                return $user->hasRole('super-admin') || $user->id === auth()->id();
            });

            if ($protectedUsers->count() > 0) {
                session()->flash('error', 'Cannot delete protected users.');
                return;
            }

            User::whereIn('id', $this->selectedUsers)->delete();

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'bulk_deleted',
                'model_type' => 'User',
                'description' => 'Bulk deleted ' . count($this->selectedUsers) . ' users',
                'ip_address' => request()->ip(),
            ]);

            $this->selectedUsers = [];
            $this->selectAll = false;

            session()->flash('message', 'Users deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Bulk User Delete Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while deleting users.');
        }
    }

    public function refreshOnlineStatus()
    {
        // This method is called periodically to refresh online status
        $this->dispatch('refreshComponent');
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function toggleOnlineFilter()
    {
        $this->showOnlineOnly = !$this->showOnlineOnly;
    }

    public function exportCsv()
    {
        // Implement CSV export
    }

    public function exportPdf()
    {
        // Implement PDF export
    }

    public function importCsv()
    {
        // Implement CSV import
    }

    public function exportAuditCsv()
    {
        // Implement audit CSV export
    }

    public function openPasswordModal($userId)
    {
        // Implement password modal
    }

    public function confirmDelete($id, $type)
    {
        // Implement delete confirmation
    }

    public function openCreatePermissionModal()
    {
        // Implement create permission modal
    }

    public function openPermissionModal($id)
    {
        // Implement permission modal
    }

    public function savePasswordPolicies()
    {
        // Implement save password policies
    }

    public function loadPasswordPolicies()
    {
        // Implement load password policies
    }

    public function runPasswordExpiryCheck()
    {
        // Implement password expiry check
    }

    public function getUsersProperty()
    {
        return User::with(['roles', 'department'])
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);
    }

    public function getRolesProperty()
    {
        return Role::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->paginate(10);
    }

    public function getPermissionsProperty()
    {
        return Permission::all();
    }

    public function getDepartmentsProperty()
    {
        return Department::where('is_active', true)->get();
    }

    public function getOnlineUsersProperty()
    {
        return User::where('last_activity', '>', now()->subMinutes(5))
            ->where('is_active', true)
            ->count();
    }

    public function getTotalUsersProperty()
    {
        return User::count();
    }

    public function getActiveUsersProperty()
    {
        return User::where('is_active', true)->count();
    }

    public function getTotalRolesProperty()
    {
        return Role::count();
    }

    public function getWardsProperty()
    {
        try {
            return \App\Models\Ward::all();
        } catch (\Exception $e) {
            // Return empty collection if table doesn't exist
            return collect([]);
        }
    }

    public function getPasswordAuditsProperty()
    {
        return \App\Models\ActivityLog::where('action', 'like', '%password%')
            ->orWhere('model_type', 'App\Models\User')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => $this->users,
            'roles' => $this->roles,
            'permissions' => $this->permissions,
            'departments' => $this->departments,
            'wards' => $this->wards,
            'onlineUsers' => $this->onlineUsers,
            'totalUsers' => $this->totalUsers,
            'activeUsers' => $this->activeUsers,
            'totalRoles' => $this->totalRoles,
            'passwordAudits' => $this->passwordAudits,
        ]);
    }
}