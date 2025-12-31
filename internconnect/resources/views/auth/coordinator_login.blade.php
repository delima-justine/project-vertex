<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coordinator Login — ROC.ph</title>
    @vite(['resources/css/landing.css','resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-gray-50 h-screen flex flex-col">
    
    <!-- Navbar (Simplified for Auth) -->
    <header class="nav-bg text-white shadow-md">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-2xl font-semibold text-yellow-400 hover:text-yellow-300 transition">ROC.ph</a>
            <a href="/" class="text-sm text-gray-200 hover:text-white transition"><i class="bi bi-arrow-left"></i> Back to Home</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center hero-gradient p-7">
        
        <div class="w-full max-w-md bg-white rounded-2xl card-shadow p-8 md:p-10">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-50 text-blue-600 rounded-full mb-4">
                    <span class="text-2xl"><i class="bi bi-mortarboard"></i></span>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Coordinator Portal</h1>
                <p class="text-gray-500">Sign in to manage your student interns</p>
            </div>

            <form action="{{ route('coordinator.login.post') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-200 outline-none transition duration-200"
                        placeholder="coordinator@university.edu.ph" 
                        required 
                        value="{{ old('email') }}"
                        autofocus>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-200 outline-none transition duration-200"
                        placeholder="••••••••" 
                        required>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-400">
                        <span class="ml-2">Remember me</span>
                    </label>
                    <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Forgot password?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full btn-yellow text-lg font-semibold hover:bg-yellow-300 transition duration-200 flex justify-center items-center">
                    Sign In
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-100 text-center text-sm text-gray-500">
                <p>&copy; 2025 ROC.ph. OJT Management System.</p>
            </div>

        </div>

    </main>

</body>
</html>
