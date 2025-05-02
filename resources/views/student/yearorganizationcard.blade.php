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
                <h2>Sophomore Class Organization</h2>
                <div class="col-12 mb-3 position-relative">
                    <a href="{{ route('student.organizationcard') }}" class="text-decoration-none text-dark d-block">
                  
                        <div class="card-student shadow-sm ">
                            <h4>School Year: 2024-2025</h4>
                            <div class="align-items-center gap-3">
                                
                                <div class="row">
                                    <div class="col-12 mb-3 position-relative">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <h5 class="col-6 m-0">Class Treasurer: John Carl Uribe</h5>
                                                    <h5 class="col-6 m-0">Date of Transaction: 11/19/2024</h5>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row">
                                                    <h5 class="col-6 m-0">First Semester Payment</h5>
                                                    <h5 class="col-6 m-0">Status: Paid</h5>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3 position-relative">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <h5 class="col-6 m-0">Class Treasurer: John Carl Uribe</h5>
                                                    <h5 class="col-6 m-0">Date of Transaction: 11/19/2024</h5>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row">
                                                    <h5 class="col-6 m-0">First Semester Payment</h5>
                                                    <h5 class="col-6 m-0">Status: Paid</h5>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                   
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