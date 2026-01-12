@extends('layouts.hr')

@section('header', 'Dashboard')

@section('content')
    <div class="row g-4 mb-4">
        <!-- Stat Card 1 - Total Interns -->
        <div class="col-md-3">
            <a href="{{ route('hr.interns') }}" class="text-decoration-none">
                <div class="card h-100 border-start border-4 border-primary" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
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
            </a>
        </div>
        <!-- Stat Card 2 - Active Applications -->
        <div class="col-md-3">
            <a href="{{ route('hr.job-postings.index') }}" class="text-decoration-none">
                <div class="card h-100 border-start border-4 border-success" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
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
            </a>
        </div>
        <!-- Stat Card 3 - Pending Documents -->
        <div class="col-md-3">
            <a href="{{ route('hr.users.index') }}" class="text-decoration-none">
                <div class="card h-100 border-start border-4 border-warning" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
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
            </a>
        </div>
        <!-- Stat Card 4 - Total Job Posts -->
        <div class="col-md-3">
            <a href="{{ route('hr.job-postings.index') }}" class="text-decoration-none">
                <div class="card h-100 border-start border-4 border-info" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-uppercase text-muted fw-bold small mb-1">Total Job Posts</p>
                            <h3 class="fw-bold mb-0">{{ $totalJobPostings }}</h3>
                        </div>
                        <div class="bg-light rounded p-2 text-info">
                            <i class="bi bi-briefcase fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
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