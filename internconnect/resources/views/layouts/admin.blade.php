<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/build/assets/app.css">
    <style>
        body { font-family: Inter, Arial, sans-serif; background:#f6f8fa; margin:0 }
        .app { display:flex; min-height:100vh }
        .sidebar { width:260px; background:#0f3b53; color:#fff; padding:20px; box-sizing:border-box }
        .brand { font-weight:700; margin-bottom:20px }
        .nav a { display:block; color:rgba(255,255,255,0.9); padding:10px 12px; margin-bottom:6px; border-radius:6px; text-decoration:none }
        .nav a.active { background:#0bb0ff; color:#063241 }
        .content { flex:1; padding:28px }
        .card-wrap { display:flex; gap:16px; margin-bottom:18px; flex-wrap:wrap }
        .card { background:#fff; padding:18px; border-radius:10px; box-shadow:0 1px 0 rgba(10,20,30,0.04); flex:1; min-width:180px }
        .table { width:100%; background:#fff; border-radius:10px; padding:0; overflow:hidden }
        .table table { width:100%; border-collapse:collapse }
        .table th, .table td { padding:12px 16px; border-bottom:1px solid #f1f3f5 }
        .btn { display:inline-block; padding:8px 12px; border-radius:6px; text-decoration:none }
        .btn-primary { background:#0bb0ff; color:#063241 }
        .btn-danger { background:#ff6b6b; color:#fff }
        .actions a { margin-right:8px }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">ROC.PH<br/><small style="font-weight:400">HR Portal</small></div>
        <div style="margin-bottom:14px">Sarah Johnson<br/><small style="opacity:.8">HR Manager</small></div>
        <nav class="nav">
            <a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">Dashboard</a>
            <a href="/admin/job-postings" class="{{ request()->is('admin/job-postings*') ? 'active' : '' }}">Job Postings</a>
            <a href="/admin/applications" class="{{ request()->is('admin/applications*') ? 'active' : '' }}">Applications</a>
            <a href="/admin/users" class="{{ request()->is('admin/users*') ? 'active' : '' }}">Manage Users</a>
            <a href="#">Manage Interns</a>
            <a href="#">Progress Tracking</a>
        </nav>
        <div style="position:fixed; bottom:20px; left:20px"><a href="/logout" style="color:#fff;text-decoration:none">Logout</a></div>
    </aside>
    <main class="content">
        @yield('content')
    </main>
 </div>
</body>
</html>
