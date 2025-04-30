<!DOCTYPE html>
<html>
<head>
    <title>Payment History PDF</title>
    <link rel="stylesheet" href="{{ asset('css/pdf.css') }}">
    <style>
        body {
    font-family: Arial, sans-serif;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
th {
    background-color: #f2f2f2;
}
.header {
    text-align: center;
    margin-bottom: 20px;
}
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $organization }} Organization</h1>
        <h2>{{ $currentSemester->semester }} - Academic Year: {{ $currentSemester->academic_year }}</h2>
        @if ($section)
            <h3>Section: {{ $section }}</h3>
        @else
            <h3>All Sections</h3>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Number</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Section</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td>{{ $student->id_number }}</td>
                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ $student->section }}</td>
                    <td>{{ $student->payment_status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>