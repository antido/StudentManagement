import { router } from '@inertiajs/react';

export default function Pagination({ links, align = 'center' }) {
    if (!links || links.length === 0) return null;

    const handlePageChange = (url) => {
        if (url) router.visit(url);
    };

    const alignment = {
        left: 'justify-start',
        center: 'justify-center',
        right: 'justify-end',
    }[align];

    return (
        <div className={`flex ${alignment} mt-4 gap-2 text-sm`}>
            {links.map((link, idx) => (
                <button
                    key={idx}
                    onClick={() => handlePageChange(link.url)}
                    disabled={!link.url}
                    className={`px-3 py-1 rounded ${link.active
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        } ${!link.url ? 'opacity-50 cursor-not-allowed' : ''}`}
                    dangerouslySetInnerHTML={{ __html: link.label }}
                />
            ))}
        </div>
    );
}
