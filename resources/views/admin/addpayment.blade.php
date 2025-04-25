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
        @include('admin.navadmin')

        <div class="main-content flex-grow-1 p-4">
            <div class="row position-relative">
                <h1 class="col-10 fw-bold text-warning mb-4">{{ $organization }} ORGANIZATION</h1>
                <i class="col-2 bi bi-plus-circle corner-icon mb-4" data-bs-toggle="modal" data-bs-target="#addSem">New Record</i>
            </div>

            <div class="line"></div>
            <br>

            <div class="row">
                @if($semesters->isNotEmpty())
                    @foreach ($semesters as $sem)
                        <div class="col-12 mb-3">
                            <div class="card-custom shadow-sm d-flex justify-content-between align-items-center">
                                <div class="line-separator"></div>
                                <div class="d-flex align-items-center gap-3">
                                    <div></div>
                                  <div>
                                        <h3 class="fw-bold org-title mb-1">
                                            {{ strtoupper($sem->semester) }} COLLECTION - Academic Year: {{ $sem->academic_year }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="dots">
                                    <h2>
                                        <a href="{{ route('admin.setSemester', ['id' => $sem->id]) }}" class="bi bi-three-dots-vertical"></a>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>No semester records found.</p>
                @endif
            </div>



            <div class="modal fade" id="addSem" tabindex="-1" aria-labelledby="addSemLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="addSemForm" method="POST" action="{{ route('addpayment.semStore') }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addSemLabel">Add Semester and Academic Year</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester</label>
                                    <select class="form-select" id="semester" name="semester">
                                        <option value="First Semester">First Semester</option>
                                        <option value="Second Semester">Second Semester</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="academic-year-from" class="form-label">Academic Year</label>
                                    <div class="d-flex gap-2">
                                        <input type="number" class="form-control" id="academic-year-from" name="academic_year_from" placeholder="From" min="2000" max="2100">
                                        <input type="number" class="form-control" id="academic-year-to" name="academic_year_to" placeholder="To" min="2000" max="2100">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
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


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.querySelector('.toggle-edit-password');
            const passwordField = document.getElementById('edit-password');

            togglePassword.addEventListener('click', function () {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);

                // Toggle eye icon
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
        });
    </script>
</body>
</html>