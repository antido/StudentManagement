import { usePage, router, Link  } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { useTranslation } from 'react-i18next';
import { useState } from 'react';
import Pagination from '@/Components/Pagination';
import StudentsPDF from '@/Components/StudentsPDF';

export default function Students() {
    const {students, search:initialSearch, sort, direction, flash} = usePage().props;
    const {t, i18n} = useTranslation();

    const [search, setSearch] = useState(initialSearch || '');

    // Handle Search
    const handleSearch = (e) => {
        e.preventDefault();

        router.get('students', {search}, {
            preserveState: true,
            replace: true
        });
    }

    // Handle Sort
    const handleSort = (field) => {
        const newDirection = sort === field && direction === 'asc' ? 'desc' : 'asc';

        router.get('students', {search, sort: field, direction: newDirection}, {
            preserveState: true,
            replace: true
        });
    }

    const renderSortArrow = (field) => {
        if (sort !== field) return null;
        return direction === 'asc' ? '▲' : '▼';
    }

    const [msg, setMsg] = useState(flash.success);

    setTimeout(() => {
        setMsg(null)
    }, 2000);

    // Handle Delete
    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this student?')) {
            router.visit(route('students.destroy', id), {
                method: 'delete',
            });
        }
    };

    return (
        <DashboardLayout>
            <main className="flex-1 p-6">
                {msg && (
                    <div className="mb-4 p-4 bg-green-100 text-green-800 border border-green-300 rounded">
                        {msg}
                    </div>
                )}
                <header className="mb-6 border-b pb-4">
                    <h1 className="text-2xl font-bold text-gray-800">
                        {t('Students Page')}
                    </h1>
                    <p className="text-sm text-gray-500">
                        {t('Welcome to the student management section.')}
                    </p>
                </header>

                <div className="overflow-x-auto bg-white rounded shadow p-4">
                    <div className="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        {/* Search form */}
                        <form onSubmit={handleSearch} className="flex gap-2">
                            <input
                                type="text"
                                placeholder={t('Search students...')}
                                className="w-full md:w-64 px-3 py-2 border rounded"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                            />
                            <button type="submit" className="px-4 py-2 bg-blue-600 text-white rounded">
                                {t('Search')}
                            </button>
                        </form>

                        {/* Right-side actions */}
                        <div className="flex flex-wrap items-center gap-2">
                            {/* Create */}
                            <Link
                                href={route('students.create')}
                                className="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition"
                            >
                                {t('Create Student')}
                            </Link>

                            {/* Export */}
                            <a
                                href={route('students.export')}
                                className="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition"
                            >
                                {t('Export Students')}
                            </a>

                            {/* Import */}
                            <form
                                onSubmit={(e) => {
                                    e.preventDefault();
                                    const formData = new FormData(e.target);
                                    router.post(route('students.import'), formData, {
                                        forceFormData: true,
                                    });
                                }}
                                className="flex items-center gap-2"
                            >
                                <input type="file" name="file" accept=".csv,.xlsx" required className="text-sm" />
                                <button
                                    type="submit"
                                    className="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded hover:bg-purple-700 transition"
                                >
                                    {t('Import Students')}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div className="overflow-x-auto bg-white rounded shadow p-4">
                        <table className="min-w-full table-auto">
                            <thead>
                                <tr className="bg-gray-100 text-left text-sm font-medium text-gray-700">
                                    <th
                                        className="p-2 cursor-pointer"
                                        onClick={() => handleSort('id')}
                                    >
                                        # {renderSortArrow('id')}
                                    </th>

                                    <th
                                        className="p-2 cursor-pointer"
                                        onClick={() => handleSort('first_name')}
                                    >
                                        Name {renderSortArrow('first_name')}
                                    </th>

                                    <th
                                        className="p-2 cursor-pointer"
                                        onClick={() => handleSort('email')}
                                    >
                                        Email {renderSortArrow('email')}
                                    </th>

                                    <th
                                        className="p-2 cursor-pointer"
                                        onClick={() => handleSort('gender')}
                                    >
                                        Gender {renderSortArrow('gender')}
                                    </th>

                                    <th
                                        className="p-2 cursor-pointer"
                                        onClick={() => handleSort('score')}
                                    >
                                        Score {renderSortArrow('score')}
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {students.data.map((student, index) => {
                                    const fullName = student.first_name + ' ' + student.middle_name + ' ' + student.last_name;

                                    return (
                                        <tr key={student.id} className="border-b text-sm">
                                            <td className="p-2">{(students.current_page - 1) * students.per_page + index + 1}</td>
                                            <td className="p-2">{fullName}</td>
                                            <td className="p-2">{student.email}</td>
                                            <td className="p-2">{student.gender}</td>
                                            <td className="p-2">{student.score}</td>
                                            <td className="p-2">
                                                <Link
                                                    href={`/students/edit/${student.id}`} 
                                                    className="inline-block px-3 py-1 mx-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition"
                                                >
                                                    Edit
                                                </Link>

                                                <Link
                                                    href={route('students.show', student.id)} 
                                                    className="inline-block px-3 py-1 mx-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition mr-2"
                                                >
                                                    View
                                                </Link>

                                                <button
                                                    onClick={() => handleDelete(student.id)} 
                                                    className="inline-block px-3 py-1 mx-1 bg-red-600 text-white rounded hover:bg-red-700 transition"
                                                >
                                                    Delete
                                                </button>

                                                <StudentsPDF studentId={student.id} />
                                            </td>
                                        </tr>
                                    ) 
                                })}
                            </tbody>
                        </table>

                        {/* Pagination */}
                        <Pagination links={students.links} align="center" />
                    </div>
                </div>
            </main>
        </DashboardLayout>
    );
}