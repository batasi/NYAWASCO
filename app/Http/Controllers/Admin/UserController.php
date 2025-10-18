<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function index()
    {
        $total_users = User::count();
        $total_events = \App\Models\Event::count();
        $total_voting_contests = \App\Models\VotingContest::count();
        $total_ticket_sales = \App\Models\Ticket::count();
        $pending_approvals = \App\Models\Event::where('status', 'pending')->count() +
            \App\Models\VotingContest::where('status', 'pending')->count();

        $recent_users = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'total_users',
            'total_events',
            'total_voting_contests',
            'total_ticket_sales',
            'pending_approvals',
            'recent_users'
        ));
    }

    /**
     * Users Management Page
     */
    public function usersIndex()
    {
        $roles = Role::all();
        return view('admin.users.index', compact('roles'));
    }

    /**
     * Get Users Data for DataTables
     */
    public function getUsersData(Request $request)
    {
        $users = User::with('roles')->select('users.*');

        return DataTables::of($users)
            ->addColumn('role', function ($user) {
                $role = $user->roles->first();
                return $role ? ucfirst($role->name) : 'No Role';
            })
            ->addColumn('status', function ($user) {
                $status = $user->is_active ? 'Active' : 'Inactive';
                $color = $user->is_active ? 'green' : 'red';
                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-'
                    . $color . '-100 text-' . $color . '-800">' . $status . '</span>';
            })
            ->addColumn('actions', function ($user) {
                return '
                <div class="flex space-x-2">
                    <button class="edit-user-btn inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            data-user-id="' . $user->id . '"
                            data-user-name="' . $user->name . '"
                            data-user-email="' . $user->email . '"
                            data-user-role="' . ($user->roles->first() ? $user->roles->first()->name : 'user') . '">
                        Edit
                    </button>
                    <button class="roles-permissions-btn inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                            data-user-id="' . $user->id . '"
                            data-user-name="' . $user->name . '"
                            data-user-role="' . ($user->roles->first() ? $user->roles->first()->name : 'user') . '">
                        Permissions
                    </button>
                    <button class="delete-user-btn inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            data-user-id="' . $user->id . '"
                            data-user-name="' . $user->name . '">
                        Delete
                    </button>
                </div>
            ';
            })
            ->addColumn('created_at_formatted', function ($user) {
                return $user->created_at->format('M j, Y g:i A');
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }


    /**
     * Show Create User Form
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store New User
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'is_active' => true,
                ]);

                $user->assignRole($request->role);
            });

            return response()->json([
                'success' => true,
                'message' => 'User created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show User Details
     */
    public function show(User $user)
    {
        $user->load('roles', 'permissions');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show Edit User Form
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update User
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,organizer,vendor,attendee'
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                // Update user details
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);

                // Remove all existing roles and assign new one
                $user->syncRoles([$request->role]);
            });

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete User
     */
    public function destroy(User $user)
    {
        // Prevent users from deleting themselves
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 403);
        }

        try {
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update User Permissions
     */
    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,organizer,vendor,attendee',
            'permissions' => 'sometimes|array',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                // Update role
                $user->syncRoles([$request->role]);

                // If specific permissions are provided, sync them
                if ($request->has('permissions') && !empty($request->permissions)) {
                    // Validate permissions exist
                    $validPermissions = Permission::whereIn('name', $request->permissions)->pluck('name')->toArray();
                    $user->syncPermissions($validPermissions);
                } else {
                    // If no specific permissions, clear direct permissions
                    $user->permissions()->detach();
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'User permissions updated successfully',
                'user' => $user->load(['roles', 'permissions'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get User Permissions
     */
    public function getPermissions(User $user)
    {
        // Group permissions by category for better organization
        $permissions = Permission::all()->groupBy(function ($permission) {
            if (str_contains($permission->name, 'event')) {
                return 'events';
            } elseif (str_contains($permission->name, 'voting')) {
                return 'voting';
            } elseif (str_contains($permission->name, 'user') || str_contains($permission->name, 'role')) {
                return 'user_management';
            } elseif (str_contains($permission->name, 'own')) {
                return 'organizer_specific';
            } elseif (str_contains($permission->name, 'service') || str_contains($permission->name, 'booking')) {
                return 'vendor_specific';
            } elseif (str_contains($permission->name, 'admin') || str_contains($permission->name, 'system')) {
                return 'system';
            } else {
                return 'general';
            }
        });

        $userPermissions = $user->getDirectPermissions()->pluck('name')->toArray();
        $userRole = $user->roles->first();

        return response()->json([
            'user' => $user->load('roles'),
            'permissions' => $permissions,
            'user_permissions' => $userPermissions,
            'user_role' => $userRole
        ]);
    }

    /**
     * Toggle User Status
     */
    public function toggleStatus(User $user)
    {
        try {
            $user->update([
                'is_active' => !$user->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully',
                'is_active' => $user->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status: ' . $e->getMessage()
            ], 500);
        }
    }
}
