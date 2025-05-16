<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" 
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/admin.css') }}">
</head>
<body>
    @include('admin.navadmin')

    <div class="d-flex">
        <div class="main-content flex-grow-1 p-4">   
            <div class="main-container">
                <h1 class="fw-bold text-warning mb-4">{{ $organization }} ORGANIZATION</h1>
                <div class="line"></div>
                <br>
            </div>       
            <div class="row">
                <div class="card mt-4">
                    <div class="card-header bg-warning text-white fw-bold">
                        Treasurer History
                    </div>
                    
                        <table class="table table-striped table-bordered align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Treasurer Name</th>
                                   
                                    <th>Semesters Created</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach($treasurers as $index => $treasurer)
                                    @php
                                        $semesters = $treasurer->semesters;
                                        $groupedByYear = $semesters->groupBy('academic_year');
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $treasurer->name }}</td>
                                      
                                        <td>
                                            @if($semesters->count())
                                                @foreach($groupedByYear as $year => $semInYear)
                                                    <div class="mb-2">
                                                        <strong>Academic Year: {{ $year }}</strong>
                                                        <ul class="mb-1">
                                                            @foreach($semInYear as $sem)
                                                                <li>
                                                                    {{ $sem->semester }}
                                                                    <span class="text-muted">(Created: {{ \Carbon\Carbon::parse($sem->created_at)->format('M d, Y') }})</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No semesters</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>     
        </div>
    </div> 
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Enable Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        });

        const toggleBtn = document.querySelector('.toggle-btn');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');

        // Restore sidebar state from localStorage
        const isSidebarOpen = localStorage.getItem('sidebarOpen') === 'true';
        if (isSidebarOpen) {
            sidebar?.classList.add('open');
            mainContent?.classList.add('shifted');
        }

        // Toggle sidebar and update localStorage
        toggleBtn?.addEventListener('click', function () {
            sidebar?.classList.toggle('open');
            mainContent?.classList.toggle('shifted');
            localStorage.setItem('sidebarOpen', sidebar?.classList.contains('open'));
        });
    });
</script>
</html>
