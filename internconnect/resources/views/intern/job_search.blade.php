@extends('layouts.intern')

@section('title', 'Job Search | ROC.PH')

@section('styles')
    @vite(['resources/sass/intern.job_search.scss'])
@endsection

@section('content')
    {{-- HEADER --}}
    <section class="page-header">
        <div>
            <h2>Job Search</h2>
            <p>Find your next marketing opportunity</p>
        </div>
        <div class="bell">🔔<span>3</span></div>
    </section>

    {{-- SEARCH --}}
    <section class="search-box">
        <input type="text" placeholder="Job title or keyword">
        <input type="text" placeholder="Location">
        <button>Search</button>
    </section>

    <p class="count">Showing 4 jobs</p>

    {{-- JOB LISTINGS --}}
    <section class="jobs">

        {{-- JOB ITEM --}}
        @foreach ($jobs as $job)
        <div class="job-card">
            <div class="job-info">
                <h3>{{ $job->title }}</h3>
                <strong>{{ $job->postedBy->name ?? 'Unknown Company' }}</strong>
                <p>
                    📍 {{ $job->location ?? 'Location not specified' }} 
                    • {{ $job->employment_type ?? 'Employment type not specified' }} 
                    • {{ $job->post_date->diffForHumans() }}
                </p>

                <div class="tags">
                    <span>Social Media</span>
                    <span>Content Creation</span>
                    <span>Analytics</span>
                </div>
            </div>

            <div class="job-action">
                <a href="{{ route('intern.job.application') }}">Apply Now</a>
                <p class="salary">₱15,000 - ₱20,000/month</p>
            </div>
        </div>
        @endforeach
    </section>
@endsection