<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index()
    {
        return inertia('Roles/Index', [
            'roles' => Role::all(),
        ]);
    }

    public function create()
    {
        return inertia('Roles/Create', [
            'permissions' => Permission::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
        ]);

        $role = Role::create(['name' => $request->name]);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function addPermissionToRole($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return inertia('Roles/Permissions', [
            'role' => $role,
            'allPermissions' => Permission::all(),
        ]);
    }

    public function assignPermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->syncPermissions($request->permissions); // replaces current permissions

        return redirect()->route('roles.index')->with('success', 'Permissions Added Successfully');
    }

    public function addUsersToRole($id)
    {
        return inertia('Roles/Users', [
            'role' => Role::with('users')->findOrFail($id),
            'allUsers' => User::select('id', 'name', 'email','user_type')->get(),
        ]);
    }

    public function assignUsersToRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $userIds = $request->input('users', []);

        $userType = config('roles.user_type_map')[$role->name] ?? null;

        DB::transaction(function () use ($role, $userIds, $userType) {
            // Remove role from users who currently have it but aren't in the new list
            $currentUsers = User::role($role->name)->get();
            $currentUsers->each(fn ($user) => $user->removeRole($role));
            User::whereIn('id', $currentUsers->pluck('id'))->update(['user_type' => null]);

            // Assign role to the selected users and set their user_type
            $newUsers = User::whereIn('id', $userIds)->get();
            $newUsers->each(fn ($user) => $user->assignRole($role));
            User::whereIn('id', $userIds)->update(['user_type' => $userType]);
        });

        return redirect()->back()->with('success', 'Users assigned to role successfully!');
    }
}
