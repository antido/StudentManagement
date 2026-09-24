<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionService
{
    public function roles(): Collection
    {
        return Role::all();
    }

    public function permissions(): Collection
    {
        return Permission::all();
    }

    public function assignableUsers(): Collection
    {
        return User::select('id', 'name', 'email', 'user_type')->get();
    }

    public function find(int|string $id, array $relations = []): Role
    {
        return Role::with($relations)->findOrFail($id);
    }

    public function create(string $name): Role
    {
        return Role::create(['name' => $name]);
    }

    /**
     * Replace the role's current permissions with the given ones.
     */
    public function syncPermissions(Role $role, array $permissions): void
    {
        $role->syncPermissions($permissions);
    }

    /**
     * Replace the role's members with the given users and update their user_type.
     */
    public function syncUsers(Role $role, array $userIds): void
    {
        $userType = config('roles.user_type_map')[$role->name] ?? null;

        DB::transaction(function () use ($role, $userIds, $userType) {
            // Remove role from users who currently have it
            $currentUsers = User::role($role->name)->get();
            $currentUsers->each(fn ($user) => $user->removeRole($role));
            User::whereIn('id', $currentUsers->pluck('id'))->update(['user_type' => null]);

            // Assign role to the selected users and set their user_type
            $newUsers = User::whereIn('id', $userIds)->get();
            $newUsers->each(fn ($user) => $user->assignRole($role));
            User::whereIn('id', $userIds)->update(['user_type' => $userType]);
        });
    }
}
