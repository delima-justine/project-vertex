@extends('layouts.hr')

@section('header', 'Create New Job Posting')

@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('hr.job-postings.index') }}" class="btn btn-secondary">Cancel</a>
    </div>

    <div class="card shadow-sm mx-auto" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('hr.job-postings.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Job Title</label>
                    <input type="text" name="title" id="title" class="form-control" required value="{{ old('title') }}" placeholder="e.g. Marketing Intern">
                    @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="department" class="form-label fw-bold">Department</label>
                        <input type="text" name="department" id="department" class="form-control" required value="{{ old('department') }}" placeholder="e.g. Marketing">
                        @error('department') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="salary_range" class="form-label fw-bold">Salary/Allowance (Optional)</label>
                        <input type="text" name="salary_range" id="salary_range" class="form-control" value="{{ old('salary_range') }}" placeholder="e.g. ₱5,000 - ₱8,000">
                        @error('salary_range') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Job Description</label>
                    <textarea name="description" id="description" rows="5" class="form-control" required>{{ old('description') }}</textarea>
                    @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label for="requirements" class="form-label fw-bold">Requirements</label>
                    <textarea name="requirements" id="requirements" rows="4" class="form-control">{{ old('requirements') }}</textarea>
                    @error('requirements') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">Publish Job Posting</button>
                </div>
            </form>
        </div>
    </div>
@endsection