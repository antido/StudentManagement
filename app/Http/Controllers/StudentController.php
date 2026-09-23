<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        $search = $request->input('search');
        $sortField = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'desc');

        $students = Student::with('user:id,name')
                            ->when($search, function($query, $search){
                               $query->whereRaw(
                                            "CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?",
                                            ["%{$search}%"]
                                        )
                                    ->orWhere('email', 'like', "%{$search}%");
                            })
                            ->orderBy($sortField, $sortDirection)
                            ->paginate(10)
                            ->withQueryString();

        return inertia('Students/Index', [
            'students' => $students,
            'search' => $search,
            'sort' => $sortField,
            'direction' => $sortDirection
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Students/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email|unique:users,email',
            'age' => 'required|integer|min:1|max:150',
            'birthday' => 'nullable|date',
            'gender' => 'required|in:m,f',
            'score' => 'required|integer|min:0|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make('password'),
            ]);

            $studentData = [
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'age' => $request->age,
                'birthday' => $request->birthday,
                'gender' => $request->gender,
                'score' => $request->score,
                'user_id' => $user->id,
            ];

            if ($request->hasFile('image')) {
                $studentData['image'] = $request->file('image')
                    ->store('students', 'public');
            }

            Student::create($studentData);

            DB::commit();

            return redirect()
                ->route('students.index')
                ->with('success', 'Student and user created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error', [
                'Message' => $e->getMessage(),
                'Traces' => $e->getTrace(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Something went wrong: ' . $e->getMessage(),
                ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::with('user')->findOrFail($id);
        $student->image_url = $student->image ? asset('storage/' . $student->image) : null;

        return inertia('Students/View', [
            'student' => $student
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::findOrFail($id);

        return inertia('Students/Edit', [
            'student' => $student
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'id' => 'required|exists:students,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $request->id,
            'age' => 'required|integer|min:1|max:150',
            'birthday' => 'nullable|date',
            'gender' => 'required|in:m,f',
            'score' => 'required|integer|min:0|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $validated) {

                $student = Student::findOrFail($validated['id']);

                $studentData = [
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'age' => $validated['age'],
                    'birthday' => $validated['birthday'] ?? null,
                    'gender' => $validated['gender'],
                    'score' => $validated['score'],
                ];

                if ($request->hasFile('image')) {

                    // Delete old image
                    if (
                        $student->image &&
                        Storage::disk('public')->exists($student->image)
                    ) {
                        Storage::disk('public')->delete($student->image);
                    }

                    // Store new image
                    $studentData['image'] = $request->file('image')
                        ->store('students', 'public');
                }

                // Update student
                $student->update($studentData);

                // Update related user
                if ($student->user) {
                    $student->user->update([
                        'name' => trim(
                            $validated['first_name'] . ' ' .
                            ($validated['middle_name'] ?? '') . ' ' .
                            $validated['last_name']
                        ),
                        'email' => $validated['email'],
                    ]);
                }
            });

            return redirect()
                ->route('students.index')
                ->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {

            Log::error('Error updating student', [
                'message' => $e->getMessage(),
                'student_id' => $request->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Something went wrong: ' . $e->getMessage(),
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $student = Student::where('id', $id)->first();

            if (!$student) {
                return redirect()
                    ->route('students.index')
                    ->with('error', 'Student not found.');
            }

            if ($student->image && Storage::disk('public')->exists($student->image)) {
                Storage::disk('public')->delete($student->image);
            }

            $student->delete();

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
}
