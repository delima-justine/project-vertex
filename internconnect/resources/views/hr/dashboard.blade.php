@extends('layouts.hr')

@section('header', 'Dashboard')

@section('content')
    <div class="row g-4 mb-4">
        <!-- Stat Card 1 -->
        <div class="col-md-4">
            <div class="card h-100 border-start border-4 border-primary">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted fw-bold small mb-1">Total Interns</p>
                        <h3 class="fw-bold mb-0">{{ $totalInterns }}</h3>
                    </div>
                    <div class="bg-light rounded p-2 text-primary">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stat Card 2 -->
        <div class="col-md-4">
            <div class="card h-100 border-start border-4 border-success">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted fw-bold small mb-1">Active Applications</p>
                        <h3 class="fw-bold mb-0">{{ $activeApplications }}</h3>
                    </div>
                    <div class="bg-light rounded p-2 text-success">
                        <i class="bi bi-file-earmark-text fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stat Card 3 -->
        <div class="col-md-4">
            <div class="card h-100 border-start border-4 border-warning">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-uppercase text-muted fw-bold small mb-1">Pending Documents</p>
                        <h3 class="fw-bold mb-0">{{ $pendingDocuments }}</h3>
                    </div>
                    <div class="bg-light rounded p-2 text-warning">
                        <i class="bi bi-folder fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h5 class="card-title fw-bold mb-3">Recent Activities</h5>
            @if(count($recentActivities) > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentActivities as $activity)
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="mb-0 fw-bold">{{ $activity['title'] }}</h6>
                                    <span class="badge bg-{{ $activity['type_badge'] }}">{{ $activity['type'] }}</span>
                                </div>
                                <p class="mb-0 text-muted small">{{ $activity['subtitle'] }} • {{ $activity['description'] }}</p>
                            </div>
                            <span class="badge bg-light text-dark ms-3">{{ $activity['display_date'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">No recent activities.</p>
            @endif
        </div>
    </div>
@endsection