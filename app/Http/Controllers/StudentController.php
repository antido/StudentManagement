<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\ImportStudentsRequest;
use App\Http\Requests\Student\IndexStudentRequest;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Services\StudentService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function __construct(private StudentService $students) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexStudentRequest $request)
    {
        $students = $this->students->paginate(
            $request->search(),
            $request->sortField(),
            $request->sortDirection()
        );

        return Inertia::render('Students/Index', [
            'students' => $students->through(fn ($student) => new StudentResource($student)),
            'search' => $request->search(),
            'sort' => $request->sortField(),
            'direction' => $request->sortDirection(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Students/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        try {
            $this->students->create(
                $request->safe()->except('image'),
                $request->file('image')
            );

            return redirect()
                ->route('students.index')
                ->with('success', 'Student and user created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating student', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Something went wrong: '.$e->getMessage(),
                ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Inertia::render('Students/View', [
            'student' => new StudentResource($this->students->find($id, ['user'])),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Inertia::render('Students/Edit', [
            'student' => new StudentResource($this->students->find($id)),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, string $id)
    {
        $student = $this->students->find($id);

        try {
            $this->students->update(
                $student,
                $request->safe()->except('image'),
                $request->file('image')
            );

            return redirect()
                ->route('students.index')
                ->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating student', [
                'message' => $e->getMessage(),
                'student_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Something went wrong: '.$e->getMessage(),
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->students->delete($this->students->find($id));

            return redirect()
                ->route('students.index')
                ->with('success', 'Student deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete student', [
                'student_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('students.index')
                ->with('error', 'Failed to delete student. Please try again.');
        }
    }

    /**
     * Export table data
     */
    public function export()
    {
        return $this->students->export();
    }

    /**
     * Import data to database
     */
    public function import(ImportStudentsRequest $request)
    {
        $this->students->import($request->file('file'));

        return redirect()->back()->with('success', 'Students imported successfully.');
    }

    public function studentReport(string $id)
    {
        $student = $this->students->find($id);

        return $this->students->reportPdf($student)->stream("student_report_{$student->id}.pdf");
    }

    public function emailReport(string $id)
    {
        $this->students->emailReport($this->students->find($id));

        return back()->with('success', 'Report sent to student email!');
    }
}
