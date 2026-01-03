<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Applicant Login — ROC.ph</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    
    <!-- Navbar (Simplified for Auth) -->
    <header class="nav-bg text-white shadow-sm py-3">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="/" class="fs-4 fw-bold text-warning text-decoration-none">ROC.ph</a>
            <a href="/" class="text-white text-decoration-none small hover-underline"><i class="bi bi-arrow-left me-1"></i> Back to Home</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow-1 d-flex align-items-center justify-content-center hero-gradient p-4">
        
        <div class="card shadow-lg border-0 rounded-4" style="max-width: 450px; width: 100%;">
            <div class="card-body p-5">
                
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-3" style="width: 64px; height: 64px;">
                        <span class="fs-2"><i class="bi bi-person"></i></span>
                    </div>
                    <h1 class="h4 fw-bold text-dark mb-1">Applicant Portal</h1>
                    <p class="text-muted small">Sign in to track your application</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div class="small">{{ session('success') }}</div>
                    </div>
                @endif

                <form action="{{ route('applicant.login.post') }}" method="POST">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small text-secondary">Email Address</label>
                        <input type="email" name="email" id="email" 
                            class="form-control form-control-lg fs-6"
                            placeholder="you@example.com" 
                            required 
                            value="{{ old('email') }}"
                            autofocus>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold small text-secondary">Password</label>
                        <input type="password" name="password" id="password" 
                            class="form-control form-control-lg fs-6"
                            placeholder="••••••••" 
                            required>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="d-flex justify-content-between align-items-center mb-4 small">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-secondary" for="remember">
                                Remember me
                            </label>
                        </div>
                        <a href="#" class="text-decoration-none text-success fw-bold">Forgot password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-warning w-100 fw-bold py-2">
                        Sign In
                    </button>
                </form>

                <div class="mt-4 text-center small">
                    <span class="text-muted">Don't have an account?</span> 
                    <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none">Register here</a>
                </div>

                <!-- Footer -->
                <div class="mt-4 pt-4 border-top text-center">
                    <p class="text-muted small mb-0">&copy; 2025 ROC.ph. Careers.</p>
                </div>

            </div>
        </div>

    </main>

</body>
</html>