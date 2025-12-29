<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Applicant Registration — ROC.ph</title>
    @vite(['resources/css/landing.css','resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-gray-50 min-h-screen flex flex-col">
    
    <!-- Navbar (Simplified for Auth) -->
    <header class="nav-bg text-white shadow-md">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-2xl font-semibold text-yellow-400 hover:text-yellow-300 transition">ROC.ph</a>
            <a href="/" class="text-sm text-gray-200 hover:text-white transition">← Back to Home</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center hero-gradient px-4 py-2">
        
        <div class="w-full max-w-lg bg-white rounded-2xl card-shadow p-8 md:p-10">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-50 text-green-600 rounded-full mb-4">
                    <span class="text-2xl">📝</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Create an Account</h1>
                <p class="text-gray-500">Join our internship program today</p>
            </div>

            <form action="{{ route('applicant.register.post') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" name="first_name" id="first_name" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-400 focus:ring-2 focus:ring-green-200 outline-none transition duration-200"
                            placeholder="John" 
                            required 
                            value="{{ old('first_name') }}"
                            autofocus>
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" id="last_name" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-400 focus:ring-2 focus:ring-green-200 outline-none transition duration-200"
                            placeholder="Doe" 
                            required 
                            value="{{ old('last_name') }}">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-400 focus:ring-2 focus:ring-green-200 outline-none transition duration-200"
                        placeholder="you@example.com" 
                        required 
                        value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-400 focus:ring-2 focus:ring-green-200 outline-none transition duration-200"
                        placeholder="+63 912 345 6789" 
                        value="{{ old('contact_number') }}">
                    @error('contact_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-400 focus:ring-2 focus:ring-green-200 outline-none transition duration-200"
                        placeholder="••••••••" 
                        required>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-green-400 focus:ring-2 focus:ring-green-200 outline-none transition duration-200"
                        placeholder="••••••••" 
                        required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full btn-yellow text-lg font-semibold hover:bg-yellow-300 transition duration-200 flex justify-center items-center">
                    Register
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center text-sm">
                <p class="text-gray-600">Already have an account? <a href="{{ route('applicant.login') }}" class="text-green-600 hover:text-green-700 font-medium hover:underline">Sign in here</a></p>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-100 text-center text-sm text-gray-500">
                <p>&copy; 2025 ROC.ph. Careers.</p>
            </div>

        </div>

    </main>

</body>
</html>
