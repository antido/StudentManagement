<?php

namespace App\Http\Controllers;

use App\Http\Requests\Teacher\IndexTeacherRequest;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Http\Resources\TeacherResource;
use App\Services\TeacherService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function __construct(private TeacherService $teachers) {}

    public function index(IndexTeacherRequest $request)
    {
        $teachers = $this->teachers->paginate(
            $request->search(),
            $request->sortField(),
            $request->sortDirection()
        );

        return Inertia::render('Teachers/Index', [
            'teachers' => $teachers->through(fn ($teacher) => new TeacherResource($teacher)),
            'search' => $request->search(),
            'sort' => $request->sortField(),
            'direction' => $request->sortDirection(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Teachers/Create');
    }

    public function store(StoreTeacherRequest $request)
    {
        try {
            $this->teachers->create(
                $request->safe()->except('image'),
                $request->file('image')
            );

            return redirect()->route('teachers.index')->with('success', 'Teacher and user created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating teacher', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->withErrors([
                'error' => 'Something went wrong: '.$e->getMessage(),
            ]);
        }
    }

    public function edit(string $id)
    {
        return Inertia::render('Teachers/Edit', [
            'teacher' => new TeacherResource($this->teachers->find($id)),
        ]);
    }

    public function update(UpdateTeacherRequest $request, string $id)
    {
        $teacher = $this->teachers->find($id);

        try {
            $this->teachers->update(
                $teacher,
                $request->safe()->except('image'),
                $request->file('image')
            );

            return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating teacher', [
                'message' => $e->getMessage(),
                'teacher_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->withErrors([
                'error' => 'Something went wrong: '.$e->getMessage(),
            ]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->teachers->delete($this->teachers->find($id));

            return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete teacher', [
                'teacher_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('teachers.index')->with('error', 'Failed to delete teacher. Please try again.');
        }
    }

    public function show(string $id)
    {
        return Inertia::render('Teachers/View', [
            'teacher' => new TeacherResource($this->teachers->find($id, ['user', 'classes'])),
        ]);
    }
}
