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
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Admin Dashboard
     */
    /**
     * Admin Dashboard
     */
    public function index()
    {
        // Total counts
        $total_users = User::count();
        $total_attendees = User::role('attendee')->count();
        $total_vendors = User::role('vendor')->count();
        $total_organizers = User::role('organizer')->count();

        $total_events = \App\Models\Event::count();
        $total_voting_contests = \App\Models\VotingContest::count();
        $total_ticket_sales = \App\Models\TicketPurchase::where('status', 'paid')->count();

        $pending_approvals = \App\Models\Event::where('status', 'pending_approval')->count() +
            \App\Models\VotingContest::where('requires_approval', true)
            ->where('is_active', false)
            ->count();

        // Recent users (latest 5, paginated)
        $recent_users = User::latest()->paginate(10);

        return view('dashboard.admin', compact(
            'total_users',
            'total_attendees',
            'total_vendors',
            'total_organizers',
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

        // Apply filters
        if ($request->has('role') && $request->role) {
            $users->where('role', $request->role);
        }

        if ($request->has('status') && $request->status !== '') {
            $users->where('is_active', $request->status);
        }

        return DataTables::of($users)
            ->addColumn('role_badge', function ($user) {
                $roleColors = [
                    'admin' => 'red',
                    'organizer' => 'blue',
                    'vendor' => 'green',
                    'attendee' => 'gray'
                ];

                $color = $roleColors[$user->role] ?? 'gray';
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-' . $color . '-100 text-' . $color . '-800">' . ucfirst($user->role) . '</span>';
            })
            ->addColumn('status', function ($user) {
                $status = $user->is_active ? 'Active' : 'Inactive';
                $color = $user->is_active ? 'green' : 'red';
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-' . $color . '-100 text-' . $color . '-800">' . $status . '</span>';
            })
            ->addColumn('verification', function ($user) {
                if ($user->is_verified) {
                    return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Verified</span>';
                }
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Unverified</span>';
            })
            ->addColumn('stats', function ($user) {
                $stats = [];
                if ($user->isOrganizer()) {
                    $stats[] = 'Events: ' . $user->total_events;
                    $stats[] = 'Revenue: KES ' . number_format($user->total_revenue, 2);
                }
                if ($user->isAttendee()) {
                    $stats[] = 'Tickets: ' . $user->ticketPurchases()->count();
                    $stats[] = 'Spent: KES ' . number_format($user->total_amount_spent, 2);
                }
                if ($user->total_votes_cast > 0) {
                    $stats[] = 'Votes: ' . $user->total_votes_cast;
                }

                return implode(' | ', $stats) ?: 'No activity';
            })
            ->addColumn('actions', function ($user) {
                $actions = '
                <div class="flex space-x-1">
                    <button class="view-user-btn inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            data-user-id="' . $user->id . '"
                            data-user-name="' . $user->name . '">
                        View
                    </button>
                    <button class="edit-user-btn inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            data-user-id="' . $user->id . '"
                            data-user-name="' . $user->name . '"
                            data-user-email="' . $user->email . '"
                            data-user-role="' . $user->role . '">
                        Edit
                    </button>';

                // Only show permissions button for non-attendee roles
                if (!$user->isAttendee()) {
                    $actions .= '
                    <button class="roles-permissions-btn inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                            data-user-id="' . $user->id . '"
                            data-user-name="' . $user->name . '"
                            data-user-role="' . $user->role . '">
                        Permissions
                    </button>';
                }

                $actions .= '
                    <button class="delete-user-btn inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            data-user-id="' . $user->id . '"
                            data-user-name="' . $user->name . '">
                        Delete
                    </button>
                </div>';

                return $actions;
            })
            ->addColumn('created_at_formatted', function ($user) {
                return $user->created_at->format('M j, Y g:i A');
            })
            ->rawColumns(['role_badge', 'status', 'verification', 'actions'])
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
            'role' => 'required|in:admin,organizer,vendor,attendee',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_verified' => 'boolean'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $userData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                    'phone' => $request->phone,
                    'is_active' => $request->boolean('is_active', true),
                    'is_verified' => $request->boolean('is_verified', false),
                ];

                // Add role-specific fields
                if ($request->role === 'organizer') {
                    $userData['company_name'] = $request->company_name;
                    $userData['website'] = $request->website;
                } elseif ($request->role === 'vendor') {
                    $userData['business_name'] = $request->business_name;
                    $userData['contact_number'] = $request->contact_number;
                } elseif ($request->role === 'attendee') {
                    $userData['occupation'] = $request->occupation;
                    $userData['institution'] = $request->institution;
                }

                $user = User::create($userData);

                // Assign role using Spatie permissions
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
        $user->load('roles', 'permissions', 'ticketPurchases.event', 'events', 'votingContests');

        // Load relevant data based on user role
        if ($user->isOrganizer()) {
            $user->load(['events' => function ($query) {
                $query->latest()->take(5);
            }, 'votingContests' => function ($query) {
                $query->latest()->take(5);
            }]);
        } elseif ($user->isAttendee()) {
            $user->load(['ticketPurchases' => function ($query) {
                $query->with('event')->latest()->take(10);
            }, 'votes' => function ($query) {
                $query->with('votingContest', 'nominee')->latest()->take(10);
            }]);
        }

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
            'role' => 'required|in:admin,organizer,vendor,attendee',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'can_vote' => 'boolean'
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                $userData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'role' => $request->role,
                    'phone' => $request->phone,
                    'is_active' => $request->boolean('is_active'),
                    'is_verified' => $request->boolean('is_verified'),
                    'can_vote' => $request->boolean('can_vote', true),
                ];

                // Update password if provided
                if ($request->filled('password')) {
                    $request->validate([
                        'password' => 'string|min:8|confirmed'
                    ]);
                    $userData['password'] = Hash::make($request->password);
                }

                // Update role-specific fields
                if ($request->role === 'organizer') {
                    $userData['company_name'] = $request->company_name;
                    $userData['website'] = $request->website;
                    $userData['about'] = $request->about;
                    $userData['address'] = $request->address;
                } elseif ($request->role === 'vendor') {
                    $userData['business_name'] = $request->business_name;
                    $userData['contact_number'] = $request->contact_number;
                    $userData['services_offered'] = $request->services_offered;
                } elseif ($request->role === 'attendee') {
                    $userData['occupation'] = $request->occupation;
                    $userData['institution'] = $request->institution;
                    $userData['membership_number'] = $request->membership_number;
                    $userData['attendee_type'] = $request->attendee_type;
                }

                $user->update($userData);

                // Sync Spatie role
                $user->syncRoles([$request->role]);
            });

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user->fresh(['roles'])
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

        // Prevent deletion of users with related data
        if (
            $user->events()->exists() || $user->votingContests()->exists() ||
            $user->ticketPurchases()->exists() || $user->votes()->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user with associated events, contests, tickets, or votes. Consider deactivating instead.'
            ], 422);
        }

        try {
            // Remove roles and permissions first
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
                $user->update(['role' => $request->role]);
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
            } elseif (str_contains($permission->name, 'ticket')) {
                return 'tickets';
            } elseif (str_contains($permission->name, 'organizer')) {
                return 'organizer';
            } elseif (str_contains($permission->name, 'vendor')) {
                return 'vendor';
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

    /**
     * Toggle Verification Status
     */
    public function toggleVerification(User $user)
    {
        try {
            $user->update([
                'is_verified' => !$user->is_verified
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User verification status updated successfully',
                'is_verified' => $user->is_verified
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update verification status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get User Statistics
     */
    public function getUserStats(User $user)
    {
        $stats = [
            'profile_completion' => $user->profile_completion,
            'total_events' => $user->total_events,
            'total_voting_contests' => $user->total_voting_contests,
            'total_ticket_purchases' => $user->ticketPurchases()->count(),
            'total_votes_cast' => $user->total_votes_cast,
            'total_revenue' => $user->total_revenue,
            'total_amount_spent' => $user->total_amount_spent,
            'joined_date' => $user->created_at->format('M j, Y'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
