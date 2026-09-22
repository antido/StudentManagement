import { useForm, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { useTranslation } from 'react-i18next';

export default function CreateStudent() {
    const {t, i18n} = useTranslation();
    const { data, setData, post, errors } = useForm({
        first_name: '',
        middle_name: '',
        last_name: '',
        email: '',
        age: '',
        birthday: '',
        gender: 'm',
        score: '',
        image: null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('students.store'));
    };

    return (
        <DashboardLayout>
            <main className="p-6 flex justify-center items-center min-h-screen bg-gray-100">
                <div className="w-full max-w-2xl bg-white rounded-2xl shadow-lg p-8">
                    <Link
                        href={route('students.index')}
                        className="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
                    >
                        {t('Back to List')}
                    </Link>
                    
                    <h1 className="text-3xl font-semibold text-gray-800 mb-6 text-center">Create Student</h1>

                    {errors.error && (
                        <div className="col-span-full bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <strong className="font-bold">Error:</strong>
                            <span className="block sm:inline ml-2">{errors.error}</span>
                        </div>
                    )}

                    <form onSubmit={handleSubmit} className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div className="col-span-full">
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

                        <div className="col-span-full">
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

                        <div className="col-span-full">
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

                        <div className="col-span-full">
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
                            <label className="block text-gray-700 font-medium mb-1">Age</label>
                            <input
                                name="age"
                                type="number"
                                value={data.age}
                                onChange={(e) => setData('age', e.target.value)}
                                placeholder="Age"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                            {errors.age && <div className="text-red-600">{errors.age}</div>}

                        </div>

                        <div>
                            <label className="block text-gray-700 font-medium mb-1">Date of Birth</label>
                            <input
                                name="birthday"
                                type="date"
                                value={data.birthday}
                                onChange={(e) => setData('birthday', e.target.value)}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                            {errors.birthday && <div className="text-red-600">{errors.birthday}</div>}

                        </div>

                        <div>
                            <label className="block text-gray-700 font-medium mb-1">Gender</label>
                            <select
                                name="gender"
                                value={data.gender}
                                onChange={(e) => setData('gender', e.target.value)}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            >
                                <option value="m">Male</option>
                                <option value="f">Female</option>
                            </select>
                            {errors.gender && <div className="text-red-600">{errors.gender}</div>}

                        </div>

                        <div>
                            <label className="block text-gray-700 font-medium mb-1">Score</label>
                            <input
                                name="score"
                                type="number"
                                value={data.score}
                                onChange={(e) => setData('score', e.target.value)}
                                placeholder="Score"
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                            {errors.score && <div className="text-red-600">{errors.score}</div>}

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
                        <div className="col-span-full mt-4">
                            <button
                                type="submit"
                                className="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-200 text-lg font-semibold"
                            >
                                Save Student
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </DashboardLayout>
    );
}