<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coordinator Dashboard — ROC.ph</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📈</text></svg>">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('head')
    @stack('styles')
    @yield('styles')
    </head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark border-end" id="sidebar-wrapper" style="width: 250px; min-height: 100vh;">
            <div class="sidebar-heading border-bottom bg-dark text-white p-3 text-center">
                <span class="fs-4 fw-bold text-warning">ROC.ph Coordinator</span>
            </div>
            
            <div class="d-flex align-items-center gap-3 p-3 text-white">
                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width: 40px; height: 40px;">
                    {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
                </div>
                <div>
                    <div class="fw-semibold">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                    <small class="opacity-75">{{ Auth::user()->user_role }}</small>
                </div>
            </div>
            
            <div class="list-group list-group-flush p-2">
                <a href="{{ route('coordinator.dashboard') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded {{ request()->routeIs('coordinator.dashboard') ? 'active' : '' }} mb-1">
                    <i class="bi bi-house me-2"></i> Dashboard
                </a>
                <a href="{{ route('coordinator.monitor-interns') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded {{ request()->routeIs('coordinator.monitor-interns') ? 'active' : '' }} mb-1">
                    <i class="bi bi-people me-2"></i> Monitor Interns
                </a>
                <a href="{{ route('coordinator.support-docs') }}" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded {{ request()->routeIs('coordinator.support-docs') ? 'active' : '' }} mb-1">
                    <i class="bi bi-folder2 me-2"></i> Support Documents
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded mb-1">
                    <i class="bi bi-gear me-2"></i> Settings
                </a>
            </div>
            <div class="mt-auto p-3 border-top border-secondary">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button 
                      type="submit" 
                      class="btn btn-outline-danger w-100 d-flex align-items-center 
                        justify-content-center">
                          <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper" class="w-100">
            <nav 
              class="navbar navbar-expand-lg navbar-light 
                bg-white border-bottom shadow-sm px-4 py-3">
                <div class="container-fluid">
                  <h2 
                  class="fs-4 m-0 text-dark">
                    @yield('header', 'Dashboard')
                  </h2>
                </div>
            </nav>

            <div class="container-fluid p-4">
                @if(session('success'))
                    <div 
                      class="alert alert-success alert-dismissible fade show" 
                      role="alert">
                        {{ session('success') }}
                        <button 
                          type="button" 
                          class="btn-close" 
                          data-bs-dismiss="alert" 
                          aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
