<?php

namespace App\Http\Requests\Student;

use App\Http\Requests\IndexRequest;
use App\Services\StudentService;

class IndexStudentRequest extends IndexRequest
{
    protected function sortable(): array
    {
        return array_keys(StudentService::SORTABLE);
    }
}
