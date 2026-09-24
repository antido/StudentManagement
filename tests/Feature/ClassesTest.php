<?php

use App\Models\Classes;
use App\Models\Teacher;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    $user = User::factory()->create();
    $this->teacher = Teacher::create([
        'user_id' => $user->id,
        'first_name' => 'Emilio',
        'middle_name' => 'Famy',
        'last_name' => 'Aguinaldo',
        'email' => $user->email,
        'phone' => '0917',
    ]);
});

test('index lists classes and searches by class or teacher name', function () {
    Classes::create(['teacher_id' => $this->teacher->id, 'name' => 'Science', 'description' => 'Bio']);
    Classes::create(['teacher_id' => $this->teacher->id, 'name' => 'History', 'description' => 'PH']);

    $this->get(route('classes.index', ['search' => 'Hist']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Classes/Index')
            ->has('classes.data', 1)
            ->where('classes.data.0.name', 'History')
            ->where('classes.data.0.teacher.first_name', 'Emilio')
            ->has('classes.links'));

    $this->get(route('classes.index', ['search' => 'Emilio']))
        ->assertInertia(fn (Assert $page) => $page->has('classes.data', 2));
});

test('create and edit pass the teacher list', function () {
    $class = Classes::create(['teacher_id' => $this->teacher->id, 'name' => 'Science', 'description' => 'Bio']);

    $this->get(route('classes.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Classes/Create')
            ->has('teachers', 1)
            ->where('teachers.0.last_name', 'Aguinaldo'));

    $this->get(route('classes.edit', $class->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Classes/Edit')
            ->where('classItem.name', 'Science')
            ->where('classItem.teacher_id', $this->teacher->id)
            ->has('teachers', 1));
});

test('store, update, show and destroy work', function () {
    $this->post(route('classes.store'), [
        'teacher_id' => $this->teacher->id,
        'name' => 'English',
        'description' => 'Grammar',
    ])->assertRedirect(route('classes.index'));

    $class = Classes::where('name', 'English')->firstOrFail();

    $this->put(route('classes.update', $class->id), [
        'teacher_id' => $this->teacher->id,
        'name' => 'English II',
        'description' => 'Literature',
    ])->assertRedirect(route('classes.index'));

    $this->get(route('classes.show', $class->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Classes/View')
            ->where('classItem.name', 'English II')
            ->where('classItem.teacher.first_name', 'Emilio'));

    $this->delete(route('classes.destroy', $class->id))
        ->assertRedirect(route('classes.index'));

    expect(Classes::find($class->id))->toBeNull();
});

test('store validates input', function () {
    $this->post(route('classes.store'), ['teacher_id' => 999])
        ->assertSessionHasErrors(['teacher_id', 'name']);
});
