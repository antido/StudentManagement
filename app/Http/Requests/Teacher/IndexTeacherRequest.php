<?php

namespace App\Http\Requests\Teacher;

use App\Http\Requests\IndexRequest;
use App\Services\TeacherService;

class IndexTeacherRequest extends IndexRequest
{
    protected function sortable(): array
    {
        return array_keys(TeacherService::SORTABLE);
    }
}
