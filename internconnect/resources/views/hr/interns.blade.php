@extends('layouts.hr')

@section('header', 'Interns')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h5 class="card-title fw-bold mb-4">Track Intern Progress</h5>

            <div class="row g-3">
                @forelse($interns as $intern)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm border-start border-4 border-{{ $intern['status_badge'] }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">{{ $intern['name'] }}</h6>
                                        <div class="text-muted small">{{ $intern['department'] }}</div>
                                        <div class="text-muted small">Week {{ $intern['week'] }} of {{ $intern['of'] }}</div>
                                    </div>
                                    <div class="d-flex gap-2 align-items-start">
                                        <span class="badge rounded-pill bg-{{ $intern['status_badge'] }} bg-opacity-10 text-{{ $intern['status_badge'] }}">
                                            {{ $intern['status_text'] }}
                                        </span>
                                        <a href="{{ route('hr.interns.show', $intern['id']) }}" class="btn btn-sm btn-primary">
                                            View Profile
                                        </a>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mt-3">
                                    <span class="text-muted small me-2">Hours: {{ $intern['logged_hours'] }}/{{ $intern['required_hours'] }}</span>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $intern['status_badge'] }}" role="progressbar" style="width: {{ $intern['progress'] }}%" aria-valuenow="{{ $intern['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="text-muted small ms-2 fw-bold">{{ $intern['progress'] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4">
                        No interns to display.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection