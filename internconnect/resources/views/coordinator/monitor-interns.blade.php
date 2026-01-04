@extends('layouts.coordinator')

@section('header', 'Monitor Interns')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-start border-4 border-primary">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted fw-bold small mb-1">Total Interns</p>
                        <h3 class="fw-bold mb-0">4</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded p-2 text-primary">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-start border-4 border-success">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted fw-bold small mb-1">Active Interns</p>
                        <h3 class="fw-bold mb-0">3</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded p-2 text-success">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-start border-4 border-warning">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted fw-bold small mb-1">Needs Support</p>
                        <h3 class="fw-bold mb-0">1</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded p-2 text-warning">
                        <i class="bi bi-exclamation-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-start border-4 border-info">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted fw-bold small mb-1">Avg Progress</p>
                        <h3 class="fw-bold mb-0">59%</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded p-2 text-info">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-bold mb-0">Intern List</h5>
                        <div class="d-flex gap-2">
                            <div class="input-group" style="max-width: 300px;">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" placeholder="Search interns...">
                            </div>
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-funnel me-1"></i> All Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">All Status</a></li>
                                <li><a class="dropdown-item" href="#">Active</a></li>
                                <li><a class="dropdown-item" href="#">Needs Support</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Intern Cards -->
                    <div class="d-flex flex-column gap-3">
                        <!-- Emma Davis -->
                        <div class="card border cursor-pointer" onclick="selectIntern('emma')">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width: 48px; height: 48px;">
                                            ED
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-0">Emma Davis</h6>
                                            <small class="text-muted">Marketing Intern</small>
                                            <div><small class="text-muted">Marketing</small></div>
                                        </div>
                                    </div>
                                    <span class="badge bg-success">Active</span>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Progress</small>
                                        <strong>75%</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Attendance</small>
                                        <strong>95%</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Tasks</small>
                                        <strong>12/15</strong>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Overall Progress</small>
                                        <small class="text-muted">75%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 75%"></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <small>Last active: 2 hours ago</small>
                                </div>
                            </div>
                        </div>

                        <!-- Frank Miller -->
                        <div class="card border cursor-pointer" onclick="selectIntern('frank')">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width: 48px; height: 48px;">
                                            FM
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-0">Frank Miller</h6>
                                            <small class="text-muted">Software Developer Intern</small>
                                            <div><small class="text-muted">Technology</small></div>
                                        </div>
                                    </div>
                                    <span class="badge bg-success">Active</span>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Progress</small>
                                        <strong>50%</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Attendance</small>
                                        <strong>88%</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Tasks</small>
                                        <strong>8/16</strong>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Overall Progress</small>
                                        <small class="text-muted">50%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 50%"></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <small>Last active: 5 hours ago</small>
                                </div>
                            </div>
                        </div>

                        <!-- Grace Lee -->
                        <div class="card border cursor-pointer" onclick="selectIntern('grace')">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width: 48px; height: 48px;">
                                            GL
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-0">Grace Lee</h6>
                                            <small class="text-muted">Design Intern</small>
                                            <div><small class="text-muted">Creative</small></div>
                                        </div>
                                    </div>
                                    <span class="badge bg-warning">Needs Support</span>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Progress</small>
                                        <strong>30%</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Attendance</small>
                                        <strong>92%</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Tasks</small>
                                        <strong>5/16</strong>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Overall Progress</small>
                                        <small class="text-muted">30%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 30%"></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <small>Last active: 1 day ago</small>
                                </div>
                            </div>
                        </div>

                        <!-- Henry Park -->
                        <div class="card border cursor-pointer" onclick="selectIntern('henry')">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width: 48px; height: 48px;">
                                            HP
                                        </div>
                                        <div>
                                            <h6 class="fw-semibold mb-0">Henry Park</h6>
                                            <small class="text-muted">Data Analytics Intern</small>
                                            <div><small class="text-muted">Analytics</small></div>
                                        </div>
                                    </div>
                                    <span class="badge bg-success">Active</span>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Progress</small>
                                        <strong>82%</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Attendance</small>
                                        <strong>97%</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Tasks</small>
                                        <strong>14/16</strong>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Overall Progress</small>
                                        <small class="text-muted">82%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 82%"></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <small>Last active: 30 minutes ago</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm" id="intern-details">
                <div class="card-body p-4 text-center">
                    <div class="bg-info rounded-circle d-flex align-items-center justify-content-center fw-bold text-white mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                        ED
                    </div>
                    <h5 class="fw-bold mb-1">Emma Davis</h5>
                    <p class="text-muted mb-4">Marketing Intern</p>

                    <div class="text-start">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Department</small>
                            <strong class="text-primary">Marketing</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Supervisor</small>
                            <strong>Sarah Johnson</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Email</small>
                            <strong>emma.davis@roc.ph</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Phone</small>
                            <strong>+63 917 123 4567</strong>
                        </div>
                        <div class="mb-4">
                            <small class="text-muted d-block mb-1">Start Date</small>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 me-2"></i>
                                <strong>2024-01-15</strong>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Milestones</h6>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">Onboarding Complete</div>
                                        <small class="text-muted">2024-01-20</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">First Campaign Launch</div>
                                        <small class="text-muted">2024-02-05</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">Mid-term Review</div>
                                        <small class="text-muted">2024-03-01</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-circle text-muted mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-muted">Final Project</div>
                                        <small class="text-muted">2024-04-15</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-journal-plus me-2"></i>Add Note
                        </button>
                        <button class="btn btn-outline-primary w-100">
                            <i class="bi bi-envelope me-2"></i>Send Message
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .cursor-pointer {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .cursor-pointer:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection

@push('scripts')
<script>
    function selectIntern(intern) {
        // In a real app, this would fetch intern details and update the panel
        console.log('Selected intern:', intern);
    }
</script>
@endpush
