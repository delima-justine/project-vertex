@extends('layouts.hr')

@section('header', 'Create User')

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('hr.users.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card shadow-sm mx-auto" style="max-width: 800px;">
        <div class="card-body p-4">
            <form method="post" action="{{ route('hr.users.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control">
                        @error('first_name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control">
                        @error('last_name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Role</label>
                        <select name="role" class="form-select">
                            <option value="admin">Admin</option>
                            <option value="hr">HR</option>
                            <option value="coordinator">Coordinator</option>
                            <option value="student">Student</option>
                        </select>
                        @error('role') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control">
                        @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="col-12 text-end mt-4">
                        <button class="btn btn-primary px-4">Create User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection