<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Submitted</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/scss/application-submitted.scss'])
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
            <a href="#"><span>🏠</span> Home</a>
            <a href="#"><span>🔍</span> Job Search</a>
            <a href="#"><span>💼</span> Jobs Feed</a>
            <a href="#"><span>🔔</span> Notifications <span class="badge">3</span></a>
            <a href="#"><span>👤</span> Profile</a>
            <a href="#"><span>🚪</span> Logout</a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="content">
        <div class="submitted-card">
            <div class="check-icon">
                ✓
            </div>

            <h1>Application Submitted!</h1>
            <p class="subtitle">
                Your application for <strong>Marketing Intern</strong> at
                <strong>TechCorp Solutions</strong> has been successfully submitted.
            </p>

            <div class="next-steps">
                <h3>What happens next?</h3>

                <ul>
                    <li>
                        <span class="step">1</span>
                        The employer will review your application within 3–5 business days
                    </li>
                    <li>
                        <span class="step">2</span>
                        You'll receive an email notification about your application status
                    </li>
                    <li>
                        <span class="step">3</span>
                        If selected, you'll be contacted for an interview
                    </li>
                </ul>
            </div>

            <div class="actions">
                <a href="#" class="btn primary">Back to Job Search</a>
                <a href="#" class="btn outline">View My Applications</a>
            </div>
        </div>

        <div class="toast">
            <strong>✔ Application submitted successfully!</strong>
            <p>Your application for Marketing Intern has been sent.</p>
        </div>
    </main>
</div>

</body>
</html>
