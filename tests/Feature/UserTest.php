<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['name' => 'Admin']));
});

test('index lists users without exposing passwords', function () {
    $this->get(route('users.index', ['sort' => 'name', 'direction' => 'asc']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Users/Index')
            ->has('users.data', 1)
            ->where('users.data.0.name', 'Admin')
            ->missing('users.data.0.password')
            ->has('users.links')
            ->where('users.current_page', 1));
});

test('store, edit, update, show and destroy work', function () {
    $this->post(route('users.store'), [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'secret123',
    ])->assertRedirect(route('users.index'));

    $user = User::where('email', 'new@example.com')->firstOrFail();
    expect(Hash::check('secret123', $user->password))->toBeTrue();

    $this->get(route('users.edit', $user->id))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Users/Edit')
            ->where('user.email', 'new@example.com'));

    $this->put(route('users.update', $user->id), [
        'id' => $user->id,
        'name' => 'Renamed',
        'email' => 'new@example.com',
        'password' => '',
    ])->assertSessionHasNoErrors()->assertRedirect(route('users.index'));

    $user->refresh();
    expect($user->name)->toBe('Renamed')
        ->and(Hash::check('secret123', $user->password))->toBeTrue();

    $this->get(route('users.show', $user->id))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Users/View')
            ->where('user.name', 'Renamed'));

    $this->delete(route('users.destroy', $user->id))
        ->assertRedirect(route('users.index'));

    expect(User::find($user->id))->toBeNull();
});

test('store rejects duplicate emails', function () {
    $this->post(route('users.store'), [
        'name' => 'Dup',
        'email' => auth()->user()->email,
        'password' => 'secret123',
    ])->assertSessionHasErrors('email');
});
