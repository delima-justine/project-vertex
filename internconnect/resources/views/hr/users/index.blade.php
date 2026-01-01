@extends('layouts.hr')

@section('header', 'Manage Users')

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ route('hr.users.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Create User</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div style="padding:12px 18px; display:flex; justify-content:space-between; align-items:center">
            <form method="get" style="margin:0">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search" style="padding:8px;border-radius:6px;border:1px solid #e5e7eb">
                <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Search</button>
            </form>
            <div>{{ $users->links() ?? '' }}</div>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="p-4 border-b border-gray-100 font-semibold text-gray-600">ID</th>
                    <th class="p-4 border-b border-gray-100 font-semibold text-gray-600">First Name</th>
                    <th class="p-4 border-b border-gray-100 font-semibold text-gray-600">Last Name</th>
                    <th class="p-4 border-b border-gray-100 font-semibold text-gray-600">Email</th>
                    <th class="p-4 border-b border-gray-100 font-semibold text-gray-600">Role</th>
                    <th class="p-4 border-b border-gray-100 font-semibold text-gray-600">Created</th>
                    <th class="p-4 border-b border-gray-100 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 border-b border-gray-50">{{ $user->user_id }}</td>
                        <td class="p-4 border-b border-gray-50">{{ $user->first_name }}</td>
                        <td class="p-4 border-b border-gray-50">{{ $user->last_name }}</td>
                        <td class="p-4 border-b border-gray-50">{{ $user->email }}</td>
                        <td class="p-4 border-b border-gray-50">{{ $user->role }}</td>
                        <td class="p-4 border-b border-gray-50">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="p-4 border-b border-gray-50 flex gap-2">
                            <a href="{{ route('hr.users.edit', $user) }}" class="px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition text-sm">Edit</a>
                            <form action="{{ route('hr.users.destroy', $user) }}" method="post" style="display:inline" onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button class="px-3 py-1 bg-red-50 text-red-600 rounded hover:bg-red-100 transition text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
