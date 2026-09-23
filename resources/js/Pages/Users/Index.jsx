import DashboardLayout from '@/Layouts/DashboardLayout';
import { usePage, router, Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { useState } from 'react';
import Pagination from '@/Components/Pagination';

export default function UsersIndex() {
    const { users, search: initialSearch, sort, direction, flash } = usePage().props;
    const { t } = useTranslation();
    const [search, setSearch] = useState(initialSearch || '');
    const [msg, setMsg] = useState(flash.success);

    setTimeout(() => setMsg(null), 2000);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('users', { search }, { preserveState: true, replace: true });
    };

    const handleSort = (field) => {
        const newDirection = sort === field && direction === 'asc' ? 'desc' : 'asc';
        router.get('users', { search, sort: field, direction: newDirection }, { preserveState: true, replace: true });
    };

    const renderSortArrow = (field) => {
        if (sort !== field) return null;
        return direction === 'asc' ? ' ▲' : ' ▼';
    };

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this user?')) {
            router.visit(route('users.destroy', id), { method: 'delete' });
        }
    };

    return (
        <DashboardLayout>
            <main className="flex-1 p-6">
                {msg && (
                    <div className="mb-4 p-4 bg-green-100 text-green-800 border border-green-300 rounded">{msg}</div>
                )}
                <header className="mb-6 border-b pb-4">
                    <h1 className="text-2xl font-bold text-gray-800">{t('Users Page')}</h1>
                    <p className="text-sm text-gray-500">{t('Manage application users')}</p>
                </header>

                <div className="overflow-x-auto bg-white rounded shadow p-4">

                    <div className="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        {/* Search Form */}
                        <form onSubmit={handleSearch} className="mb-4 flex gap-2">
                            <input 
                                type="text" 
                                placeholder={t('Search users...')} 
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
                            <Link 
                                href={route('users.create')} 
                                className="inline-block mb-4 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition"
                            >
                                {t('Create User')}
                            </Link>
                        </div>
                    </div>

                    <table className="min-w-full table-auto">
                        <thead>
                            <tr className="bg-gray-100 text-left text-sm font-medium text-gray-700">
                                <th className="p-2 cursor-pointer" onClick={() => handleSort('id')}># {renderSortArrow('id')}</th>
                                <th className="p-2 cursor-pointer" onClick={() => handleSort('name')}>{t('Name')}{renderSortArrow('name')}</th>
                                <th className="p-2 cursor-pointer" onClick={() => handleSort('email')}>{t('Email')}{renderSortArrow('email')}</th>
                                <th className='p-2'>{t('Action')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {users.data.map((user, index) => (
                                <tr key={user.id} className="border-b text-sm">
                                    <td className="p-2">{(users.current_page - 1) * users.per_page + index + 1}</td>
                                    <td className="p-2">{user.name}</td>
                                    <td className="p-2">{user.email}</td>
                                    <td className="p-2 space-x-2">
                                        <Link href={route('users.show', user.id)} className="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">View</Link>
                                        <Link href={route('users.edit', user.id)} className="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</Link>
                                        <button onClick={() => handleDelete(user.id)} className="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {/* Pagination */}
                    <Pagination links={users.links} align="center" />
                </div>
            </main>
        </DashboardLayout>
    );
}