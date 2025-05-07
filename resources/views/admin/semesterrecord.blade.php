<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Semester Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
</head>
<body>
    @include('admin.navadmin')

    <div class="d-flex">
        <div class="main-content flex-grow-1 p-4">
            <div class="main-container">
                <div class="row">
                    <div class="col-10">
                    <h1 class="fw-bold text-warning mb-4">{{ $organization }} ORGANIZATION</h1>
                    </div>
                    <div class="col-2">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('admin.addpayment') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                    </div>
                </div>


                <div class="line"></div>
                @if ($currentSemester)
                <h1 class="fw-bold text-warning mb-4">
        {{ $currentSemester->semester }} {{ $currentSemester->academic_year }}
    </h1>
                @else
                    <h1 class="text-danger">No semester selected or found.</h1>
                @endif
                <br>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Whoops!</strong> Please fix the following errors:
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @php
                if (!function_exists('ordinal')) {
                    function ordinal($number)
                    {
                        $ends = ['th','st','nd','rd','th','th','th','th','th','th'];
                        if (($number % 100) >= 11 && ($number % 100) <= 13) {
                            return $number . 'th';
                        }
                        return $number . $ends[$number % 10];
                    }
                }
            @endphp

            <div class="d-flex justify-content-between-table align-items-center mb-3 flex-wrap gap-2">
                <!-- Filter Dropdown -->
                <form method="GET" action="{{ route('admin.semesterrecord') }}">
                    <select name="filter" class="form-select" style="min-width: 200px;" onchange="this.form.submit()">
                        <option value="">Show All</option>
                        @foreach ($groupedSections as $year => $sections)
                            <option value="year_{{ $year }}"
                                {{ request('filter') == 'year_'.$year ? 'selected' : '' }}>
                                {{ ordinal($year) }} Year
                            </option>
                            @foreach ($sections as $sec)
                                <option value="section_{{ $sec }}"
                                    {{ request('filter') == 'section_'.$sec ? 'selected' : '' }}>
                                    {{ ordinal((int) substr($sec, 2, 1)) }} Year - {{ $sec }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </form>

                <!-- DataTable Search bar is automatically included -->
            </div>


            <!-- Students Table -->
            <table id="studentsTable" class="table table-striped table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Student Id</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Section</th>
                        <th>Payment Status</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td>{{ $student->id_number }}</td>
                            <td>{{ $student->first_name }}</td>
                            <td>{{ $student->last_name }}</td>
                            <td>{{$student->section}}</td>
                            <td>
                                <form action="{{ route('admin.updatePaymentStatus') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <input type="hidden" name="semester_id" value="{{ $currentSemester->id }}">
                                    <select name="payment_status" class="form-select" style="max-width: 150px;" onchange="this.form.submit()">
                                        <option value="Paid" {{ $student->pivot->payment_status == 'Paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="Unpaid" {{ $student->pivot->payment_status == 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No students found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.querySelector('.toggle-btn');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            // Restore sidebar state from localStorage
            const isSidebarOpen = localStorage.getItem('sidebarOpen') === 'true';
            if (isSidebarOpen) {
                sidebar.classList.add('open');
                if (mainContent) mainContent.classList.add('shifted');
            }

            // Toggle sidebar and update localStorage
            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('open');
                if (mainContent) mainContent.classList.toggle('shifted');

                // Save the state
                localStorage.setItem('sidebarOpen', sidebar.classList.contains('open'));
            });

            // Select all buttons with the class 'pay-btn'
            const payButtons = document.querySelectorAll('.pay-btn');

            payButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const form = this.closest('.payment-form');
                    const hiddenStatusInput = form.querySelector('input[name="payment_status"]');

                    if (this.textContent === 'Pay') {
                        this.textContent = 'Paid';
                        this.classList.remove('btn-primary');
                        this.classList.add('btn-light');
                        hiddenStatusInput.value = 'Paid';
                    } else {
                        this.textContent = 'Pay';
                        this.classList.remove('btn-light');
                        this.classList.add('btn-primary');
                        hiddenStatusInput.value = 'Unpaid';
                    }

                    // Submit the form
                    form.submit();
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            const table = $('#studentsTable').DataTable({
                paging: true,
                ordering: true,
                searching: true,
                lengthChange: false,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                columnDefs: [
                    { orderable: false, targets: 3 } // Disable sorting on "Action" column
                ],
                initComplete: function () {
                    const searchBox = $('#studentsTable_filter');
                    searchBox.addClass('ms-auto'); // Optional: aligns to the right
                    $('.d-flex.justify-content-between-table').append(searchBox);
                }
            });
        });
    </script>
</body>
</html>
