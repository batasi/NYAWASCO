<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions')->withCount('users');

        // Search functionality
        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('guard_name', 'like', "%{$search}%");
            });
        }

        // Sort
        $sort = $request->get('sort', 'name');
        $order = $request->get('order', 'asc');
        $query->orderBy($sort, $order);

        $roles = $query->paginate(20);
        $permissions = Permission::all()->groupBy(function($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'general';
        });

        // Statistics
        $stats = [
            'total' => Role::count(),
            'with_permissions' => Role::whereHas('permissions')->count(),
            'without_permissions' => Role::whereDoesntHave('permissions')->count(),
            'users_with_roles' => \App\Models\User::whereHas('roles')->count(),
        ];

        return view('admin.roles', compact('roles', 'permissions', 'stats'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'required|string|max:255|in:web,api',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $role = Role::create([
                    'name' => $request->name,
                    'guard_name' => $request->guard_name,
                ]);

                if ($request->has('permissions')) {
                    $role->syncPermissions($request->permissions);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id)
            ],
            'guard_name' => 'required|string|max:255|in:web,api',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            DB::transaction(function () use ($request, $role) {
                $role->update([
                    'name' => $request->name,
                    'guard_name' => $request->guard_name,
                ]);

                if ($request->has('permissions')) {
                    $role->syncPermissions($request->permissions);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of admin role
        if ($role->name === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin role'
            ], 403);
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role that has users assigned. Please reassign users first.'
            ], 403);
        }

        try {
            $role->permissions()->detach();
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role permissions
     */
    public function getPermissions(Role $role)
    {
        try {
            $permissions = $role->permissions->pluck('name');

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
     * Get all available permissions
     */
    public function getAllPermissions()
    {
        try {
            $permissions = Permission::all()->pluck('name');

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
