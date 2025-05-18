<!-- resources/views/admin/dashboard.blade.php -->

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
                <h1 class="fw-bold text-warning mb-4">{{ $organization }} ORGANIZATION</h2>
                <div class="line"></div>
                <br>
            </div>       
           
            <div class="row">
                <div class="col mb-3">
                    <div class="card h-100 position-relative overflow-hidden text-white">
                        <div class="card-body d-flex flex-column justify-content-end position-relative" style="z-index: 2;">
                            <h1 class="text">{{ $totalMembers }}</h1>
                            <h6 class="card-text p-2 mt-auto">Total Members</h6>
                        </div>
                        <i class="cardIcon fa-solid fa-users     position-absolute text-white-50"></i>
                    </div>

                </div>
                <div class="col mb-3">
                    <div class="card h-100 position-relative overflow-hidden text-white">
                        <div class="card-body d-flex flex-column justify-content-end position-relative" style="z-index: 2;">
                            <h1 class="text text-white">{{ $totalPaid }}</h1>
                            <h6 class="card-text text-white p-2 mt-auto">Total Paid</h6>
                        </div>
                        <i class="cardIcon fa-solid fa-money-bills position-absolute text-white-50"></i>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="card h-100 position-relative overflow-hidden text-white">
                        <div class="card-body d-flex flex-column justify-content-end position-relative" style="z-index: 2;">
                            <h1 class="text text-white">{{ $totalUnpaid }}</h1>
                            <h6 class="card-text text-white p-2 mt-auto">Total Unpaid</h6>
                        </div>
                        <i class="cardIcon fa-solid fa-triangle-exclamation position-absolute text-white-50"></i>
                    </div>
                </div>
            </div>

            <div class="card">
                <h4 class="card-title p-2 mb-3">Recent Payment Transactions</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Student ID</th>
                           
                            <th scope="col">Last Name</th>
                             <th scope="col">First Name</th>
                            <th scope="col">Section</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTransactions as $transaction)
                            <tr>
                                <td>{{ $transaction->student_id }}</td>
                               
                                <td>{{ $transaction->last_name }}</td>
                                 <td>{{ $transaction->first_name }}</td>
                                <td>{{ $transaction->section }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No recent transactions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card mt-4 h-50">
                <div class="card-body">
                    <h4 class="card-title">Paid Students by Semester</h4>
                    <div class="position-relative" style="height: 300px;">
                        <canvas id="paidChart"></canvas>
                    </div>
                </div>
            </div>
            <hr>

            <!-- <div class="card mt-4 h-50">
                <div class="card-body">
                    <h4 class="card-title">Membership Payment Overview</h4>
                    <div class="position-relative mt-4" style="height: 300px;">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div> -->


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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('paymentChart').getContext('2d');
        const paymentChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Paid', 'Unpaid'],
                datasets: [{
                    label: 'Members',
                    data: [{{ $totalPaid }}, {{ $totalUnpaid }}],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderColor: ['#ffffff', '#ffffff'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Payment Distribution'
                    }
                }
            }
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('paidChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [
                {
                    label: 'Paid',
                    data: {!! json_encode($paidData) !!},
                    backgroundColor: '#0d6efd',
                    stack: 'members'
                },
                {
                    label: 'Unpaid',
                    data: {!! json_encode($totalData->map(fn($total, $i) => $total - $paidData[$i])) !!},
                    backgroundColor: '#b0c4de',
                    stack: 'members'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Total Paid vs Total Unpaid Students per Semester'
                },
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                x: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'Semesters'
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Students'
                    }
                }
            }
        }
    });
});
</script>



</html>
