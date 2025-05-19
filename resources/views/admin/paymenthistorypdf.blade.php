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
     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <img src="{{ public_path('OrganizationLogo/' . strtoupper($organization) . '.png') }}" alt="Org Logo" height="70" class="img-fluid">
        <img src="{{ public_path('OrganizationLogo/lnu.png') }}" alt="LNU Logo" height="70" class="img-fluid">
    </div>

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
                <th>Last Name</th>
                <th>First Name</th>
                <th>Section</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td>{{ $student->id_number }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->section }}</td>
                   <td>{{ $student->pivot->payment_status }}</td> <!-- ✅ Correct way -->

                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        <p class="fw-2">Total Paid: {{ $totalPaid }}</p>
        <p class="fw-2">Total Unpaid: {{ $totalUnpaid }}</p>
</body>
</html>