<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class TeacherController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortField = $request->input('sort', 'id');       // ✅ New: Get sort field (default: id)
        $sortDirection = $request->input('direction', 'desc');

        $teachers = Teacher::when($search, function ($query, $search) {
                                $query->whereRaw(
                                        "CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?",
                                        ["%{$search}%"]
                                    )
                                    ->orWhere('email', 'like', "%{$search}%");
                            })
                            ->orderBy($sortField, $sortDirection) // ✅ New: Apply sorting
                            ->paginate(5)
                            ->withQueryString();

        return Inertia::render('Teachers/Index', [
            'teachers' => $teachers,
            'search' => $search,
            'sort' => $sortField,         // ✅ New
            'direction' => $sortDirection // ✅ New
        ]);
    }


    public function create()
    {
        return Inertia::render('Teachers/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email|unique:users,email',
            'phone' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make('password'),
            ]);

            $teacherData = [
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'user_id' => $user->id,
            ];

            if ($request->hasFile('image')) {
                $teacherData['image'] = $request->file('image')
                    ->store('teachers', 'public');
            }

            Teacher::create($teacherData);

            DB::commit();

            return redirect()->route('teachers.index')->with('success', 'Teacher and user created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors([
                'error' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }


    public function edit($id)
    {
        $teacher = Teacher::where('id', $id)->first();
        return Inertia::render('Teachers/Edit', [
            'teacher' => $teacher
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $teacher = Teacher::where('id', $request->id)->first();
        $teacher->first_name = $request->first_name;
        $teacher->middle_name = $request->middle_name;
        $teacher->last_name = $request->last_name;
        $teacher->email = $request->email;
        $teacher->phone = $request->phone;
        $teacher->user_id = 1;


        if ($request->hasFile('image')) {
            if ($teacher->image && Storage::disk('public')->exists($teacher->image)) {
                Storage::disk('public')->delete($teacher->image);
            }

            $path = $request->file('image')->store('teachers', 'public');
            $teacher->image = $path;
        }

        $teacher->update();

        return redirect()->route('teachers.index')->with('success', 'Teacher created successfully.');
    }

    public function destroy($id)
    {
        $teacher = Teacher::where('id', $id)->first();
        if ($teacher->image && Storage::disk('public')->exists($teacher->image)) {
            Storage::disk('public')->delete($teacher->image);
        }

        $teacher->delete();

        return redirect()->route('teachers.index')->with('success', 'Teacher deleted successfully.');
    }

    public function show($id)
    {
        $teacher = Teacher::with('user', 'classes')->findOrFail($id);
        $teacher->image_url = $teacher->image ? asset('storage/' . $teacher->image) : null;

        return Inertia::render('Teachers/View', [
            'teacher' => $teacher
        ]);
    }
}