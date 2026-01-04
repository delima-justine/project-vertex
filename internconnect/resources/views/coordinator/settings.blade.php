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
                        <a href="#notifications" class="list-group-item list-group-item-action border-0 rounded mb-1 disabled text-muted" style="opacity: 0.6;">
                            <i class="bi bi-bell me-2"></i> Notifications
                        </a>
                        <a href="#preferences" class="list-group-item list-group-item-action border-0 rounded mb-1 disabled text-muted" style="opacity: 0.6;">
                            <i class="bi bi-sliders me-2"></i> Preferences
                        </a>
                        <a href="#security-settings" class="list-group-item list-group-item-action border-0 rounded mb-1" id="security-tab" data-bs-toggle="pill">
                            <i class="bi bi-lock me-2"></i> Security
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="col-lg-9">
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

                            <form>
                                <!-- Profile Photo -->
                                <div class="mb-4 text-center">
                                    <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center fw-bold text-white mb-3" style="width: 100px; height: 100px; font-size: 48px;">
                                        {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-camera me-2"></i>Change Photo
                                        </button>
                                        <div class="text-muted small mt-2">JPG, PNG or GIF. Max size 2MB</div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <!-- Full Name -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-person text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control" value="{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}">
                                        </div>
                                    </div>

                                    <!-- Email Address -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-envelope text-muted"></i>
                                            </span>
                                            <input type="email" class="form-control" value="{{ Auth::user()->email }}">
                                        </div>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-telephone text-muted"></i>
                                            </span>
                                            <input type="tel" class="form-control" value="{{ Auth::user()->contact_number ?? '+63 917 555 1234' }}" placeholder="+63 917 555 1234">
                                        </div>
                                    </div>

                                    <!-- Position -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Position</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-briefcase text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control" value="{{ Auth::user()->user_role }}" readonly>
                                        </div>
                                    </div>

                                    <!-- Department -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Department</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-building text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control" value="Operations" placeholder="Operations">
                                        </div>
                                    </div>

                                    <!-- Location -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Location</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-geo-alt text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control" value="Manila, Philippines" placeholder="Manila, Philippines">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="button" class="btn btn-outline-secondary me-2">Cancel</button>
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

                                <form>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Current Password</label>
                                            <input type="password" class="form-control" placeholder="Enter current password">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">New Password</label>
                                            <input type="password" class="form-control" placeholder="Enter new password">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Confirm New Password</label>
                                            <input type="password" class="form-control" placeholder="Confirm new password">
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

