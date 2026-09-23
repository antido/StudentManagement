<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return Student::select('id', 'first_name', 'middle_name', 'last_name', 'email', 'gender', 'score')
                    ->get();
    }

    public function headings():array
    {
        return [
            'ID',
            'First Name',
            'Middle Name',
            'Last Name',
            'Email',
            'Gender',
            'Score'
        ];
    }
}
