<?php

namespace App\Services;

use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use App\Mail\StudentReportMail;
use App\Models\Student;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentService
{
    /**
     * Sort keys accepted from the UI mapped to their database columns.
     */
    public const SORTABLE = [
        'id' => 'id',
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'email' => 'email',
        'age' => 'age',
        'gender' => 'gender',
        'score' => 'score',
    ];

    public function paginate(?string $search, string $sort, string $direction): LengthAwarePaginator
    {
        return Student::with('user:id,name')
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

    public function find(int|string $id, array $relations = []): Student
    {
        return Student::with($relations)->findOrFail($id);
    }

    /**
     * Create the student together with its login account.
     */
    public function create(array $data, ?UploadedFile $image = null): Student
    {
        return DB::transaction(function () use ($data, $image) {
            $user = User::create([
                'name' => $this->fullName($data),
                'email' => $data['email'],
                'password' => Hash::make('password'),
            ]);

            $data['user_id'] = $user->id;

            if ($image) {
                $data['image'] = $image->store('students', 'public');
            }

            return Student::create($data);
        });
    }

    /**
     * Update the student and keep its login account in sync.
     */
    public function update(Student $student, array $data, ?UploadedFile $image = null): Student
    {
        return DB::transaction(function () use ($student, $data, $image) {
            if ($image) {
                $this->deleteImage($student);
                $data['image'] = $image->store('students', 'public');
            }

            $student->update($data);

            $student->user?->update([
                'name' => $this->fullName($data),
                'email' => $data['email'],
            ]);

            return $student;
        });
    }

    public function delete(Student $student): void
    {
        $this->deleteImage($student);
        $student->delete();
    }

    public function export(): BinaryFileResponse
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }

    public function import(UploadedFile $file): void
    {
        Excel::import(new StudentsImport, $file);
    }

    public function reportPdf(Student $student): \Barryvdh\DomPDF\PDF
    {
        $student->loadMissing('studentClasses.class');

        return Pdf::loadView('pdfs.student_report', compact('student'))
            ->setPaper('a4', 'portrait');
    }

    public function emailReport(Student $student): void
    {
        Mail::to($student->email)->send(
            new StudentReportMail($student, $this->reportPdf($student)->output())
        );
    }

    private function deleteImage(Student $student): void
    {
        if ($student->image && Storage::disk('public')->exists($student->image)) {
            Storage::disk('public')->delete($student->image);
        }
    }

    private function fullName(array $data): string
    {
        return trim(preg_replace('/\s+/', ' ', implode(' ', [
            $data['first_name'],
            $data['middle_name'] ?? '',
            $data['last_name'],
        ])));
    }
}
