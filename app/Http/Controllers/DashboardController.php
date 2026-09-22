<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classes;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $studentMonthly = Student::selectRaw('Month(created_at) as month, count(*) as total')
                                ->groupBy('month')
                                ->orderBy('month')
                                ->get()
                                ->pluck('total', 'month');

        $teacherMonthly = Teacher::selectRaw('Month(created_at) as month, count(*) as total')
                                ->groupBy('month')
                                ->orderBy('month')
                                ->get()
                                ->pluck('total', 'month');

        $months = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec',
        ];

        $chartData = [];

        for ($month = 1; $month <= 12; $month++) {
            $chartData[] = [
                'name' => $months[$month - 1],
                'Students' => $studentMonthly->get($month, 0),
                'Teachers' => $teacherMonthly->get($month, 0),
            ];
        }

        return inertia('Dashboard', [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'classes' => Classes::count(),
            'subjects' => 12,
            'chartData' => $chartData
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
