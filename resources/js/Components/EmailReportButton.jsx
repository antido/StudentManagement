import { router } from '@inertiajs/react';

function EmailReportButton({ studentId }) {
    const sendEmailReport = () => {
        router.get(`/students/${studentId}/email-report`);
    };

    return (
        <button
            onClick={sendEmailReport}
            className="inline-block px-3 py-1 mx-1 bg-green-600 text-white rounded hover:bg-green-700 transition"
        >
            Send Report to Email
        </button>
    );
}

export default EmailReportButton;