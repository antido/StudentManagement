<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortField = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'desc');

        $classes = Classes::with('teacher:id,first_name,middle_name,last_name')
            ->when($search, function ($query, $search) {
                $query->whereRaw(
                            "CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?",
                            ["%{$search}%"]
                        );
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Classes/Index', [
            'classes' => $classes,
            'search' => $search,
            'sort' => $sortField,
            'direction' => $sortDirection
        ]);
    }

    public function create()
    {
        $teachers = Teacher::select('id', 'first_name', 'middle_name', 'last_name')->get();
        return Inertia::render('Classes/Create', [
            'teachers' => $teachers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $class = new Classes();
        $class->teacher_id = $request->teacher_id;
        $class->name = $request->name;
        $class->description = $request->description;
        $class->save();

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }

    public function edit($id)
    {
        $class = Classes::where('id', $id)->first();
        $teachers = Teacher::select('id', 'first_name', 'middle_name', 'last_name')->get();

        return Inertia::render('Classes/Edit', [
            'classItem' => $class,
            'teachers' => $teachers
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $class = Classes::where('id', $id)->first();
        $class->teacher_id = $request->teacher_id;
        $class->name = $request->name;
        $class->description = $request->description;
        $class->save();

        return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
    }

    public function destroy($id)
    {
        $class = Classes::where('id', $id)->first();
        $class->delete();

        return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
    }

    public function show($id)
    {
        $class = Classes::with('teacher:id,first_name,middle_name,last_name')->findOrFail($id);
        return Inertia::render('Classes/View', [
            'classItem' => $class
        ]);
    }
}