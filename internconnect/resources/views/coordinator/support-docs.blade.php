@extends('layouts.coordinator')

@section('header', 'Support Documents')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-start border-4 border-primary">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted fw-bold small mb-1">Total Documents</p>
                        <h3 class="fw-bold mb-0">8</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded p-2 text-primary">
                        <i class="bi bi-file-earmark-text fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-start border-4 border-success">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted fw-bold small mb-1">Templates</p>
                        <h3 class="fw-bold mb-0">2</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded p-2 text-success">
                        <i class="bi bi-file-earmark-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-start border-4 border-info">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted fw-bold small mb-1">Guides</p>
                        <h3 class="fw-bold mb-0">2</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded p-2 text-info">
                        <i class="bi bi-folder-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-start border-4 border-warning">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted fw-bold small mb-1">Policies</p>
                        <h3 class="fw-bold mb-0">2</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded p-2 text-warning">
                        <i class="bi bi-file-earmark-ruled fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title fw-bold mb-0">Support Documents</h5>
                <div class="d-flex gap-2">
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Search documents...">
                    </div>
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-funnel me-1"></i> All Types
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">All Types</a></li>
                        <li><a class="dropdown-item" href="#">Guides</a></li>
                        <li><a class="dropdown-item" href="#">Templates</a></li>
                        <li><a class="dropdown-item" href="#">Handbooks</a></li>
                        <li><a class="dropdown-item" href="#">Policies</a></li>
                    </ul>
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Upload Document
                    </button>
                </div>
            </div>

            <!-- Document List -->
            <div class="d-flex flex-column gap-3">
                <!-- Onboarding Guide -->
                <div class="card border">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                <i class="bi bi-file-earmark-text fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Onboarding Guide</h6>
                                        <p class="text-muted small mb-2">Complete guide for new intern onboarding process</p>
                                        <div class="d-flex align-items-center gap-3 text-muted small">
                                            <span class="badge bg-primary bg-opacity-10 text-primary">Guide</span>
                                            <span><i class="bi bi-person me-1"></i>by You</span>
                                            <span><i class="bi bi-calendar3 me-1"></i>2024-03-01</span>
                                            <span><i class="bi bi-file-earmark me-1"></i>2.4 MB</span>
                                            <span><i class="bi bi-download me-1"></i>45 downloads</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Weekly Progress Template -->
                <div class="card border">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                <i class="bi bi-file-earmark-fill fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Weekly Progress Template</h6>
                                        <p class="text-muted small mb-2">Template for tracking weekly intern progress and achievements</p>
                                        <div class="d-flex align-items-center gap-3 text-muted small">
                                            <span class="badge bg-success bg-opacity-10 text-success">Template</span>
                                            <span><i class="bi bi-person me-1"></i>by You</span>
                                            <span><i class="bi bi-calendar3 me-1"></i>2024-02-28</span>
                                            <span><i class="bi bi-file-earmark me-1"></i>1.1 MB</span>
                                            <span><i class="bi bi-download me-1"></i>32 downloads</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Intern Handbook -->
                <div class="card border">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                <i class="bi bi-folder-fill fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Intern Handbook</h6>
                                        <p class="text-muted small mb-2">Official handbook with policies, guidelines, and expectations</p>
                                        <div class="d-flex align-items-center gap-3 text-muted small">
                                            <span class="badge bg-info bg-opacity-10 text-info">Handbook</span>
                                            <span><i class="bi bi-person me-1"></i>by HR Team</span>
                                            <span><i class="bi bi-calendar3 me-1"></i>2024-02-15</span>
                                            <span><i class="bi bi-file-earmark me-1"></i>5.8 MB</span>
                                            <span><i class="bi bi-download me-1"></i>87 downloads</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Safety Guidelines -->
                <div class="card border">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                <i class="bi bi-file-earmark-ruled fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Safety Guidelines</h6>
                                        <p class="text-muted small mb-2">Workplace safety guidelines and emergency procedures</p>
                                        <div class="d-flex align-items-center gap-3 text-muted small">
                                            <span class="badge bg-warning bg-opacity-10 text-warning">Policy</span>
                                            <span><i class="bi bi-person me-1"></i>by You</span>
                                            <span><i class="bi bi-calendar3 me-1"></i>2024-02-10</span>
                                            <span><i class="bi bi-file-earmark me-1"></i>800 KB</span>
                                            <span><i class="bi bi-download me-1"></i>28 downloads</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Evaluation Form -->
                <div class="card border">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                <i class="bi bi-file-earmark-fill fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Performance Evaluation Form</h6>
                                        <p class="text-muted small mb-2">Standardized form for intern performance evaluations</p>
                                        <div class="d-flex align-items-center gap-3 text-muted small">
                                            <span class="badge bg-success bg-opacity-10 text-success">Template</span>
                                            <span><i class="bi bi-person me-1"></i>by You</span>
                                            <span><i class="bi bi-calendar3 me-1"></i>2024-02-05</span>
                                            <span><i class="bi bi-file-earmark me-1"></i>950 KB</span>
                                            <span><i class="bi bi-download me-1"></i>41 downloads</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Training Resources -->
                <div class="card border">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                <i class="bi bi-file-earmark-text fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Training Resources</h6>
                                        <p class="text-muted small mb-2">Comprehensive training materials and learning resources</p>
                                        <div class="d-flex align-items-center gap-3 text-muted small">
                                            <span class="badge bg-primary bg-opacity-10 text-primary">Guide</span>
                                            <span><i class="bi bi-person me-1"></i>by You</span>
                                            <span><i class="bi bi-calendar3 me-1"></i>2024-01-28</span>
                                            <span><i class="bi bi-file-earmark me-1"></i>3.2 MB</span>
                                            <span><i class="bi bi-download me-1"></i>56 downloads</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Code of Conduct -->
                <div class="card border">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                <i class="bi bi-file-earmark-ruled fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Code of Conduct</h6>
                                        <p class="text-muted small mb-2">Professional behavior standards and ethical guidelines</p>
                                        <div class="d-flex align-items-center gap-3 text-muted small">
                                            <span class="badge bg-warning bg-opacity-10 text-warning">Policy</span>
                                            <span><i class="bi bi-person me-1"></i>by HR Team</span>
                                            <span><i class="bi bi-calendar3 me-1"></i>2024-01-20</span>
                                            <span><i class="bi bi-file-earmark me-1"></i>1.5 MB</span>
                                            <span><i class="bi bi-download me-1"></i>63 downloads</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remote Work Policy -->
                <div class="card border">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                                <i class="bi bi-folder-fill fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-semibold mb-1">Remote Work Policy</h6>
                                        <p class="text-muted small mb-2">Guidelines and requirements for remote work arrangements</p>
                                        <div class="d-flex align-items-center gap-3 text-muted small">
                                            <span class="badge bg-info bg-opacity-10 text-info">Handbook</span>
                                            <span><i class="bi bi-person me-1"></i>by You</span>
                                            <span><i class="bi bi-calendar3 me-1"></i>2024-01-15</span>
                                            <span><i class="bi bi-file-earmark me-1"></i>720 KB</span>
                                            <span><i class="bi bi-download me-1"></i>34 downloads</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Download">
                                            <i class="bi bi-download"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
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
    .card {
        transition: all 0.2s ease;
    }
    .card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.08) !important;
    }
</style>
@endsection

