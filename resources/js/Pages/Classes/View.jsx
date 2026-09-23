import { Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { useTranslation } from 'react-i18next';

export default function ViewClass({ classItem }) {
    const { t } = useTranslation();

    return (
        <DashboardLayout>
            <main className="p-6 max-w-2xl mx-auto bg-white shadow rounded">
                <Link
                    href={route('classes.index')}
                    className="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
                >
                    {t('Back to List')}
                </Link>

                <h1 className="text-2xl font-bold my-4">View Class</h1>

                <div className="space-y-4">
                    <div>
                        <strong>ID:</strong> {classItem.id}
                    </div>
                    <div>
                        <strong>Name:</strong> {classItem.name}
                    </div>
                    <div>
                        <strong>Description:</strong> {classItem.description}
                    </div>
                    <div>
                        <strong>Teacher:</strong> {classItem.teacher?.first_name} {classItem.teacher?.middle_name} {classItem.teacher?.last_name}
                    </div>
                </div>
            </main>
        </DashboardLayout>
    );
}