<?php

use App\Models\Classes;
use App\Models\Teacher;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

function makeTeacher(array $attributes = []): Teacher
{
    $user = User::factory()->create();

    return Teacher::create(array_merge([
        'user_id' => $user->id,
        'first_name' => 'Jose',
        'middle_name' => 'Protacio',
        'last_name' => 'Rizal',
        'email' => $user->email,
        'phone' => '09171234567',
    ], $attributes));
}

test('index lists teachers and can sort by name', function () {
    makeTeacher();

    $this->get(route('teachers.index', ['sort' => 'name', 'direction' => 'asc']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Teachers/Index')
            ->has('teachers.data', 1)
            ->where('teachers.data.0.phone', '09171234567')
            ->has('teachers.links')
            ->where('teachers.current_page', 1));
});

test('store creates a teacher and its user account', function () {
    $this->post(route('teachers.store'), [
        'first_name' => 'Andres',
        'middle_name' => 'de Castro',
        'last_name' => 'Bonifacio',
        'email' => 'andres@example.com',
        'phone' => '09181234567',
    ])->assertRedirect(route('teachers.index'));

    $teacher = Teacher::where('email', 'andres@example.com')->firstOrFail();

    expect($teacher->user->email)->toBe('andres@example.com');
});

test('update keeps its own email and user link', function () {
    $teacher = makeTeacher();
    $userId = $teacher->user_id;

    $this->put(route('teachers.update', $teacher->id), [
        'id' => $teacher->id,
        'first_name' => 'Pepe',
        'middle_name' => 'Protacio',
        'last_name' => 'Rizal',
        'email' => $teacher->email,
        'phone' => '0999',
    ])->assertSessionHasNoErrors()->assertRedirect(route('teachers.index'));

    $teacher->refresh();

    expect($teacher->first_name)->toBe('Pepe')
        ->and($teacher->user_id)->toBe($userId);
});

test('show includes user and classes', function () {
    $teacher = makeTeacher();
    Classes::create(['teacher_id' => $teacher->id, 'name' => 'Math', 'description' => 'Algebra']);

    $this->get(route('teachers.show', $teacher->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Teachers/View')
            ->where('teacher.id', $teacher->id)
            ->where('teacher.user.email', $teacher->email)
            ->has('teacher.classes', 1)
            ->where('teacher.classes.0.name', 'Math'));
});

test('destroy failures are caught and flashed instead of erroring', function () {
    $teacher = makeTeacher();
    Classes::create(['teacher_id' => $teacher->id, 'name' => 'Math', 'description' => 'Algebra']);

    $this->delete(route('teachers.destroy', $teacher->id))
        ->assertRedirect(route('teachers.index'))
        ->assertSessionHas('error', 'Failed to delete teacher. Please try again.');

    expect(Teacher::find($teacher->id))->not->toBeNull();
});

test('edit and destroy work', function () {
    $teacher = makeTeacher();

    $this->get(route('teachers.edit', $teacher->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Teachers/Edit')
            ->where('teacher.email', $teacher->email));

    $this->delete(route('teachers.destroy', $teacher->id))
        ->assertRedirect(route('teachers.index'));

    expect(Teacher::find($teacher->id))->toBeNull();
});
