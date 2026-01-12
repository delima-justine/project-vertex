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
        <div class="job-card">
            <div class="job-info">
                <h3>Marketing Intern</h3>
                <strong>TechCorp Solutions</strong>
                <p>📍 Manila, Philippines • Full-time • 2 days ago</p>

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

        <div class="job-card">
            <div class="job-info">
                <h3>Digital Marketing Assistant</h3>
                <strong>Digital Agency Pro</strong>
                <p>📍 Makati, Philippines • Part-time • 5 days ago</p>

                <div class="tags">
                    <span>SEO</span>
                    <span>Email Marketing</span>
                    <span>Copywriting</span>
                </div>
            </div>

            <div class="job-action">
                <a href="{{ route('intern.job.application') }}">Apply Now</a>
                <p class="salary">₱12,000 - ₱18,000/month</p>
            </div>
        </div>

        <div class="job-card">
            <div class="job-info">
                <h3>Social Media Intern</h3>
                <strong>StartupXYZ</strong>
                <p>🌐 Remote • Internship • 1 week ago</p>

                <div class="tags">
                    <span>Instagram</span>
                    <span>TikTok</span>
                    <span>Content Strategy</span>
                </div>
            </div>

            <div class="job-action">
                <a href="{{ route('intern.job.application') }}">Apply Now</a>
                <p class="salary">₱10,000 - ₱15,000/month</p>
            </div>
        </div>

        <div class="job-card">
            <div class="job-info">
                <h3>Content Marketing Trainee</h3>
                <strong>Creative Hub Inc</strong>
                <p>📍 Quezon City • Full-time • 1 week ago</p>

                <div class="tags">
                    <span>Blog Writing</span>
                    <span>Video Production</span>
                    <span>Branding</span>
                </div>
            </div>

            <div class="job-action">
                <a href="{{ route('intern.job.application') }}">Apply Now</a>
                <p class="salary">₱18,000 - ₱25,000/month</p>
            </div>
        </div>

    </section>
@endsection