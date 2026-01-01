<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HR Dashboard — ROC.ph</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark border-end" id="sidebar-wrapper" style="width: 250px; min-height: 100vh;">
            <div class="sidebar-heading border-bottom bg-dark text-white p-3 text-center">
                <span class="fs-4 fw-bold text-warning">ROC.ph HR</span>
            </div>
            <div class="list-group list-group-flush p-2">
                <a href="{{ route('hr.dashboard') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded {{ request()->routeIs('hr.dashboard') ? 'active' : '' }} mb-1">
                    <i class="bi bi-house me-2"></i> Dashboard
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded mb-1">
                    <i class="bi bi-people me-2"></i> Interns
                </a>
                <a href="{{ route('hr.job-postings.index') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded {{ request()->routeIs('hr.job-postings.*') ? 'active' : '' }} mb-1">
                    <i class="bi bi-megaphone me-2"></i> Job Postings
                </a>
                <a href="{{ route('hr.users.index') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded {{ request()->routeIs('hr.users.*') ? 'active' : '' }} mb-1">
                    <i class="bi bi-person-badge me-2"></i> Manage Users
                </a>
            </div>
            <div class="mt-auto p-3 border-top border-secondary">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper" class="w-100">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 py-3">
                <div class="container-fluid">
                    <h2 class="fs-4 m-0 text-dark">@yield('header', 'Dashboard')</h2>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0 align-items-center">
                            <li class="nav-item">
                                <span class="text-secondary me-2">Welcome, {{ Auth::user()->first_name }}</span>
                            </li>
                            <li class="nav-item">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px;">
                                    {{ substr(Auth::user()->first_name, 0, 1) }}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>