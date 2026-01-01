@extends('layouts.hr')

@section('header', 'Manage Users')

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('hr.users.create') }}" class="btn btn-primary">Create User</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <form method="get" class="d-flex m-0">
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search..." class="form-control me-2">
                    <button class="btn btn-light border">Search</button>
                </form>
                <div>{{ $users->links() ?? '' }}</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 ps-4">ID</th>
                        <th class="py-3">First Name</th>
                        <th class="py-3">Last Name</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Role</th>
                        <th class="py-3">Created</th>
                        <th class="py-3 pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="ps-4">{{ $user->user_id }}</td>
                            <td>{{ $user->first_name }}</td>
                            <td>{{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($user->user_role) }}</span></td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td class="pe-4">
                                <a href="{{ route('hr.users.edit', $user) }}" class="btn btn-sm btn-light border me-1">Edit</a>
                                <form action="{{ route('hr.users.destroy', $user) }}" method="post" style="display:inline" onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection