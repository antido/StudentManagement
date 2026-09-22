import { useForm, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { useTranslation } from 'react-i18next';

export default function EditTeacher({ teacher }) {
    const {t, i18n} = useTranslation();
    const { data, setData, put, errors } = useForm({
        id: teacher.id,
        first_name: teacher.first_name,
        middle_name: teacher.middle_name,
        last_name: teacher.last_name,
        email: teacher.email,
        phone: teacher.phone,
        image: null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(`/teachers/${teacher.id}`);
    };

    return (
        <DashboardLayout>
            <main className="p-6 flex justify-center items-center min-h-screen bg-gray-100">
                <div className="w-full max-w-xl bg-white rounded-2xl shadow-lg p-8">
                    <Link
                        href={route('teachers.index')}
                        className="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
                    >
                        {t('Back to List')}
                    </Link>

                    <h1 className="text-3xl font-semibold text-gray-800 mb-6 text-center">Edit Teacher</h1>

                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div>
                            <label className="block text-gray-700 font-medium mb-1">First Name</label>
                            <input
                                name="first_name"
                                value={data.first_name}
                                onChange={(e) => setData('first_name', e.target.value)}
                                placeholder="Enter first name"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                            {errors.first_name && <div className="text-red-600">{errors.first_name}</div>}
                        </div>

                        <div>
                            <label className="block text-gray-700 font-medium mb-1">Middle Name</label>
                            <input
                                name="middle_name"
                                value={data.middle_name}
                                onChange={(e) => setData('middle_name', e.target.value)}
                                placeholder="Enter middle name"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                            {errors.middle_name && <div className="text-red-600">{errors.middle_name}</div>}
                        </div>

                        <div>
                            <label className="block text-gray-700 font-medium mb-1">Last Name</label>
                            <input
                                name="last_name"
                                value={data.last_name}
                                onChange={(e) => setData('last_name', e.target.value)}
                                placeholder="Enter last name"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                            {errors.last_name && <div className="text-red-600">{errors.last_name}</div>}
                        </div>

                        <div>
                            <label className="block text-gray-700 font-medium mb-1">Email</label>
                            <input
                                name="email"
                                type="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                placeholder="Enter email"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                            {errors.email && <div className="text-red-600">{errors.email}</div>}

                        </div>

                        <div>
                            <label className="block text-gray-700 font-medium mb-1">Phone</label>
                            <input
                                name="phone"
                                value={data.phone}
                                onChange={(e) => setData('phone', e.target.value)}
                                placeholder="Enter phone number"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                            {errors.phone && <div className="text-red-600">{errors.phone}</div>}

                        </div>
                        <div className="col-span-full">
                            <label className="block text-gray-700 font-medium mb-1">Image</label>
                            <input
                                type="file"
                                accept="image/*"
                                onChange={e => setData('image', e.target.files[0])}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                            {errors.image && <div className="text-red-600">{errors.image}</div>}

                        </div>
                        <div className="pt-2">
                            <button
                                type="submit"
                                className="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-200 text-lg font-semibold"
                            >
                                Update Teacher
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </DashboardLayout>
    );
}