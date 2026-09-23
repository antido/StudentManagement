import { useForm, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { useTranslation } from 'react-i18next';

export default function CreateClass({ teachers }) {
    const { t } = useTranslation();
    const { data, setData, post, errors } = useForm({
        name: '',
        description: '',
        teacher_id: ''
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('classes.store'));
    };

    return (
        <DashboardLayout>
            <main className="p-6 flex justify-center items-center min-h-screen bg-gray-100">
                <div className="w-full max-w-2xl bg-white p-8 rounded shadow">
                    <Link
                        href={route('classes.index')}
                        className="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
                    >
                        {t('Back to List')}
                    </Link>

                    <h1 className="text-2xl my-4">Create Class</h1>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label className="block mb-1">Name</label>
                            <input
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                className="w-full border px-3 py-2 rounded"
                            />
                            {errors.name && <div className="text-red-500 text-sm">{errors.name}</div>}
                        </div>
                        <div>
                            <label className="block mb-1">Description</label>
                            <textarea
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                                className="w-full border px-3 py-2 rounded"
                            />
                            {errors.description && <div className="text-red-500 text-sm">{errors.description}</div>}
                        </div>
                        <div>
                            <label className="block mb-1">Teacher</label>
                            <select
                                value={data.teacher_id}
                                onChange={(e) => setData('teacher_id', e.target.value)}
                                className="w-full border px-3 py-2 rounded"
                            >
                                <option value="">Select a teacher</option>
                                {teachers.map(teacher => (
                                    <option key={teacher.id} value={teacher.id}>{teacher.first_name} {teacher.middle_name} {teacher.last_name}</option>
                                ))}
                            </select>
                            {errors.teacher_id && <div className="text-red-500 text-sm">{errors.teacher_id}</div>}
                        </div>
                        <button type="submit" className="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                    </form>
                </div>
            </main>
        </DashboardLayout>
    );
}