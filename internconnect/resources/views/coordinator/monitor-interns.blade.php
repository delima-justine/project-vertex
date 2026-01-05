@extends('layouts.coordinator')

@section('header', 'Monitor Interns')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-start border-4 border-primary">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted fw-bold small mb-1">Total Interns</p>
                        <h3 class="fw-bold mb-0">{{ $totalInterns }}</h3>
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
                        <h3 class="fw-bold mb-0">{{ $activeInterns }}</h3>
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
                        <h3 class="fw-bold mb-0">{{ $needsSupport }}</h3>
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
                        <h3 class="fw-bold mb-0">{{ $avgProgress }}%</h3>
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
                    @if($interns->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-people fs-1 text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-2">No Interns Yet</h5>
                            <p class="text-muted">There are no interns assigned to you at the moment.</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($interns as $intern)
                                <div class="card border cursor-pointer" onclick="selectIntern({{ $intern['id'] }})">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-start justify-content-between mb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center fw-bold text-white" style="width: 48px; height: 48px;">
                                                    {{ $intern['initials'] }}
                                                </div>
                                                <div>
                                                    <h6 class="fw-semibold mb-0">{{ $intern['full_name'] }}</h6>
                                                    <small class="text-muted">{{ $intern['position'] }}</small>
                                                    <div><small class="text-muted">{{ $intern['department'] }}</small></div>
                                                </div>
                                            </div>
                                            <span class="badge bg-{{ $intern['status_badge'] }}">{{ $intern['status_text'] }}</span>
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-4">
                                                <small class="text-muted d-block">Progress</small>
                                                <strong>{{ $intern['progress_percentage'] }}%</strong>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted d-block">Attendance</small>
                                                <strong>{{ $intern['attendance_percentage'] }}%</strong>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted d-block">Tasks</small>
                                                <strong>{{ $intern['completed_tasks'] }}/{{ $intern['total_tasks'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small class="text-muted">Overall Progress</small>
                                                <small class="text-muted">{{ $intern['progress_percentage'] }}%</small>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                @php
                                                    $progressColor = $intern['progress_percentage'] >= 70 ? 'success' : ($intern['progress_percentage'] >= 40 ? 'warning' : 'danger');
                                                @endphp
                                                <div class="progress-bar bg-{{ $progressColor }}" role="progressbar" style="width: {{ $intern['progress_percentage'] }}%"></div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <small>Last active: {{ $intern['last_active'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm" id="intern-details">
                @if($interns->isEmpty())
                    <div class="card-body p-4 text-center">
                        <div class="mb-4 mt-4">
                            <i class="bi bi-person-circle fs-1 text-muted"></i>
                        </div>
                        <h5 class="text-muted mb-2">No Intern Selected</h5>
                        <p class="text-muted small mb-4">Select an intern from the list to view their details</p>
                    </div>
                @else
                    @php
                        $firstIntern = $interns->first();
                    @endphp
                    <div class="card-body p-4 text-center">
                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center fw-bold text-white mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                            {{ $firstIntern['initials'] }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $firstIntern['full_name'] }}</h5>
                        <p class="text-muted mb-4">{{ $firstIntern['position'] }}</p>

                        <div class="text-start">
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Department</small>
                                <strong class="text-primary">{{ $firstIntern['department'] }}</strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Email</small>
                                <strong>{{ $firstIntern['email'] }}</strong>
                            </div>
                            @if($firstIntern['contact_number'])
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">Phone</small>
                                    <strong>{{ $firstIntern['contact_number'] }}</strong>
                                </div>
                            @endif
                            <div class="mb-4">
                                <small class="text-muted d-block mb-1">Start Date</small>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar3 me-2"></i>
                                    <strong>{{ $firstIntern['created_at']->format('Y-m-d') }}</strong>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Progress Details</h6>
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Required Hours:</span>
                                        <strong>{{ $firstIntern['required_hours'] }} hrs</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Logged Hours:</span>
                                        <strong>{{ $firstIntern['logged_hours'] }} hrs</strong>
                                    </div>
                                    @if($firstIntern['milestone'])
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Current Milestone:</span>
                                            <strong>{{ $firstIntern['milestone'] }}</strong>
                                        </div>
                                    @endif
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
                @endif
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
    const internsData = @json($interns);

    function selectIntern(internId) {
        const intern = internsData.find(i => i.id === internId);
        if (!intern) return;

        const detailsPanel = document.getElementById('intern-details');
        const progressColor = intern.progress_percentage >= 70 ? 'success' : (intern.progress_percentage >= 40 ? 'warning' : 'danger');
        
        let milestoneHtml = '';
        if (intern.milestone) {
            milestoneHtml = `
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Current Milestone:</span>
                    <strong>${intern.milestone}</strong>
                </div>
            `;
        }
        
        let phoneHtml = '';
        if (intern.contact_number) {
            phoneHtml = `
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Phone</small>
                    <strong>${intern.contact_number}</strong>
                </div>
            `;
        }
        
        detailsPanel.innerHTML = `
            <div class="card-body p-4 text-center">
                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center fw-bold text-white mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                    ${intern.initials}
                </div>
                <h5 class="fw-bold mb-1">${intern.full_name}</h5>
                <p class="text-muted mb-4">${intern.position}</p>

                <div class="text-start">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Department</small>
                        <strong class="text-primary">${intern.department}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Email</small>
                        <strong>${intern.email}</strong>
                    </div>
                    ${phoneHtml}
                    <div class="mb-4">
                        <small class="text-muted d-block mb-1">Start Date</small>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar3 me-2"></i>
                            <strong>${new Date(intern.created_at).toLocaleDateString('en-CA')}</strong>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Progress Details</h6>
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Required Hours:</span>
                                <strong>${intern.required_hours} hrs</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Logged Hours:</span>
                                <strong>${intern.logged_hours} hrs</strong>
                            </div>
                            ${milestoneHtml}
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
        `;
    }
</script>
@endpush
