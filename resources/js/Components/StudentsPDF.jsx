function StudentsPDF({ studentId }) {
    const openReportPdf = () => {
        window.open(`/students/${studentId}/report-pdf`, '_blank');
    };

    return (
        <button
            onClick={openReportPdf}
            className="inline-block px-3 py-1 mx-1 bg-gray-600 text-white rounded hover:bg-gray-700 transition"
        >
            View Report PDF
        </button>
    );
}

export default StudentsPDF;