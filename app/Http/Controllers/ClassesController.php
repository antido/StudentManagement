<?php

namespace App\Http\Controllers;

use App\Http\Requests\Classes\IndexClassesRequest;
use App\Http\Requests\Classes\StoreClassesRequest;
use App\Http\Requests\Classes\UpdateClassesRequest;
use App\Http\Resources\ClassesResource;
use App\Http\Resources\TeacherResource;
use App\Services\ClassesService;
use App\Services\TeacherService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ClassesController extends Controller
{
    public function __construct(
        private ClassesService $classes,
        private TeacherService $teachers,
    ) {}

    public function index(IndexClassesRequest $request)
    {
        $classes = $this->classes->paginate(
            $request->search(),
            $request->sortField(),
            $request->sortDirection()
        );

        return Inertia::render('Classes/Index', [
            'classes' => $classes->through(fn ($class) => new ClassesResource($class)),
            'search' => $request->search(),
            'sort' => $request->sortField(),
            'direction' => $request->sortDirection(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Classes/Create', [
            'teachers' => TeacherResource::collection($this->teachers->options()),
        ]);
    }

    public function store(StoreClassesRequest $request)
    {
        try {
            $this->classes->create($request->validated());

            return redirect()->route('classes.index')->with('success', 'Class created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating class', [
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
        return Inertia::render('Classes/Edit', [
            'classItem' => new ClassesResource($this->classes->find($id)),
            'teachers' => TeacherResource::collection($this->teachers->options()),
        ]);
    }

    public function update(UpdateClassesRequest $request, string $id)
    {
        $class = $this->classes->find($id);

        try {
            $this->classes->update($class, $request->validated());

            return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating class', [
                'message' => $e->getMessage(),
                'class_id' => $id,
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
            $this->classes->delete($this->classes->find($id));

            return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete class', [
                'class_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('classes.index')->with('error', 'Failed to delete class. Please try again.');
        }
    }

    public function show(string $id)
    {
        $class = $this->classes->find($id, ['teacher:id,first_name,middle_name,last_name']);

        return Inertia::render('Classes/View', [
            'classItem' => new ClassesResource($class),
        ]);
    }
}
