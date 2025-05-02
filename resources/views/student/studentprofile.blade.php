<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Student Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    </head>
    <body>
        <h2>Welcome, {{ auth('student')->user()->first_name }}!</h2>
        <ul>
            <li><strong>ID Number:</strong> {{ auth('student')->user()->id_number }}</li>
            <li><strong>Full Name:</strong> {{ auth('student')->user()->first_name }} {{ auth('student')->user()->last_name }}</li>
            <li><strong>Organization:</strong> {{ auth('student')->user()->organization }}</li>
            <li><strong>Year Level:</strong> {{ auth('student')->user()->year_level }}</li>
            <li><strong>Section:</strong> {{ auth('student')->user()->section }}</li>
        </ul>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Logout</button>
        </form>
    </body>
</html>
