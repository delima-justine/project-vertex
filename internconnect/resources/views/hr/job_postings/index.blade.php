@extends('layouts.hr')

@section('header', 'Job Postings')

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('hr.job-postings.create') }}" class="btn btn-primary">+ Post New Job</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex flex-column gap-3">
                @forelse($jobPostings as $job)
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $job->title }}</h5>
                            <div class="text-muted small mb-2">{{ $job->department }}</div>
                            <div class="text-muted small">
                                {{ $job->applications_count ?? 0 }} applications · Posted {{ $job->post_date ? $job->post_date->diffForHumans() : '—' }}
                            </div>
                        </div>
                        <div>
                            @if($job->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Active</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Closed</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">No job postings yet.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection