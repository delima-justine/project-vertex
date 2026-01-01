@extends('layouts.hr')

@section('header', 'Create New Job Posting')

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ route('hr.job-postings.index') }}" class="btn" style="background:#e2e8f0; color:#475569">Cancel</a>
    </div>

    <div style="background:#fff; border-radius:10px; padding:24px; max-width: 800px;">
        <form action="{{ route('hr.job-postings.store') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label for="title" style="display:block; font-weight:600; margin-bottom:8px; color:#374151">Job Title</label>
                <input type="text" name="title" id="title" class="form-input" required 
                    style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; font-size:14px;"
                    value="{{ old('title') }}" placeholder="e.g. Marketing Intern">
                @error('title')
                    <div style="color:red; font-size:12px; margin-top:4px">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label for="department" style="display:block; font-weight:600; margin-bottom:8px; color:#374151">Department</label>
                    <input type="text" name="department" id="department" required 
                        style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; font-size:14px;"
                        value="{{ old('department') }}" placeholder="e.g. Marketing">
                    @error('department')
                        <div style="color:red; font-size:12px; margin-top:4px">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="salary_range" style="display:block; font-weight:600; margin-bottom:8px; color:#374151">Salary/Allowance (Optional)</label>
                    <input type="text" name="salary_range" id="salary_range" 
                        style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; font-size:14px;"
                        value="{{ old('salary_range') }}" placeholder="e.g. ₱5,000 - ₱8,000">
                    @error('salary_range')
                        <div style="color:red; font-size:12px; margin-top:4px">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="description" style="display:block; font-weight:600; margin-bottom:8px; color:#374151">Job Description</label>
                <textarea name="description" id="description" rows="5" required 
                    style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; font-size:14px; font-family:inherit">{{ old('description') }}</textarea>
                @error('description')
                    <div style="color:red; font-size:12px; margin-top:4px">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label for="requirements" style="display:block; font-weight:600; margin-bottom:8px; color:#374151">Requirements</label>
                <textarea name="requirements" id="requirements" rows="4" 
                    style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px; font-size:14px; font-family:inherit">{{ old('requirements') }}</textarea>
                @error('requirements')
                    <div style="color:red; font-size:12px; margin-top:4px">{{ $message }}</div>
                @enderror
            </div>

            <div style="text-align:right">
                <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:14px;">Publish Job Posting</button>
            </div>
        </form>
    </div>
@endsection
