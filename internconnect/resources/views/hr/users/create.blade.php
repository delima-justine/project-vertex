@extends('layouts.hr')

@section('header', 'Create User')

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ route('hr.users.index') }}" class="btn" style="background:#e2e8f0; color:#475569">Back</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 max-w-2xl">
        <form method="post" action="{{ route('hr.users.store') }}">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 font-medium text-gray-700">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block mb-1 font-medium text-gray-700">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full p-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Role</label>
                    <select name="role" class="w-full p-2 border border-gray-300 rounded-lg">
                        <option value="admin">Admin</option>
                        <option value="hr">HR</option>
                        <option value="coordinator">Coordinator</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Password</label>
                    <input type="password" name="password" class="w-full p-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block mb-1 font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full p-2 border border-gray-300 rounded-lg">
                </div>
                <div class="pt-4 text-right">
                    <button class="btn btn-primary">Create User</button>
                </div>
            </div>
        </form>
    </div>

@endsection
