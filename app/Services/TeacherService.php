<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeacherService
{
    /**
     * Sort keys accepted from the UI mapped to their database columns.
     */
    public const SORTABLE = [
        'id' => 'id',
        'name' => 'first_name',
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'email' => 'email',
        'phone' => 'phone',
    ];

    public function paginate(?string $search, string $sort, string $direction): LengthAwarePaginator
    {
        return Teacher::with('user:id,name')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->whereRaw(
                        "CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?",
                        ["%{$search}%"]
                    )->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy(self::SORTABLE[$sort], $direction)
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Lightweight list used by dropdowns.
     */
    public function options(): Collection
    {
        return Teacher::select('id', 'first_name', 'middle_name', 'last_name')->get();
    }

    public function find(int|string $id, array $relations = []): Teacher
    {
        return Teacher::with($relations)->findOrFail($id);
    }

    /**
     * Create the teacher together with its login account.
     */
    public function create(array $data, ?UploadedFile $image = null): Teacher
    {
        return DB::transaction(function () use ($data, $image) {
            $user = User::create([
                'name' => $data['first_name'].' '.$data['middle_name'].' '.$data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
            ]);

            $data['user_id'] = $user->id;

            if ($image) {
                $data['image'] = $image->store('teachers', 'public');
            }

            return Teacher::create($data);
        });
    }

    public function update(Teacher $teacher, array $data, ?UploadedFile $image = null): Teacher
    {
        if ($image) {
            $this->deleteImage($teacher);
            $data['image'] = $image->store('teachers', 'public');
        }

        $teacher->update($data);

        return $teacher;
    }

    public function delete(Teacher $teacher): void
    {
        $this->deleteImage($teacher);
        $teacher->delete();
    }

    private function deleteImage(Teacher $teacher): void
    {
        if ($teacher->image && Storage::disk('public')->exists($teacher->image)) {
            Storage::disk('public')->delete($teacher->image);
        }
    }
}
