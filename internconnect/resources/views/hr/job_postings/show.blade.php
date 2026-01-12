@extends('layouts.hr')

@section('header', $jobPosting->title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="text-muted mb-2">{{ $jobPosting->department }}</h6>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('hr.job-postings.edit', $jobPosting) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form action="{{ route('hr.job-postings.destroy', $jobPosting) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job posting?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </form>
            <a href="{{ route('hr.job-postings.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h5 class="fw-bold mb-3">Job Details</h5>
                    
                    <div class="mb-4">
                        <h6 class="text-muted small text-uppercase mb-2">Description</h6>
                        <p class="mb-0">{!! nl2br(e($jobPosting->description)) !!}</p>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted small text-uppercase mb-2">Requirements</h6>
                        <p class="mb-0">{!! nl2br(e($jobPosting->requirements)) !!}</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bg-light p-3 rounded mb-3">
                        <div class="mb-3">
                            <h6 class="text-muted small text-uppercase mb-2">Posted Date</h6>
                            <p class="mb-0">
                                {{ $jobPosting->post_date ? $jobPosting->post_date->format('M d, Y') : '—' }}
                                <small class="text-muted d-block">{{ $jobPosting->post_date ? $jobPosting->post_date->diffForHumans() : '' }}</small>
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted small text-uppercase mb-2">Salary/Allowance</h6>
                            <p class="mb-0">{{ $jobPosting->salary_range ?? 'Not specified' }}</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted small text-uppercase mb-2">Posted By</h6>
                            <p class="mb-0">
                                {{ $jobPosting->postedBy ? $jobPosting->postedBy->first_name . ' ' . $jobPosting->postedBy->last_name : 'Unknown' }}
                            </p>
                        </div>

                        <div>
                            <h6 class="text-muted small text-uppercase mb-2">Applications</h6>
                            <p class="mb-0 fw-bold fs-5">{{ $jobPosting->applications()->count() }}</p>
                        </div>
                    </div>

                    @if($jobPosting->post_date && $jobPosting->post_date->isAfter(now()->subDays(30)))
                        <span class="badge bg-success w-100 py-2">Active</span>
                    @else
                        <span class="badge bg-secondary w-100 py-2">Closed</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
