<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Station;
use App\Models\Terminal;
use App\Models\UserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        if ($request->filled('permissions')) {
            $permissions = $request->permissions;
            $query->whereHas('role.permissions', function($q) use ($permissions) {
                $q->whereIn('name', $permissions);
            });
        }

        $users = $query->paginate(20)->appends($request->query());
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'nullable|string|max:255|unique:WEBUSERS,username',
                'email' => 'required|email|unique:WEBUSERS,email',
                'password' => 'required|min:8',
                'role_id' => 'required|exists:roles,id',
            ]);

            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'is_active' => true,
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'User created successfully']);
            }

            return redirect()->route('users.index')->with('success', 'User created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to create user: ' . $e->getMessage()], 500);
            }
            return redirect()->route('users.index')->with('error', 'Failed to create user');
        }
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'nullable|string|max:255|unique:WEBUSERS,username,' . $user->id,
                'email' => 'required|email|unique:WEBUSERS,email,' . $user->id,
                'role_id' => 'required|exists:roles,id',
                'is_active' => 'boolean',
            ]);

            $data = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'role_id' => $request->role_id,
                'is_active' => $request->has('is_active'),
            ];

            if ($request->filled('password')) {
                $request->validate(['password' => 'min:8']);
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'User updated successfully']);
            }

            return redirect()->route('users.index')->with('success', 'User updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to update user: ' . $e->getMessage()], 500);
            }
            return redirect()->route('users.index')->with('error', 'Failed to update user');
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Cannot delete your own account');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:WEBUSERS,id'
        ]);

        $userIds = $request->user_ids;
        $currentUserId = auth()->id();

        // Remove current user from deletion list
        $userIds = array_filter($userIds, function($id) use ($currentUserId) {
            return $id != $currentUserId;
        });

        if (empty($userIds)) {
            return redirect()->route('users.index')->with('error', 'No valid users selected for deletion');
        }

        $deletedCount = User::whereIn('id', $userIds)->delete();
        
        return redirect()->route('users.index')->with('success', "Successfully deleted {$deletedCount} user(s)");
    }

    public function getPermissions(User $user)
    {
        $permissions = $user->role ? $user->role->permissions->pluck('id') : collect();
        return response()->json(['permissions' => $permissions]);
    }

    public function updatePermissions(Request $request, User $user)
    {
        try {
            $permissions = $request->input('permissions', []);
            
            if ($user->role) {
                $user->role->permissions()->sync($permissions);
            }

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Permissions updated successfully']);
            }

            return redirect()->route('users.index')->with('success', 'Permissions updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to update permissions'], 500);
            }
            return redirect()->route('users.index')->with('error', 'Failed to update permissions');
        }
    }

    public function getAccess(User $user)
    {
        $stations = Station::select('ID_STATIONS', 'StationName')->get();
        $terminals = Terminal::with('station:ID_STATIONS,StationName')
            ->select('ID_TERMINALS', 'Description', 'StationsID')
            ->get();
        
        $userAccess = $user->userAccess;
        $access = [
            'station_ids' => $userAccess->station_ids ?? [],
            'terminal_ids' => $userAccess->terminal_ids ?? []
        ];
        
        return response()->json(compact('stations', 'terminals', 'access'));
    }

    public function updateAccess(Request $request, User $user)
    {
        $request->validate([
            'station_ids' => 'nullable|array',
            'terminal_ids' => 'nullable|array'
        ]);

        UserAccess::updateOrCreate(
            ['user_id' => $user->id],
            [
                'station_ids' => $request->station_ids,
                'terminal_ids' => $request->terminal_ids
            ]
        );

        return response()->json(['success' => true, 'message' => 'Access updated successfully']);
    }

    // Role Management Methods
    public function getRoles()
    {
        $roles = Role::all();
        return response()->json(['roles' => $roles]);
    }

    public function storeRole(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
            ]);

            Role::create([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
            ]);

            return response()->json(['success' => true, 'message' => 'Role created successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create role: ' . $e->getMessage()], 500);
        }
    }

    public function updateRole(Request $request, Role $role)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
            ]);

            $role->update([
                'name' => $request->name,
                'display_name' => $request->display_name,
                'description' => $request->description,
            ]);

            return response()->json(['success' => true, 'message' => 'Role updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update role: ' . $e->getMessage()], 500);
        }
    }

    public function destroyRole(Role $role)
    {
        try {
            // Prevent deletion of system roles
            if (in_array($role->name, ['administrator', 'editor', 'viewer'])) {
                return response()->json(['success' => false, 'message' => 'Cannot delete system roles'], 403);
            }

            // Check if role is assigned to any users
            if ($role->users()->count() > 0) {
                return response()->json(['success' => false, 'message' => 'Cannot delete role that is assigned to users'], 403);
            }

            $role->delete();
            return response()->json(['success' => true, 'message' => 'Role deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete role: ' . $e->getMessage()], 500);
        }
    }
}