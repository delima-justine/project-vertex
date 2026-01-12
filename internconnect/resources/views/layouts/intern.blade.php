<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'ROC.PH Intern Portal')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('styles')
</head>
<body>
<div class="app-container app">
    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="logo">
            <span class="badge">R</span>
            <div>
                <h2>ROC.PH</h2>
                <small>Intern Portal</small>
            </div>
        </div>

        <div class="user">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->first_name, 0, 1) . substr(Auth::user()->last_name, 0, 1)) }}</div>
            <div>
                <strong>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</strong>
                <small>{{ Auth::user()->user_role }}</small>
            </div>
        </div>

        <nav class="menu">
            <a class="{{ request()->routeIs('intern.dashboard') ? 'active' : '' }}" href="{{ route('intern.dashboard') }}">Home</a>
            <a class="{{ request()->routeIs('intern.job.search') ? 'active' : '' }}" href="{{ route('intern.job.search') }}">Job Search</a>
            {{-- <a href="#">Jobs Feed</a>
            <a href="#">Notifications <span class="notif">3</span></a> --}}
            <a class="{{ request()->routeIs('intern.profile') ? 'active' : '' }}" href="{{ route('intern.profile', auth()->id()) }}">Profile</a>
            <a class="logout" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="content main">
        @yield('content')
    </main>
</div>
</body>
</html>
