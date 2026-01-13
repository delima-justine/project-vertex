@extends('layouts.hr')

@section('header', 'Edit Job Posting')

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('hr.job-postings.index') }}" class="btn btn-secondary">Cancel</a>
    </div>

    <div class="card shadow-sm mx-auto" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('hr.job-postings.update', $jobPosting) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Job Title</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" required value="{{ old('title', $jobPosting->title) }}" placeholder="e.g. Marketing Intern">
                    @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="department" class="form-label fw-bold">Department</label>
                        <input type="text" name="department" id="department" class="form-control @error('department') is-invalid @enderror" required value="{{ old('department', $jobPosting->department) }}" placeholder="e.g. Marketing">
                        @error('department') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="salary_range" class="form-label fw-bold">Salary/Allowance (Optional)</label>
                        <input type="text" name="salary_range" id="salary_range" class="form-control @error('salary_range') is-invalid @enderror" value="{{ old('salary_range', $jobPosting->salary_range) }}" placeholder="e.g. ₱5,000 - ₱8,000">
                        @error('salary_range') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Job Description</label>
                    <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $jobPosting->description) }}</textarea>
                    @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label for="requirements" class="form-label fw-bold">Requirements</label>
                    <textarea name="requirements" id="requirements" rows="4" class="form-control @error('requirements') is-invalid @enderror">{{ old('requirements', $jobPosting->requirements) }}</textarea>
                    @error('requirements') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">Update Job Posting</button>
                </div>
            </form>
        </div>
    </div>
@endsection
