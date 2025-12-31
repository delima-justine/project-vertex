<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HR Dashboard — ROC.ph</title>
    @vite(['resources/css/landing.css','resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-800 text-white flex flex-col">
            <div class="h-16 flex items-center justify-center border-b border-slate-700">
                <span class="text-xl font-semibold text-yellow-400">ROC.ph HR</span>
            </div>
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1 px-2">
                    <li>
                        <a href="#" class="flex items-center px-4 py-3 bg-slate-700 rounded-lg text-white">
                            <span class="mr-3"><i class="bi bi-speedometer2"></i></span> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700 hover:text-white rounded-lg transition">
                            <span class="mr-3"><i class="bi bi-people"></i></span> Interns
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700 hover:text-white rounded-lg transition">
                            <span class="mr-3"><i class="bi bi-megaphone"></i></span> Job Postings
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700 hover:text-white rounded-lg transition">
                            <span class="mr-3"><i class="bi bi-file-earmark-text"></i></span> Applications
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="p-4 border-t border-slate-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-400 hover:text-red-300 transition">
                        <span class="mr-2"><i class="bi bi-box-arrow-right"></i></span> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
                <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
                <div class="flex items-center">
                    <span class="text-gray-600 mr-2">Welcome, {{ Auth::user()->first_name }}</span>
                    <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-white font-bold">
                        {{ substr(Auth::user()->first_name, 0, 1) }}
                    </div>
                </div>
            </header>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Stat Card 1 -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase">Total Interns</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1">12</h3>
                            </div>
                            <div class="p-2 bg-blue-50 rounded-lg text-blue-500">
                                <i class="bi bi-people text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Stat Card 2 -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase">Active Applications</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1">5</h3>
                            </div>
                            <div class="p-2 bg-green-50 rounded-lg text-green-500">
                                <i class="bi bi-file-earmark-text text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Stat Card 3 -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase">Pending Documents</p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-1">3</h3>
                            </div>
                            <div class="p-2 bg-purple-50 rounded-lg text-purple-500">
                                <i class="bi bi-folder text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Activities</h2>
                    <p class="text-gray-500">No recent activities.</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
