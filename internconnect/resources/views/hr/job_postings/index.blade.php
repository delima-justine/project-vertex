@extends('layouts.hr')

@section('header', 'Job Postings')

@section('content')
    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('hr.job-postings.backup') }}" class="btn btn-outline-secondary" title="Backup all job postings">
            <i class="bi bi-download"></i> Backup
        </a>
        <a href="{{ route('hr.job-postings.restore') }}" class="btn btn-outline-info" title="Restore from backup">
            <i class="bi bi-upload"></i> Restore
        </a>
        <a href="{{ route('hr.job-postings.create') }}" class="btn btn-primary">+ Post New Job</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex flex-column gap-3">
                @forelse($jobPostings as $job)
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                        <div style="flex: 1;">
                            <h5 class="fw-bold mb-1">{{ $job->title }}</h5>
                            <div class="text-muted small mb-2">{{ $job->department }}</div>
                            <div class="text-muted small">
                                {{ $job->applications_count ?? 0 }} applications · Posted {{ $job->post_date ? $job->post_date->diffForHumans() : '—' }}
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 ms-3">
                            @if($job->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Active</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Closed</span>
                            @endif
                            <a href="{{ route('hr.job-postings.show', $job) }}" class="btn btn-sm btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('hr.job-postings.edit', $job) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('hr.job-postings.destroy', $job) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job posting?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">No job postings yet.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection