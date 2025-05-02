<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Payment Admin | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin.css') }}">
</head>
<body>
    <div class="d-flex">
        @include('student.navstudent')

        <div class="main-content flex-grow-1 p-4">
            <div class="row position-relative">
                <h1 class="col-10 fw-bold text-warning mb-4">Hello, {{ $student->first_name }}!</h1>
            </div>

            <div class="line"></div>
            <br>

            <div class="row">
                <div class="col-12 mb-3 position-relative">
                    <a href="{{ route('student.organizationcard') }}" class="text-decoration-none text-dark d-block">
                        <div class="card-custom shadow-sm d-flex justify-content-between align-items-center">
                            <div class="line-separator"></div>
                            <div class="d-flex align-items-center gap-3">
                                <div></div>
                                <div>
                                    <h3 class="fw-bold org-title mb-1">
                                        <p>{{ $student->organization }}</p>
                                    </h3>
                                </div>
                            </div>
                        </div>    
                    </a>
                </div>
                <div class="col-12 mb-3 position-relative">
                    <a href="{{ route('student.yearorganizationcard') }}" class="text-decoration-none text-dark d-block">
                    <div class="card-custom shadow-sm d-flex justify-content-between align-items-center">
                            <div class="line-separator"></div>
                            <div class="d-flex align-items-center gap-3">
                                <div></div>
                                <div>
                                    <h3 class="fw-bold org-title mb-1">
                                        <p>
                                            @switch($student->year_level)
                                                @case(1)
                                                    SCO
                                                    @break
                                                @case(2)
                                                    FCO
                                                    @break
                                                @case(3)
                                                    JCO
                                                    @break
                                                @case(4)
                                                    SENCO
                                                    @break
                                                @default
                                                    Unknown Year Level
                                            @endswitch
                                        </p>
                                    </h3>
                                </div>
                            </div>
                        
                    </div>
                    </a>
                </div>
            </div>
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