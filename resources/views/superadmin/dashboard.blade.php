<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Super Admin | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidenav.css') }}">
</head>
<body>
    <div class="d-flex">
        @include('superadmin.navsuperadmin')

        <!-- Main content -->
        <div class="main-content flex-grow-1 p-4">
            <h2 class="fw-bold text-warning mb-4">👋 Hello, Super Admin!</h2>
            

            <div class="row">
            @foreach ($organizations as $org)
            <p>{{ json_encode($org->semesters) }}</p>

<div class="col-12 mb-3">
    <div class="card-custom shadow-sm d-flex justify-content-between align-items-center">
        <div class="line-separator"></div>
        <div class="d-flex align-items-center gap-3">
            <div></div>
            <div>
                <h3 class="fw-bold org-title mb-1">{{ strtoupper($org->username) }}</h3>
                <p class="mb-0">{{ $org->name }}</p>
            </div>
        </div>
        <div class="text-end">
            <small>Total Members:</small>
            <h5 class="fw-semibold">{{ $org->students_count }}</h5>
        </div>
    </div>
</div>
@endforeach
            </div> 
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.querySelector('.toggle-btn');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('open');
                mainContent.classList.toggle('shifted');
            });
        });
    </script>
</body>
</html>