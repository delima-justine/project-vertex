
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile | ROC.PH</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/sass/intern.profile.scss'])
</head>
<body>

<div class="app">

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
            <div class="avatar">JD</div>
            <div>
                <strong>John Doe</strong>
                <small>Intern</small>
            </div>
        </div>

        <nav>
            <a href="{{ route('intern.dashboard') }}">Home</a>
            <a href="{{ route('intern.job.search') }}">Job Search</a>
            <a href="#">Jobs Feed</a>
            <a href="#">Notifications <span class="notif">3</span></a>
            <a class="active" href="{{ route('intern.profile') }}">Profile</a>
            <a class="logout" href="#">Logout</a>
        </nav>
    </aside>

    {{--  MAIN  --}}
    <main class="main">

        {{-- PROFILE HEADER --}}
        <section class="profile-header">
            <div>
                <h3>Profile Management</h3>
                <p>Manage your profile and showcase your skills</p>
            </div>
            <button class="edit-btn">Edit Profile</button>
        </section>

        <section class="cover">
            <div class="avatar-large"></div>
            <div class="info">
                <h2>John Doe</h2>
                <p>📍 Manila, Philippines &nbsp; ✉ john.doe@example.com &nbsp; ☎ +63 912 345 6789</p>
            </div>
        </section>

        {{-- ABOUT --}}
        <section class="card">
            <h3>About Me</h3>
            <p>
                Passionate marketing student seeking internship opportunities to apply my skills
                in social media management, content creation, and digital marketing.
            </p>

            <div class="links">
                <a>linkedin.com/in/johndoe</a>
                <a>github.com/johndoe</a>
                <a>johndoe.com</a>
            </div>
        </section>

        {{-- SKILLS --}}
        <section class="grid">

            {{-- SKILLS --}}
            <div class="card">
                <h3>Skills</h3>

                <div class="skill">
                    <span>Social Media Marketing</span><span>85%</span>
                    <div class="bar"><div style="width:85%"></div></div>
                </div>

                <div class="skill">
                    <span>Content Creation</span><span>90%</span>
                    <div class="bar"><div style="width:90%"></div></div>
                </div>

                <div class="skill">
                    <span>SEO & Analytics</span><span>75%</span>
                    <div class="bar"><div style="width:75%"></div></div>
                </div>

                <div class="skill">
                    <span>Copywriting</span><span>80%</span>
                    <div class="bar"><div style="width:80%"></div></div>
                </div>

                <div class="skill">
                    <span>Email Marketing</span><span>70%</span>
                    <div class="bar"><div style="width:70%"></div></div>
                </div>
            </div>

            {{-- EXPERIENCE --}}
            <div class="card">
                <h3>Experience</h3>

                <div class="exp">
                    <strong>Marketing Volunteer</strong>
                    <span class="org">Local Non-Profit Organization</span>
                    <small>Jan 2024 – Present</small>
                    <p>Managing social media accounts and creating engaging content</p>
                </div>

                <div class="exp">
                    <strong>Content Creator</strong>
                    <span class="org">University Marketing Club</span>
                    <small>Sep 2023 – Dec 2023</small>
                    <p>Produced marketing materials for campus events</p>
                </div>
            </div>

        </section>
    </main>
</div>

</body>
</html>
