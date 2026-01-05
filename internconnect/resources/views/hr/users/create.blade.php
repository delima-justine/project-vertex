@extends('layouts.hr')

@section('header', 'Create User')

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('hr.users.index') }}" class="btn btn-secondary">Back</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <h5>Errors:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

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
                        <select name="role" class="form-select" id="roleSelect">
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="hr" {{ old('role') == 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="coordinator" {{ old('role') == 'coordinator' ? 'selected' : '' }}>Coordinator</option>
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                        @error('role') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12" id="schoolField">
                        <label class="form-label fw-bold">School <small class="text-muted">(Required for Coordinators and Students)</small></label>
                        <select name="school_id" class="form-select">
                            <option value="">-- Select School --</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->school_id }}" {{ old('school_id') == $school->school_id ? 'selected' : '' }}>
                                    {{ $school->school_name }} @if($school->branch_campus) - {{ $school->branch_campus }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('school_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12" id="coordinatorField" style="display: none;">
                        <label class="form-label fw-bold">Assign Coordinator <small class="text-muted">(Optional for Students/Interns)</small></label>
                        <select name="assign_coordinator_id" class="form-select">
                            <option value="">-- No Coordinator Assigned --</option>
                            @foreach($coordinators as $coordinator)
                                <option value="{{ $coordinator->coordinator_id }}" {{ old('assign_coordinator_id') == $coordinator->coordinator_id ? 'selected' : '' }}>
                                    {{ $coordinator->first_name }} {{ $coordinator->last_name }} 
                                    @if($coordinator->school) ({{ $coordinator->school->school_name }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('assign_coordinator_id') <div class="text-danger small">{{ $message }}</div> @enderror
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('roleSelect');
            const schoolField = document.getElementById('schoolField');
            const coordinatorField = document.getElementById('coordinatorField');
            
            function toggleFields() {
                const role = roleSelect.value;
                console.log('Role changed to:', role); // Debug log
                
                // Show school field for coordinator and student
                if (role === 'coordinator' || role === 'student') {
                    schoolField.style.display = 'block';
                } else {
                    schoolField.style.display = 'none';
                }
                
                // Show coordinator field ONLY for student/intern (hide for admin, hr, coordinator)
                if (role === 'student') {
                    coordinatorField.style.display = 'block';
                    console.log('Coordinator field SHOWN'); // Debug log
                } else {
                    coordinatorField.style.display = 'none';
                    console.log('Coordinator field HIDDEN'); // Debug log
                }
            }
            
            // Listen for role changes - THIS MAKES IT REACTIVE
            roleSelect.addEventListener('change', toggleFields);
            
            // Run on page load to set initial state
            toggleFields();
        });
    </script>
    @endpush
@endsection