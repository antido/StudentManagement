<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

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

        $students = Student::when($search, function($query, $search){
                               $query->whereRaw(
                                            "CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?",
                                            ["%{$search}%"]
                                        )
                                    ->orWhere('email', 'like', "%{$search}%");
                            })
                            ->orderBy($sortField, $sortDirection)
                            ->paginate(5)
                            ->withQueryString();

        return inertia('Students/Index', [
            'students' => $students,
            'search' => $search,
            'sort' => $sortField,
            'direction' => $sortDirection
        ]);
    }

    public function withData()
    {    
        return inertia('Students/Index', [
            'a' => 'Name',
            'b' => 'Last Name'
        ]);
    }

    public function withRouteParameters($name = 'Guest', $last_name = 'User')
    {
        return inertia('Students/Index', [
            'name' => $name,
            'last_name' => $last_name
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
