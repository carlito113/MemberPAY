<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Payment Admin | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/student.css') }}">
</head>
<body>
    <div class="d-flex">
        @include('student.navstudent')

        <div class="main-content flex-grow-1 p-4">
            <div class="row position-relative">
                <h1 class="col-10 fw-bold text-warning mb-4">Hello, {{ $student->first_name }}!</h1>
                <div class="col-2">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                </div>
            </div>

            <div class="line"></div>
            <br>

            <div class="row">
    @php
        $groupedSemesters = $semesters->groupBy(function ($sem) {
            return $sem->admin->username ?? 'Unknown Organization';
        });
    @endphp

    @forelse ($groupedSemesters as $orgName => $orgSemesters)
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $orgName }}</h4>
                </div>
                <div class="card-body">
                    @php
                        $groupedByYear = $orgSemesters->groupBy('academic_year');
                    @endphp

                    @foreach ($groupedByYear as $schoolYear => $semestersInYear)
                        <div class="border rounded p-3 mb-4 bg-light">
                            <h5 class="text-primary mb-3">School Year: {{ $schoolYear }}</h5>
                            
                            @foreach ($semestersInYear as $semester)
                                <div class="mb-3">
                                    <div class="row">
                                        <h6 class="col-md-6 m-0">Semester: {{ $semester->semester }}</h6>
                                        <h6 class="col-md-6 m-0">Status: {{ ucfirst($semester->pivot->payment_status) }}</h6>
                                    </div>
                                    <div class="row mt-2">
                                        <p class="col-md-6 m-0">Date of Transaction: {{ \Carbon\Carbon::parse($semester->pivot->updated_at)->format('m/d/Y') }}</p>
                                        <p class="col-md-6 m-0"><h6 class="m-0">Treasurer: {{ $semester->pivot->admin_name ?? 'Unknown' }}</h6>

                                        </p>
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">No payment records found.</p>
    @endforelse
</div>

    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.querySelector('.toggle-btn');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            // 1. Restore sidebar state from localStorage
            const isSidebarOpen = localStorage.getItem('sidebarOpen') === 'true';
            if (isSidebarOpen) {
                sidebar.classList.add('open');
                if (mainContent) mainContent.classList.add('shifted');
            }

            // 2. Toggle sidebar and update localStorage
            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('open');
                if (mainContent) mainContent.classList.toggle('shifted');

                // Save the state
                localStorage.setItem('sidebarOpen', sidebar.classList.contains('open'));
            });
        });

    </script>

    

</body>
</html>