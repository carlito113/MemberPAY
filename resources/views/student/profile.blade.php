<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Student Profile</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('css/student.css') }}">

    </head>
    <body>
        @include('student.navstudent')
        <div class="d-flex">
            <div class="main-content flex-grow-1 p-4">
                <div class="main-container">
                    <div class="row position-relative">
                        <h1 class="col-10 fw-bold text-primary mb-4"> My Personal Information </h1>
                    </div>
                    <div class="line"></div>
                    <br>
                    <div class="container mt-4">
                        <div class="card shadow-sm rounded-4 border-0">
                            <div class="card-header bg-warning text-white rounded-top-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0 fw-semibold">{{ auth('student')->user()->first_name ?? 'Student' }}'s Profile</h4>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>ID Number:</strong></p>
                                        <p>{{ auth('student')->user()->id_number }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Full Name:</strong></p>
                                        <p>{{ auth('student')->user()->first_name }} {{ auth('student')->user()->last_name }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Contact Number:</strong></p>
                                        <p>{{ auth('student')->user()->contact_number }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Organization:</strong></p>
                                        <p>{{ auth('student')->user()->organization }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Year Level:</strong></p>
                                        <p>{{ auth('student')->user()->year_level }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Section:</strong></p>
                                        <p>{{ auth('student')->user()->section }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </body>
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
</html>
