@extends('layouts.hr')

@section('header', 'Edit User')

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('hr.users.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card shadow-sm mx-auto" style="max-width: 800px;">
        <div class="card-body p-4">
            <form method="post" action="{{ route('hr.users.update', $user) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="form-control">
                        @error('first_name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="form-control">
                        @error('last_name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
                        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Role</label>
                        <select name="role" class="form-select">
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="hr" {{ $user->role == 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="coordinator" {{ $user->role == 'coordinator' ? 'selected' : '' }}>Coordinator</option>
                            <option value="student" {{ $user->role == 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                        @error('role') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">New Password <small class="text-muted fw-normal">(leave blank to keep)</small></label>
                        <input type="password" name="password" class="form-control">
                        @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="col-12 text-end mt-4">
                        <button class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection