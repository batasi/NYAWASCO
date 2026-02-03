<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display the user management page
     */
    public function index(Request $request)
    {
        $query = User::query()->with('roles');

        // Search functionality
        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->has('role') && $request->role != 'all') {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Status filter
        if ($request->has('status') && $request->status != 'all') {
            $isActive = $request->status == 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        // Sort
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        $query->orderBy($sort, $order);

        $users = $query->paginate(20);
        $roles = Role::all();
        $permissions = Permission::all();

        // Statistics
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', 1)->count(),
            'inactive' => User::where('is_active', 0)->count(),
            'admin' => User::whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'this_month' => User::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count(),
        ];

        return view('admin.users', compact('users', 'roles', 'permissions', 'stats'));
    }

    /**
     * Store New User with Spatie Roles
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|alpha_dash|unique:users,username',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'required|string|max:50',
            'is_active' => 'required|boolean',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $role = Role::firstOrCreate([
                    'name' => $request->role
                ]);

                $user = User::create([
                    'name'              => $request->name,
                    'username'          => strtolower($request->username),
                    'email'             => $request->email,
                    'email_verified_at' => now(),
                    'password'          => Hash::make($request->password),
                    'role'              => $role->name,
                    'is_active'         => $request->boolean('is_active'),
                ]);

                $user->assignRole($role);
            });

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update User
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|alpha_dash|unique:users,username,' . $user->id,
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'role'      => 'required|string|max:50',
            'is_active' => 'required|boolean',
            'password'  => 'nullable|string|min:6',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                $role = Role::firstOrCreate([
                    'name' => $request->role
                ]);

                $userData = [
                    'name'      => $request->name,
                    'username'  => strtolower($request->username),
                    'email'     => $request->email,
                    'role'      => $role->name,
                    'is_active' => $request->boolean('is_active'),
                ];

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $user->update($userData);
                $user->syncRoles([$role]);
            });

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get User Permissions
     */
    public function getPermissions(User $user)
    {
        try {
            $permissions = $user->getAllPermissions()->pluck('name');

            return response()->json([
                'success' => true,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update User Permissions
     */
    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            $user->syncPermissions($request->permissions ?? []);

            return response()->json([
                'success' => true,
                'message' => 'User permissions updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete User
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 403);
        }

        try {
            $user->roles()->detach();
            $user->permissions()->detach();
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
 * Get all available permissions
 */
public function getAllPermissions()
{
    try {
        $permissions = Permission::all()->map(function($permission) {
            return $permission->name; // Just return the name as string
        });

        return response()->json([
            'success' => true,
            'permissions' => $permissions
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to load permissions: ' . $e->getMessage()
        ], 500);
    }
}
}
