<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return response()->json([
            'roles' => $roles,
            'permissions' => $permissions,
            'success' => true
        ]);
    }

    public function show(Role $role)
    {
        return response()->json([
            'role' => $role->load('permissions'),
            'success' => true
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role->load('permissions'),
            'success' => true
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);

        $permissions = $request->has('permissions') ? $request->permissions : [];
        $role->syncPermissions($permissions);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role->fresh('permissions'),
            'success' => true
        ]);
    }

    public function getPermissions(Role $role)
    {
        $allPermissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return response()->json([
            'allPermissions' => $allPermissions,
            'rolePermissions' => $rolePermissions,
            'success' => true
        ]);
    }
}
