<link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
<link rel="stylesheet" href="{{ asset('css/sidenav.css') }}">


<div class="sidebar p-4">
    <img src="{{ asset('MemberPay.png') }}" alt="MemberPay Logo">
    <ul class="nav flex-column">
        <li class="nav-item mb-3">
            <a class="nav-link d-flex align-items-center text-decoration-none {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                href="{{ route('admin.dashboard') }}">
                <i class="bi bi-house-door me-2"></i> <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item mb-3">
            <a class="nav-link d-flex align-items-center text-decoration-none {{ request()->routeIs('admin.addpayment') ? 'active' : '' }}" 
                href="{{ route('admin.addpayment') }}"> 
                <i class="bi bi-cash me-2"></i> <span>Add Payment</span>
            </a>
        </li>
        <li class="nav-item mb-3">
            <a class="nav-link d-flex align-items-center text-decoration-none text-decoration-none {{ request()->routeIs('admin.members') ? 'active' : '' }}" 
                href="{{ route('admin.members') }}">
                
                <i class="bi bi-people me-2"></i> <span>Members</span>
            </a>
        </li>
        <li class="nav-item mb-3">
            <a class="nav-link d-flex align-items-center text-decoration-none text-decoration-none {{ request()->routeIs('admin.paymenthistory') ? 'active' : '' }}" 
                href="{{ route('admin.paymenthistory') }}">
                <i class="bi bi-file-earmark-text me-2"></i> <span>Payment History</span>
            </a>
        </li>
        <li class="nav-item">
            {{-- <a href="{{ route('logout') }}" class="nav-link d-flex align-items-center">
                <i class="bi bi-box-arrow-right me-2"></i> <span>Log Out</span>
            </a> --}}
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="nav-link d-flex align-items-center" >
                    <i class="bi bi-box-arrow-right me-2"></i> <span>Log Out</span>
                </button>
            </form>
        </li>
        <li class="nav-item mb-3">
            <button class="nav-link toggle-btn">
                <i class="bi bi-list me-2"></i>
            </button>
        </li>
    </ul>
</div>
