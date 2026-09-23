<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row): Model|null
    {
        $user = User::firstOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'],
                'password' => Hash::make('password'),
            ]
        );

        return new Student([
            'first_name' => $row['first_name'],
            'middle_name' => $row['middle_name'],
            'last_name' => $row['last_name'],
            'email' => $row['email'],
            'age' => $row['age'],
            'birthday' => $row['birthday'],
            'gender' => $row['gender'],
            'score' => $row['score'],
            'user_id'       => $user->id,
        ]);
    }
}
