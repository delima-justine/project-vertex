@extends('layouts.hr')

@section('header', 'Dashboard')

@section('content')
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
@endsection
