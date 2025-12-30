@extends('layouts.admin')

@section('content')
    <div class="header">
        <h2>Manage Users</h2>
        <div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Create User</a>
        </div>
    </div>

    <div class="table">
        <div style="padding:12px 18px; display:flex; justify-content:space-between; align-items:center">
            <form method="get" style="margin:0">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search" style="padding:8px;border-radius:6px;border:1px solid #e5e7eb">
                <button class="btn" style="background:#ececec">Search</button>
            </form>
            <div>{{ $users->links() ?? '' }}</div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn" style="background:#f3f4f6">Edit</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="post" style="display:inline" onsubmit="return confirm('Delete this user?')">
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
