<?php

namespace App\Services;

use App\Models\Classes;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClassesService
{
    /**
     * Sort keys accepted from the UI mapped to their database columns.
     */
    public const SORTABLE = [
        'id' => 'id',
        'name' => 'name',
    ];

    public function paginate(?string $search, string $sort, string $direction): LengthAwarePaginator
    {
        return Classes::with('teacher:id,first_name,middle_name,last_name')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('teacher', function ($query) use ($search) {
                            $query->whereRaw(
                                "CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?",
                                ["%{$search}%"]
                            );
                        });
                });
            })
            ->orderBy(self::SORTABLE[$sort], $direction)
            ->paginate(10)
            ->withQueryString();
    }

    public function find(int|string $id, array $relations = []): Classes
    {
        return Classes::with($relations)->findOrFail($id);
    }

    public function create(array $data): Classes
    {
        return Classes::create($data);
    }

    public function update(Classes $class, array $data): Classes
    {
        $class->update($data);

        return $class;
    }

    public function delete(Classes $class): void
    {
        $class->delete();
    }
}
