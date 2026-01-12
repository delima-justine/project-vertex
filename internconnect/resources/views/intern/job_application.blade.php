@extends('layouts.intern')

@section('title', 'Job Application - ROC.PH Intern Portal')

@section('styles')
    @vite(['resources/sass/intern.job_application.scss'])
@endsection

@section('content')
    <div class="header">
        <a href="{{ route('intern.job.search') }}" class="back">&larr; Back</a>
        <h2>Apply for Position</h2>
        <p>Marketing Intern at TechCorp Solutions</p>
    </div>  

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="errors">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-wrapper">
        <!-- Form -->
        <form class="application-form" method="POST" action="#" enctype="multipart/form-data">
            @csrf

            <h3>Application Form</h3>
            <p class="subtitle">Fill out the form below to submit your application</p>

            <label>Full Name *</label>
            <input type="text" name="name" value="{{ old('name') }}">

            <div class="row">
                <div>
                    <label>Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}">
                </div>
                <div>
                    <label>Phone *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}">
                </div>
            </div>

            <label>Resume / CV *</label>
            <div class="upload-box">
                <input type="file" name="resume">
                <span>Click to upload resume<br><small>PDF, DOC, DOCX (Max 5MB)</small></span>
            </div>

            <label>Portfolio URL (Optional)</label>
            <input type="url" name="portfolio" value="{{ old('portfolio') }}">

            <label>Cover Letter *</label>
            <textarea name="cover_letter" rows="5">{{ old('cover_letter') }}</textarea>
            <small>Minimum 100 characters</small>

            <button type="submit">Submit Application</button>
        </form>

        <!-- Job Details -->
        <aside class="job-details">
            <h4>Job Details</h4>
            <ul>
                <li><strong>Position:</strong> Marketing Intern</li>
                <li><strong>Company:</strong> TechCorp Solutions</li>
                <li><strong>Location:</strong> Manila, Philippines</li>
                <li><strong>Employment Type:</strong> Full-time</li>
                <li><strong>Salary:</strong> ₱15,000 – ₱20,000</li>
            </ul>

            <div class="tip">
                <strong>Application Tip</strong>
                <p>Make sure your resume highlights relevant experience and skills.</p>
            </div>
        </aside>
    </div>
@endsection
