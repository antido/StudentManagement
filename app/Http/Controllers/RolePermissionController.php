<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\AssignPermissionsRequest;
use App\Http\Requests\Role\AssignUsersRequest;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Services\RolePermissionService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class RolePermissionController extends Controller
{
    public function __construct(private RolePermissionService $roles) {}

    public function index()
    {
        return Inertia::render('Roles/Index', [
            'roles' => RoleResource::collection($this->roles->roles()),
        ]);
    }

    public function create()
    {
        return Inertia::render('Roles/Create', [
            'permissions' => PermissionResource::collection($this->roles->permissions()),
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        try {
            $this->roles->create($request->validated('name'));

            return redirect()->route('roles.index')->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating role', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->withErrors([
                'error' => 'Something went wrong: '.$e->getMessage(),
            ]);
        }
    }

    public function addPermissionToRole(string $id)
    {
        return Inertia::render('Roles/Permissions', [
            'role' => new RoleResource($this->roles->find($id, ['permissions'])),
            'allPermissions' => PermissionResource::collection($this->roles->permissions()),
        ]);
    }

    public function assignPermissions(AssignPermissionsRequest $request, string $id)
    {
        $role = $this->roles->find($id);

        try {
            $this->roles->syncPermissions($role, $request->validated('permissions') ?? []);

            return redirect()->route('roles.index')->with('success', 'Permissions Added Successfully');
        } catch (\Exception $e) {
            Log::error('Error assigning permissions to role', [
                'message' => $e->getMessage(),
                'role_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->withErrors([
                'error' => 'Something went wrong: '.$e->getMessage(),
            ]);
        }
    }

    public function addUsersToRole(string $id)
    {
        return Inertia::render('Roles/Users', [
            'role' => new RoleResource($this->roles->find($id, ['users'])),
            'allUsers' => UserResource::collection($this->roles->assignableUsers()),
        ]);
    }

    public function assignUsersToRole(AssignUsersRequest $request, string $id)
    {
        $role = $this->roles->find($id);

        try {
            $this->roles->syncUsers($role, $request->validated('users') ?? []);

            return redirect()->back()->with('success', 'Users assigned to role successfully!');
        } catch (\Exception $e) {
            Log::error('Error assigning users to role', [
                'message' => $e->getMessage(),
                'role_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->withErrors([
                'error' => 'Something went wrong: '.$e->getMessage(),
            ]);
        }
    }
}
