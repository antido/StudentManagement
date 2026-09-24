<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('index and create pages render', function () {
    Role::create(['name' => 'Admin Role']);
    Permission::create(['name' => 'view students']);

    $this->get(route('roles.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Roles/Index')
            ->has('roles', 1)
            ->where('roles.0.name', 'Admin Role'));

    $this->get(route('roles.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Roles/Create')
            ->where('permissions.0.name', 'view students'));
});

test('store creates a role', function () {
    $this->post(route('roles.store'), ['name' => 'Teachers Role'])
        ->assertRedirect(route('roles.index'));

    expect(Role::where('name', 'Teachers Role')->exists())->toBeTrue();
});

test('permissions can be assigned to a role', function () {
    $role = Role::create(['name' => 'Admin Role']);
    Permission::create(['name' => 'view students']);
    Permission::create(['name' => 'edit students']);

    $this->post("/roles/assign-permissions-to-role/{$role->id}", ['permissions' => ['view students']])
        ->assertRedirect(route('roles.index'));

    $this->get("/roles/add-permission-to-role/{$role->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Roles/Permissions')
            ->where('role.name', 'Admin Role')
            ->has('role.permissions', 1)
            ->where('role.permissions.0.name', 'view students')
            ->has('allPermissions', 2));

    $this->post("/roles/assign-permissions-to-role/{$role->id}", ['permissions' => ['unknown']])
        ->assertSessionHasErrors('permissions.0');
});

test('users can be assigned to a role and get the mapped user_type', function () {
    $role = Role::create(['name' => 'Teachers Role']);
    $user = User::factory()->create();

    $this->from("/roles/add-users-to-role/{$role->id}")
        ->post("/roles/assign-users-to-role/{$role->id}", ['users' => [$user->id]])
        ->assertRedirect("/roles/add-users-to-role/{$role->id}");

    expect($user->fresh()->hasRole('Teachers Role'))->toBeTrue()
        ->and($user->fresh()->user_type)->toBe('teacher');

    $this->get("/roles/add-users-to-role/{$role->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Roles/Users')
            ->where('role.users.0.id', $user->id)
            ->has('allUsers', 2)
            ->has('allUsers.0.user_type'));
});
