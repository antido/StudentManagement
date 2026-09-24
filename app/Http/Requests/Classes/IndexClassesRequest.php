<?php

namespace App\Http\Requests\Classes;

use App\Http\Requests\IndexRequest;
use App\Services\ClassesService;

class IndexClassesRequest extends IndexRequest
{
    protected function sortable(): array
    {
        return array_keys(ClassesService::SORTABLE);
    }
}
