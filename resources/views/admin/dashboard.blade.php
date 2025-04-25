<!-- resources/views/admin/dashboard.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard</title>
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
                    <div class="card h-100 d-flex flex-column">
                        <div class="card-body d-flex flex-column justify-content-end">
                            <h1 class="text text-white">{{ $totalMembers }}</h1>
                            <h6 class="card-text text-white p-2 mt-auto">Total Members</h6>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="card h-100 d-flex flex-column">
                        <div class="card-body d-flex flex-column justify-content-end">
                            <h1 class="text text-white">{{ $totalPaid }}</h1>
                            <h6 class="card-text text-white p-2 mt-auto">Total Paid</h6>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="card h-100 d-flex flex-column">
                        <div class="card-body d-flex flex-column justify-content-end">
                            <h1 class="text text-white">{{ $totalUnpaid }}</h1>
                            <h6 class="card-text text-white p-2 mt-auto">Total Unpaid</h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h4 class="card-title p-2 mb-3">Recent Payment Transactions</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Student ID</th>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Section</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTransactions as $transaction)
                            <tr>
                                <td>{{ $transaction->student_id }}</td>
                                <td>{{ $transaction->first_name }}</td>
                                <td>{{ $transaction->last_name }}</td>
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
