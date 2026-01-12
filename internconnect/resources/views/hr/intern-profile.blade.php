@extends('layouts.hr')

@section('header', 'Intern Profile')

@section('content')
<div class="row">
    <!-- Back Button -->
    <div class="col-12 mb-3">
        <a href="{{ route('hr.interns') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Interns
        </a>
    </div>

    <!-- Profile Header -->
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $internData['name'] }}</h4>
                        <p class="text-muted mb-3">{{ $internData['department'] }}</p>
                        
                        <div class="row g-4 mt-2">
                            <div class="col-auto">
                                <small class="text-muted d-block">Email</small>
                                <span class="fw-500">{{ $internData['email'] }}</span>
                            </div>
                            <div class="col-auto">
                                <small class="text-muted d-block">Contact</small>
                                <span class="fw-500">{{ $internData['contact_number'] ?? 'N/A' }}</span>
                            </div>
                            <div class="col-auto">
                                <small class="text-muted d-block">Start Date</small>
                                <span class="fw-500">{{ $internData['created_at']->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <span class="badge rounded-pill bg-{{ $internData['status_badge'] }} bg-opacity-10 text-{{ $internData['status_badge'] }} fs-6">
                        {{ $internData['status_text'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-4">Progress Overview</h5>
                
                <!-- Week Progress -->
                <div class="mb-4">
                    <label class="form-label text-muted small">Internship Duration</label>
                    <p class="mb-2 fw-bold">Week {{ $internData['week'] }} of {{ $internData['of'] }}</p>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($internData['week'] / $internData['of']) * 100 }}%" aria-valuenow="{{ $internData['week'] }}" aria-valuemin="0" aria-valuemax="{{ $internData['of'] }}"></div>
                    </div>
                </div>

                <!-- Hours Progress -->
                <div class="mb-4">
                    <label class="form-label text-muted small">Hours Logged</label>
                    <p class="mb-2 fw-bold">{{ $internData['logged_hours'] }} / {{ $internData['required_hours'] }} hours</p>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-{{ $internData['status_badge'] }}" role="progressbar" style="width: {{ $internData['progress'] }}%" aria-valuenow="{{ $internData['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted mt-2 d-block">{{ $internData['progress'] }}% Complete</small>
                </div>

                <!-- Status Details -->
                <div class="alert alert-{{ $internData['status_badge'] }} bg-{{ $internData['status_badge'] }} bg-opacity-10 border-{{ $internData['status_badge'] }} mb-0">
                    <h6 class="fw-bold mb-2">{{ $internData['status_text'] }}</h6>
                    @if($internData['status'] === 'completed')
                        <p class="mb-0 small">This intern has successfully completed their internship program with {{ $internData['progress'] }}% progress.</p>
                    @elseif($internData['status'] === 'needs-attention')
                        <p class="mb-0 small">This intern is in week {{ $internData['week'] }} and needs attention. Current progress is {{ $internData['progress'] }}%.</p>
                    @else
                        <p class="mb-0 small">This intern is on track. Currently in week {{ $internData['week'] }} with {{ $internData['progress'] }}% progress.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-12 col-lg-4">
        <div class="row g-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <small class="text-muted d-block mb-2">Documents Submitted</small>
                        <h5 class="fw-bold">{{ $internData['documents_count'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <small class="text-muted d-block mb-2">Job Applications</small>
                        <h5 class="fw-bold">{{ $internData['applications_count'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Milestones/Achievements -->
    @if($intern->progress && $intern->progress->milestone)
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-3">Milestone</h5>
                <p class="mb-2">{{ $intern->progress->milestone }}</p>
                @if($intern->progress->milestone_achieved_date)
                    <small class="text-muted">Achieved on {{ \Carbon\Carbon::parse($intern->progress->milestone_achieved_date)->format('M d, Y') }}</small>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Evaluation Score -->
    @if($intern->progress && $intern->progress->evaluation_score)
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-3">Evaluation Score</h5>
                <div class="d-flex align-items-center">
                    <h3 class="fw-bold text-primary me-3">{{ $intern->progress->evaluation_score }}/100</h3>
                    <div class="progress flex-grow-1" style="height: 12px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $intern->progress->evaluation_score }}%" aria-valuenow="{{ $intern->progress->evaluation_score }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .fw-500 {
        font-weight: 500;
    }
</style>
@endsection
