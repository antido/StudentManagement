import DashboardLayout from '@/Layouts/DashboardLayout';
import { usePage, router, Link } from '@inertiajs/react';
import { useState } from 'react';

export default function ClassesIndex() {
    const { roles, flash } = usePage().props;
    const [msg, setMsg] = useState(flash.success);

    setTimeout(() => setMsg(null), 2000);

    return (
        <DashboardLayout>
            <main className="p-6">
                {msg && <div className="mb-4 p-4 bg-green-100 border rounded text-green-800">{msg}</div>}
                <header className="mb-6 border-b pb-4">
                    <h1 className="text-2xl font-bold text-gray-800">Roles</h1>
                </header>

                <Link
                    href={route('roles.create')}
                    className="inline-block mb-4 px-4 py-2 bg-green-600 text-white rounded"
                >
                    Create Roles
                </Link>

                <div className="overflow-x-auto bg-white rounded shadow p-4">
                    <table className="min-w-full table-auto">
                        <thead>
                            <tr className="bg-gray-100 text-left text-sm font-medium text-gray-700">
                                <th className="p-2">#</th>
                                <th className="p-2">Name</th>
                                <th className="p-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {roles.map(role => (
                                <tr key={role.id} className="border-b text-sm">
                                    <td className="p-2">{role.id}</td>
                                    <td className="p-2">{role.name}</td>
                                    <td className="p-2 space-x-2">
                                        <Link
                                            href={`roles/add-permission-to-role/${role.id}`}
                                            className="px-3 py-1 bg-yellow-500 text-white rounded"
                                        >
                                            Permissions
                                        </Link>

                                        <Link
                                            href={`roles/add-users-to-role/${role.id}`}
                                            className="inline-block px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition"
                                        >
                                            Assign Users
                                        </Link>

                                        {/* <Link
                                            href={route('students.show', role.id)}
                                            className="inline-block px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition mr-2"
                                        >
                                            Rename
                                        </Link> */}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </main>
        </DashboardLayout>
    );
}