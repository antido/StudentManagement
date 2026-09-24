<?php

namespace App\Http\Requests\User;

use App\Http\Requests\IndexRequest;
use App\Services\UserService;

class IndexUserRequest extends IndexRequest
{
    protected function sortable(): array
    {
        return array_keys(UserService::SORTABLE);
    }
}
