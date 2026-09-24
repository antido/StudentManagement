<?php

use App\Mail\StudentReportMail;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

function makeStudent(array $attributes = []): Student
{
    $user = User::factory()->create();

    return Student::create(array_merge([
        'user_id' => $user->id,
        'first_name' => 'Juan',
        'middle_name' => 'Santos',
        'last_name' => 'Cruz',
        'email' => $user->email,
        'birthday' => '2005-06-15',
        'age' => 20,
        'gender' => 'm',
        'score' => 90,
    ], $attributes));
}

test('index lists paginated students with the paginator shape', function () {
    makeStudent();
    makeStudent(['first_name' => 'Maria', 'gender' => 'f']);

    $this->get(route('students.index', ['search' => 'Maria', 'sort' => 'score', 'direction' => 'asc']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Students/Index')
            ->has('students.data', 1)
            ->where('students.data.0.first_name', 'Maria')
            ->has('students.data.0.user.name')
            ->has('students.links')
            ->where('students.current_page', 1)
            ->where('students.per_page', 10)
            ->where('search', 'Maria')
            ->where('sort', 'score')
            ->where('direction', 'asc'));
});

test('index rejects unknown sort columns', function () {
    $this->get(route('students.index', ['sort' => 'password']))
        ->assertSessionHasErrors('sort');
});

test('store creates a student and its user account', function () {
    Storage::fake('public');

    $this->post(route('students.store'), [
        'first_name' => 'Ana',
        'middle_name' => 'Reyes',
        'last_name' => 'Lopez',
        'email' => 'ana@example.com',
        'age' => 18,
        'birthday' => '2007-01-02',
        'gender' => 'f',
        'score' => 88,
        'image' => UploadedFile::fake()->image('ana.jpg'),
    ])->assertRedirect(route('students.index'));

    $student = Student::where('email', 'ana@example.com')->firstOrFail();

    expect($student->user->name)->toBe('Ana Reyes Lopez');
    Storage::disk('public')->assertExists($student->image);
});

test('store validates input', function () {
    $this->post(route('students.store'), [])
        ->assertSessionHasErrors(['first_name', 'middle_name', 'last_name', 'email', 'age', 'gender', 'score']);
});

test('show and edit pass the student as an unwrapped resource', function () {
    $student = makeStudent();

    $this->get(route('students.show', $student->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Students/View')
            ->where('student.id', $student->id)
            ->where('student.birthday', '2005-06-15')
            ->where('student.image_url', null)
            ->where('student.user.email', $student->email));

    $this->get(route('students.edit', $student->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Students/Edit')
            ->where('student.first_name', 'Juan')
            ->missing('student.user'));
});

test('update keeps its own email and syncs the user account', function () {
    $student = makeStudent();

    $this->put(route('students.update', $student->id), [
        'id' => $student->id,
        'first_name' => 'Pedro',
        'middle_name' => null,
        'last_name' => 'Cruz',
        'email' => $student->email,
        'age' => 21,
        'gender' => 'm',
        'score' => 95,
    ])->assertSessionHasNoErrors()->assertRedirect(route('students.index'));

    $student->refresh();

    expect($student->first_name)->toBe('Pedro')
        ->and($student->user->name)->toBe('Pedro Cruz');
});

test('destroy deletes the student', function () {
    $student = makeStudent();

    $this->delete(route('students.destroy', $student->id))
        ->assertRedirect(route('students.index'));

    expect(Student::find($student->id))->toBeNull();
});

test('import validates the uploaded file', function () {
    $this->post(route('students.import'), [])
        ->assertSessionHasErrors('file');
});

test('email report sends the report mail', function () {
    Mail::fake();
    $student = makeStudent();

    $this->from(route('students.show', $student->id))
        ->get(route('students.email.report', $student->id))
        ->assertRedirect(route('students.show', $student->id));

    Mail::assertSent(StudentReportMail::class, fn ($mail) => $mail->hasTo($student->email));
});
