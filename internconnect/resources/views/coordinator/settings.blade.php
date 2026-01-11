@extends('layouts.coordinator')

@section('header', 'Settings')

@section('content')
    <div class="row g-4">
        <!-- Settings Menu -->
        <div class="col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3">Settings Menu</h6>
                    <div class="list-group list-group-flush">
                        <a href="#profile-settings" class="list-group-item list-group-item-action border-0 rounded mb-1 active" id="profile-tab" data-bs-toggle="pill">
                            <i class="bi bi-person me-2"></i> Profile Settings
                        </a>
                        {{-- <a href="#notifications" class="list-group-item list-group-item-action border-0 rounded mb-1 disabled text-muted" style="opacity: 0.6;">
                            <i class="bi bi-bell me-2"></i> Notifications
                        </a> --}}
                        <a href="#security-settings" class="list-group-item list-group-item-action border-0 rounded mb-1" id="security-tab" data-bs-toggle="pill">
                            <i class="bi bi-lock me-2"></i> Security
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="col-lg-9">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="tab-content">
                <!-- Profile Settings -->
                <div class="tab-pane fade show active" id="profile-settings">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                    <i class="bi bi-person fs-4 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="fw-bold mb-0">Profile Settings</h5>
                                    <p class="text-muted small mb-0">Manage your personal information</p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('coordinator.update-profile') }}">
                                @csrf
                                
                                <!-- Profile Photo Display (No upload) -->
                                <div class="mb-4 text-center">
                                    <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center fw-bold text-white mb-3" style="width: 100px; height: 100px; font-size: 48px;">
                                        {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                    </div>
                                    <div class="text-muted small">
                                        Profile Avatar
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <!-- First Name -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-person text-muted"></i>
                                            </span>
                                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                                                   value="{{ old('first_name', $user->first_name) }}" required>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-person text-muted"></i>
                                            </span>
                                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                                   value="{{ old('last_name', $user->last_name) }}" required>
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Email Address -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-envelope text-muted"></i>
                                            </span>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                                   value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-telephone text-muted"></i>
                                            </span>
                                            <input type="tel" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" 
                                                   value="{{ old('contact_number', $user->contact_number) }}" placeholder="+63 917 555 1234">
                                            @error('contact_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Position (Read-only) -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Position</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-briefcase text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control" value="{{ $user->user_role }}" readonly>
                                        </div>
                                    </div>

                                    <!-- School (Read-only) -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">School</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-building text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control" 
                                                   value="{{ $user->school ? $user->school->school_name : 'N/A' }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="tab-pane fade" id="security-settings">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                    <i class="bi bi-lock fs-4 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="fw-bold mb-0">Security Settings</h5>
                                    <p class="text-muted small mb-0">Manage your account security</p>
                                </div>
                            </div>

                            <!-- Change Password -->
                            <div class="mb-4 pb-4 border-bottom">
                                <h6 class="fw-bold mb-1">Change Password</h6>
                                <p class="text-muted small mb-3">Update your password to keep your account secure</p>

                                <form method="POST" action="{{ route('coordinator.update-password') }}">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                                            <input type="password" name="current_password" 
                                                   class="form-control @error('current_password') is-invalid @enderror" 
                                                   placeholder="Enter current password" required>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                                            <input type="password" name="new_password" 
                                                   class="form-control @error('new_password') is-invalid @enderror" 
                                                   placeholder="Enter new password (min. 8 characters)" required>
                                            @error('new_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                                            <input type="password" name="new_password_confirmation" 
                                                   class="form-control" 
                                                   placeholder="Confirm new password" required>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-shield-check me-2"></i>Update Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .list-group-item.active {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        color: white;
    }
    .list-group-item:not(.disabled):hover {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .list-group-item.disabled {
        cursor: not-allowed;
    }
</style>
@endsection

