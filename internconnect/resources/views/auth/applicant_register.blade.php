<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Applicant Registration — ROC.ph</title>
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
        
        <div class="card shadow-lg border-0 rounded-4" style="max-width: 600px; width: 100%;">
            <div class="card-body p-5">
                
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-3" style="width: 64px; height: 64px;">
                        <span class="fs-2"><i class="bi bi-file-earmark-text"></i></span>
                    </div>
                    <h1 class="h4 fw-bold text-dark mb-1">Create an Account</h1>
                    <p class="text-muted small">Join our internship program today</p>
                </div>

                <form action="{{ route('applicant.register.post') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3 mb-3">
                        <!-- First Name -->
                        <div class="col-md-6">
                            <label for="first_name" class="form-label fw-bold small text-secondary">First Name</label>
                            <input type="text" name="first_name" id="first_name" 
                                class="form-control form-control-lg fs-6"
                                placeholder="John" 
                                required 
                                value="{{ old('first_name') }}"
                                autofocus>
                            @error('first_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6">
                            <label for="last_name" class="form-label fw-bold small text-secondary">Last Name</label>
                            <input type="text" name="last_name" id="last_name" 
                                class="form-control form-control-lg fs-6"
                                placeholder="Doe" 
                                required 
                                value="{{ old('last_name') }}">
                            @error('last_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small text-secondary">Email Address</label>
                        <input type="email" name="email" id="email" 
                            class="form-control form-control-lg fs-6"
                            placeholder="you@example.com" 
                            required 
                            value="{{ old('email') }}">
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Contact Number -->
                    <div class="mb-3">
                        <label for="contact_number" class="form-label fw-bold small text-secondary">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" 
                            class="form-control form-control-lg fs-6"
                            placeholder="+63 912 345 6789" 
                            value="{{ old('contact_number') }}">
                        @error('contact_number')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Password Field -->
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-bold small text-secondary">Password</label>
                            <input type="password" name="password" id="password" 
                                class="form-control form-control-lg fs-6"
                                placeholder="••••••••" 
                                required>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-bold small text-secondary">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                class="form-control form-control-lg fs-6"
                                placeholder="••••••••" 
                                required>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-warning w-100 fw-bold py-2">
                        Register
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-4 text-center small">
                    <span class="text-muted">Already have an account?</span> 
                    <a href="{{ route('applicant.login') }}" class="text-success fw-bold text-decoration-none">Sign in here</a>
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