<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Admin - Students</title>

        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/sidenav.css') }}">
        <link rel="stylesheet" href="{{ asset('css/table.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
        <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    </head>
    <body>
        <div class="d-flex">
        @include('admin.navadmin')

            <!-- Main content -->
            <div class="main-content flex-grow-1 p-4">
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
                <h2 class="fw-bold text-warning mb-4">Hello, {{ $organization }} Admin</h2>
                <div class="line"></div>
                <br>
                <div>
                    <h3 class="fw-bold org-title mb-1 text-primary"> Members List </h3>
                </div>
                <!-- Add Student Button -->
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="bi bi-person-plus"></i> Add Student
                    </button>
                </div>
                @php
                    function ordinal($number) {
                        if (!in_array(($number % 100), [11, 12, 13])) {
                            return match ($number % 10) {
                                1 => $number . 'st',
                                2 => $number . 'nd',
                                3 => $number . 'rd',
                                default => $number . 'th',
                            };
                        }
                        return $number . 'th';
                    }
                @endphp
                <!-- Filter Form -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <!-- Filter Dropdown -->
                    <form method="GET" action="{{ route('admin.members') }}">
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
                <table id="studentsTable" class="table table-responsive table-striped table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Student Id</th>
                            <th>Name</th>
                           
                            <th>Contact Number</th>
                            <th>Year Level</th>
                            <th>Section</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr>
                                <td>{{ $student->id_number }}</td>
                                <td>{{ $student->last_name }}, {{ $student->first_name }}</td>    
                             
                                <td>{{ $student->contact_number }}</td>
                                <td> Year {{ $student->year_level }}</td>
                                <td>{{ $student->section }}</td>
                                {{-- <td>{{$student->status}}</td> --}}
                                <td>
                                    @if ($student->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editStudentModal{{ $student->id }}">Edit</button>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#transferModal{{ $student->id }}">Transfer</button>
                                        <form method="POST" action="{{ route('admin.students.toggleStatus', $student->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $student->status === 'active' ? 'btn-danger' : 'btn-success' }}">
                                                {{ $student->status === 'active' ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>


                            </tr>
                            <!-- Edit Modal -->
                            <div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('admin.students.update', $student->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Student</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-2">
                                                    <label class="form-label fw-semibold">First Name</label>
                                                    <input name="first_name" maxlength="20" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" class="form-control @error('first_name') is-invalid @enderror"
                                                        value="{{ old('first_name', $student->first_name) }}" required>
                                                    @if(session('editing_student_id') == $student->id)
                                                        @error('first_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    @endif
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label fw-semibold">Last Name</label>
                                                    <input name="last_name" maxlength="20" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" class="form-control @error('last_name') is-invalid @enderror"
                                                        value="{{ old('last_name', $student->last_name) }}" required>
                                                    @if(session('editing_student_id') == $student->id)
                                                        @error('last_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    @endif
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label fw-semibold">Contact Number</label>
                                                    <input name="contact_number"  maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control @error('contact_number') is-invalid @enderror"
                                                        value="{{ old('contact_number', $student->contact_number) }}" required>
                                                    @if(session('editing_student_id') == $student->id)
                                                        @error('contact_number')
                                                            <div class="invalid-feedback">Follow the format 09*********</div>
                                                        @enderror
                                                    @endif
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label fw-semibold">ID Number</label>
                                                    <input name="id_number" maxlength="7" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control" value="{{ $student->id_number }}" required>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label fw-semibold">Year Level</label>
                                                    <select id="editYearLevel{{ $student->id }}" data-student-id="{{ $student->id }}" name="year_level" class="form-select @error('year_level') is-invalid @enderror" required>
                                                        @foreach (range(1, 4) as $level)
                                                            <option value="{{ $level }}" {{ old('year_level', $student->year_level) == $level ? 'selected' : '' }}>
                                                                {{ $level }}{{ ['st','nd','rd','th'][$level-1] ?? 'th' }} Year
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if(session('editing_student_id') == $student->id)
                                                        @error('year_level')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    @endif
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label fw-semibold">Section</label>
                                                    <select id="editSection{{ $student->id }}" name="section" class="form-select @error('section') is-invalid @enderror" required>
                                                        <option value="{{ $student->section }}">{{ $student->section }}</option>
                                                    </select>
                                                    @if(session('editing_student_id') == $student->id)
                                                        @error('section')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    @endif
                                                </div>
                                                @php
                                                    $courseOrgs = ['APSS', 'AVED', 'BACOMMUNITY', 'BPED MOVERS', 'COFED', 'DIGITS',
                                                        'EC', 'EA', 'HRC', 'JSWAP', 'KMF', 'LNU MSS', 'INTERSOC', 'TC', 'TLEG', 'SQU', 'ECEO'];

                                                    $originalOrg = $student->organizations()->whereIn('code', $courseOrgs)->first();
                                                    $currentOrg = $student->currentOrganization;
                                                @endphp
                                                <div class="mb-2">
                                                    <label class="form-label fw-semibold">Organization</label>
                                                    <input name="organization" class="form-control" 
                                                        value="{{ $currentOrg->name ?? $originalOrg->name ?? 'No organization' }}"
                                                        readonly required>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Transfer Modal -->
                            <div class="modal fade" id="transferModal{{ $student->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('admin.students.transfer', $student->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Transfer Student</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                         <div class="modal-body">
                                            <label class="form-label fw-semibold">Select Organization</label>
                                            <select name="organization" class="form-select" required>
                                                <option value="">-- Choose Organization --</option>
                                                @foreach($allOrganizations as $org)
                                                    @if(!in_array($org, ['FCO', 'JCO', 'SCO', 'SENCO']))
                                                        <option value="{{ $org }}" {{ $student->organization == $org ? 'selected' : '' }}>
                                                            {{ $org }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-warning">Transfer</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No students found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- Add Student Modal -->
                <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('admin.students.store') }}">
                            @csrf
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold ">First Name</label>
                                        <input type="text" name="first_name" maxlength="20" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}"  required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold ">Last Name</label>
                                        <input type="text" name="last_name" maxlength="20" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold ">Contact Number</label>
                                        <input type="text" name="contact_number" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number') }}" required>
                                        @error('contact_number')
                                            <div class="invalid-feedback">Follow the format 09*********</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold ">ID Number</label>
                                        <input type="text" name="id_number" maxlength="7" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control @error('id_number') is-invalid @enderror" value="{{ old('id_number') }}" required>
                                        @error('id_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold ">Year Level</label>
                                        <select name="year_level" id="year_level" class="form-select @error('year_label') is-invalid @enderror" value="{{ old('year_label') }}" required>
                                            <option value="">-- Select Year --</option>
                                            @foreach (range(1,4) as $level)
                                                <option value="{{ $level }}">{{ ordinal($level) }} Year</option>
                                            @endforeach
                                        </select>
                                        @error('year_label')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold ">Section</label>
                                        <select name="section" id="section" class="form-select @error('section') is-invalid @enderror" required>
                                            <option value="">-- Select Section --</option>
                                        </select>
                                        @error('section')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Organization</label>
                                        <input type="text" class="form-control" value="{{ $organization }}" readonly>
                                        <input type="hidden" name="organization" value="{{ $organization }}">
                                    </div>
                                </div>
                                <div class="modal-footer d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-success">Save Student</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
                const sectionDropdown = document.getElementById('sectionDropdown');
                sectionDropdown.addEventListener('change', function () {
                    this.form.submit(); // Automatically submit the form on change
                });
            });
        </script>
        <script>
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    new bootstrap.Alert(alert).close();
                });
            }, 5000); // Close after 5 seconds
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
                        { orderable: false, targets: 5 } // Disable sorting on "Action" column
                    ],
                    initComplete: function () {
                        const searchBox = $('#studentsTable_filter');
                        searchBox.addClass('ms-auto'); // Optional: aligns to the right
                        $('.d-flex.justify-content-between').append(searchBox);
                    }
                });
            });
        </script>

        @if ($errors->any() && session('editing_student_id'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var editModal = new bootstrap.Modal(document.getElementById('editStudentModal{{ session('editing_student_id') }}'));
                editModal.show();
            });
        </script>
        @endif

        @if ($errors->any() && session('showAddModal'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var addModal = new bootstrap.Modal(document.getElementById('addStudentModal'));
                addModal.show();
            });
        </script>
        @endif

        <script>
            document.getElementById('year_level').addEventListener('change', function () {
                const yearLevel = this.value;
                const organization = "{{ $organization }}";

                if (yearLevel) {
                    fetch(`/api/sections?year_level=${yearLevel}&organization=${organization}`)
                        .then(response => response.json())
                        .then(data => {
                            const sectionSelect = document.getElementById('section');
                            sectionSelect.innerHTML = '';
                            data.forEach(section => {
                                const option = document.createElement('option');
                                option.value = section.section;
                                option.textContent = section.section;
                                sectionSelect.appendChild(option);
                            });
                        });
                }
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Attach event listeners to all year_level dropdowns in edit modals
                document.querySelectorAll('[id^="editYearLevel"]').forEach(function (yearLevelDropdown) {
                    yearLevelDropdown.addEventListener('change', function () {
                        const yearLevel = this.value;
                        const studentId = this.dataset.studentId; // Get the student ID from a data attribute
                        const organization = "{{ $organization }}";

                        if (yearLevel) {
                            fetch(`/api/sections?year_level=${yearLevel}&organization=${organization}`)
                                .then(response => response.json())
                                .then(data => {
                                    const sectionSelect = document.getElementById(`editSection${studentId}`);
                                    sectionSelect.innerHTML = '';
                                    data.forEach(section => {
                                        const option = document.createElement('option');
                                        option.value = section.section;
                                        option.textContent = section.section;
                                        sectionSelect.appendChild(option);
                                    });
                                });
                        }
                    });
                });
            });
        </script>


    </body>
</html>
