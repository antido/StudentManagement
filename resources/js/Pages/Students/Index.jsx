import { usePage, router } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { useTranslation } from 'react-i18next';
import { useState } from 'react';

export default function Students() {
    const {students, search:initialSearch, sort, direction} = usePage().props;
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

    const handlePageChange = (url) => {
        if (url) router.visit(url);
    }

    return (
        <DashboardLayout>
            <main className="flex-1 p-6">
                <header className="mb-6 border-b pb-4">
                    <h1 className="text-2xl font-bold text-gray-800">
                        {t('Students Page')}
                    </h1>
                    <p className="text-sm text-gray-500">
                        {t('Welcome to the student management section.')}
                    </p>
                </header>

                <form onSubmit={handleSearch}>
                    <input 
                        type="text" 
                        placeholder={t('Search Students')} 
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                    />
                    <button type="submit">{t('Search')}</button>
                </form>

                <section className="space-y-4">
                    <div className="bg-white p-6 rounded shadow">
                        <p className="text-gray-700">
                            {t('Here you can manage student data, view details, etc.')}
                        </p>
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
                                </tr>
                            </thead>
                            <tbody>
                                {students.data.map((student, index) => {
                                    const fullName = student.first_name + ' ' + student.middle_name + ' ' + student.last_name;

                                    return (
                                        <tr key={index}>
                                            <td className="p-2">{index + 1}</td>
                                            <td className="p-2">{fullName}</td>
                                            <td className="p-2">{student.email}</td>
                                            <td className="p-2">{student.gender}</td>
                                            <td className="p-2">{student.score}</td>
                                        </tr>
                                    ) 
                                })}
                            </tbody>
                        </table>

                        <div className="flex justify-center items-center gap-1 mt-4">
                            {students.links.map((link, idx) => (
                                <button
                                    key={idx}
                                    disabled={!link.url}
                                    className={`px-3 py-1 rounded ${
                                        link.active
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                    } ${!link.url ? 'opacity-50 cursor-not-allowed' : ''}`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                    onClick={() => handlePageChange(link.url)}
                                />
                            ))}
                        </div>
                    </div>
                </section>
            </main>
        </DashboardLayout>
    );
}