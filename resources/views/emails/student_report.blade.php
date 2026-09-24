<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Report</title>
</head>
<body>
    <p>Hello {{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }},</p>
    <p>Your report card is attached as a PDF.</p>
    <p>Best regards,<br>School Management Team</p>
</body>
</html>