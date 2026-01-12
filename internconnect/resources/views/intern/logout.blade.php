
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Logout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/scss/logout-confirm.scss'])
</head>
<body>

<div class="dashboard">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="logo">
            <div class="logo-icon">R</div>
            <div>
                <strong>ROC.PH</strong>
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
            <a class="active" href="#">Home</a>
            <a href="#">Job Search</a>
            <a href="#">Jobs Feed</a>
            <a href="#">Notifications <span class="badge">3</span></a>
            <a href="#">Profile</a>
            <a href="#">Logout</a>
        </nav>
    </aside>

    <!-- CONTENT (BLURRED) -->
    <main class="content">
        <div class="overlay"></div>

        <!-- MODAL -->
        <div class="modal">
            <div class="modal-header">
                <div class="icon">↪</div>
                <h3>Confirm Logout</h3>
                <span class="close">×</span>
            </div>

            <p class="message">
                Are you sure you want to log out?
            </p>
            <p class="subtext">
                You'll need to sign in again to access your dashboard.
            </p>

            <div class="actions">
                <a href="#" class="btn cancel">Cancel</a>

                <form method="POST" action="{{ route('logout.perform') }}">
                    @csrf
                    <button type="submit" class="btn danger">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>

</body>
</html>

