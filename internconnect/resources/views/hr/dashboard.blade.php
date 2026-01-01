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
                        <h3 class="fw-bold mb-0">12</h3>
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
                        <h3 class="fw-bold mb-0">5</h3>
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
                        <h3 class="fw-bold mb-0">3</h3>
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
            <p class="text-muted">No recent activities.</p>
        </div>
    </div>
@endsection