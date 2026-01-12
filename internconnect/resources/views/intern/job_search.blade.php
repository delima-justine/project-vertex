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
    </section>

    {{-- SEARCH --}}
    <section class="search-box">
        <input type="text" placeholder="Job title or keyword">
        <input type="text" placeholder="Location">
        <button>Search</button>
    </section>

    {{-- JOB COUNT --}}
    <p class="count">Showing {{ $jobs->count() }} jobs</p>

    {{-- JOB LISTINGS --}}
    <section class="jobs">

        {{-- JOB ITEM --}}
        @foreach ($jobs as $job)
        <div class="job-card">
            <div class="job-info">
                <h3>{{ $job->title }}</h3>
                <strong>{{ $job->department ?? 'Unknown Department' }}</strong>
                <p>
                    📍 {{ $job->location ?? 'Location not specified' }} 
                    • {{ $job->employment_type ?? 'Employment type not specified' }} 
                    • {{ $job->post_date->diffForHumans() }}
                </p>

                <div class="description">
                    <p>
                        {{ $job->description }}
                    </p>
                </div>
            </div>

            <div class="job-action">
                <a href="{{ route('intern.job.application') }}">Apply Now</a>
                <p class="salary">₱15,000 - ₱20,000/month</p>
            </div>
        </div>
        @endforeach
    </section>

    {{-- Pagination --}}
    {{ $jobs->links('pagination::bootstrap-5') }}
@endsection