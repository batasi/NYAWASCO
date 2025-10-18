<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.users.index', compact('totalUsers', 'activeUsers', 'recentUsers'));
    }

    public function getUsers(Request $request)
    {
        $users = User::with(['roles', 'permissions'])
            ->withCount(['events', 'tickets'])
            ->latest()
            ->get();

        return response()->json([
            'users' => $users,
            'success' => true
        ]);
    }

    public function show(User $user)
    {
        $user->load(['roles', 'permissions']);
        $user->loadCount(['events', 'tickets']);

        return response()->json([
            'user' => $user,
            'success' => true
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,organizer,vendor,attendee',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'bio' => $request->bio,
            'is_active' => $request->boolean('is_active', true),
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $userData['avatar'] = $avatarPath;
        }

        $user = User::create($userData);

        // Assign role
        $user->syncRoles([$request->role]);

        // Assign direct permissions if provided
        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->load(['roles', 'permissions']),
            'success' => true
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,organizer,vendor,attendee',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'bio' => $request->bio,
            'is_active' => $request->boolean('is_active'),
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $userData['avatar'] = $avatarPath;
        }

        $user->update($userData);

        // Sync role
        $user->syncRoles([$request->role]);

        // Sync direct permissions
        $permissions = $request->has('permissions') ? $request->permissions : [];
        $user->syncPermissions($permissions);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->fresh(['roles', 'permissions']),
            'success' => true
        ]);
    }

    public function destroy(User $user)
    {
        // Prevent users from deleting themselves
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'You cannot delete your own account',
                'success' => false
            ], 422);
        }

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
            'success' => true
        ]);
    }

    public function getPermissions(User $user)
    {
        $allPermissions = Permission::all();
        $userPermissions = $user->getDirectPermissions()->pluck('name')->toArray();
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();

        return response()->json([
            'allPermissions' => $allPermissions,
            'userPermissions' => $userPermissions,
            'rolePermissions' => $rolePermissions,
            'success' => true
        ]);
    }

    public function toggleStatus(User $user)
    {
        // Prevent users from deactivating themselves
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'You cannot deactivate your own account',
                'success' => false
            ], 422);
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        return response()->json([
            'message' => 'User status updated successfully',
            'user' => $user,
            'success' => true
        ]);
    }
}
