<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ROC.PH Intern Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/sass/intern.dashboard.scss'])
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="logo">
            <span class="badge">R</span>
            <h2>ROC.PH</h2>
            <p>Intern Portal</p>
        </div>

        <div class="user">
            <div class="avatar">JD</div>
            <div>
                <strong>John Doe</strong>
                <small>Intern</small>
            </div>
        </div>

        <nav class="menu">
            <a href="/intern/dashboard" class="active">Home</a>
            <a href="/intern/job.search">Job Search</a>
            {{-- <a href="">Jobs Feed</a> --}}
            {{-- <a href=>Notifications <span class="notif">3</span></a> --}}
            <a href="/intern/profile">Profile</a>
            <a class="logout">Logout</a>
        </nav>
    </aside>

    <main class="content">
        <section class="welcome">
            <h1>Welcome back, Intern!</h1>
            <p>You have 3 upcoming tasks and 5 applications in review.</p>
        </section>

        <section class="stats">
            <div class="card blue">
                <span>12</span>
                <p>Applications</p>
            </div>
            <div class="card yellow">
                <span>5</span>
                <p>In Review</p>
            </div>
            <div class="card green">
                <span>3</span>
                <p>Interviews</p>
            </div>
            <div class="card red">
                <span>1</span>
                <p>Offers</p>
            </div>
        </section>

        <section class="lower">
            <div class="panel">
                <h3>Recent Activity</h3>
                <ul>
                    <li>Applied to Marketing Intern at Tech Corp <small>2 hours ago</small></li>
                    <li>Profile viewed by Digital Agency <small>5 hours ago</small></li>
                    <li>Interview scheduled with StartupXYZ <small>1 day ago</small></li>
                    <li>Application updated for Social Media Role <small>2 days ago</small></li>
                </ul>
            </div>

            <div class="panel">
                <h3>Upcoming Tasks</h3>
                <div class="task high">
                    Interview with StartupXYZ
                    <small>Tomorrow, 2:00 PM</small>
                </div>
                <div class="task medium">
                    Submit portfolio for TechCorp
                    <small>Dec 10, 5:00 PM</small>
                </div>
                <div class="task low">
                    Follow up with Digital Agency
                    <small>Dec 12</small>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>

