<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DashboardService
{
    private const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    public function stats(): array
    {
        return [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'classes' => Classes::count(),
            'subjects' => 12,
            'chartData' => $this->chartData(),
        ];
    }

    /**
     * Students and teachers created per month.
     */
    private function chartData(): array
    {
        $studentMonthly = $this->monthlyTotals(Student::class);
        $teacherMonthly = $this->monthlyTotals(Teacher::class);

        $chartData = [];

        foreach (self::MONTHS as $index => $name) {
            $month = $index + 1;

            $chartData[] = [
                'name' => $name,
                'Students' => $studentMonthly->get($month, 0),
                'Teachers' => $teacherMonthly->get($month, 0),
            ];
        }

        return $chartData;
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function monthlyTotals(string $model): Collection
    {
        return $model::selectRaw('Month(created_at) as month, count(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');
    }
}
