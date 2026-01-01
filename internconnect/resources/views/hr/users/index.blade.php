@extends('layouts.hr')

@section('header', 'Manage Users')

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ route('hr.users.create') }}" class="btn btn-primary">Create User</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div style="padding:12px 18px; display:flex; justify-content:space-between; align-items:center">
            <form method="get" style="margin:0">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search" style="padding:8px;border-radius:6px;border:1px solid #e5e7eb">
                <button class="btn" style="background:#ececec">Search</button>
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
                            <a href="{{ route('hr.users.edit', $user) }}" class="btn" style="background:#f3f4f6">Edit</a>
                            <form action="{{ route('hr.users.destroy', $user) }}" method="post" style="display:inline" onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
