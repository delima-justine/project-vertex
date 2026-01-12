@extends('layouts.intern')

@section('title', 'ROC.PH Intern Portal')

@section('styles')
    @vite(['resources/sass/intern.dashboard.scss'])
@endsection

@section('content')
    <section class="welcome">
        <h1>Welcome back, {{ $user->first_name }}!</h1>
        <p>You have {{ $pendingApplications }} applications in review and {{ $interviewingApplications }} interviews scheduled.</p>
    </section>

    <section class="stats">
        <div class="card blue">
            <span>{{ $totalApplications }}</span>
            <p>Applications</p>
        </div>
        <div class="card yellow">
            <span>{{ $pendingApplications }}</span>
            <p>In Review</p>
        </div>
        <div class="card green">
            <span>{{ $interviewingApplications }}</span>
            <p>Interviews</p>
        </div>
        <div class="card red">
            <span>{{ $offersCount }}</span>
            <p>Offers</p>
        </div>
    </section>

    <section class="lower">
        <div class="panel">
            <h3>Recent Activity</h3>
            @if($recentApplications->count() > 0)
                <ul>
                    @foreach($recentApplications as $application)
                        <li>
                            Applied to {{ $application->jobPosting->title }}
                            <small>{{ $application->application_date->diffForHumans() }}</small>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">No recent applications yet.</p>
            @endif
        </div>

        <div class="panel">
            <h3>Quick Links</h3>
            <div class="task high">
                <a href="{{ route('intern.job.search') }}">Browse Job Postings</a>
            </div>
            <div class="task medium">
                <a href="{{ route('intern.profile', $user->user_id) }}">View Your Profile</a>
            </div>
            <div class="task low">
                <a href="{{ route('intern.job.application') }}">My Applications</a>
            </div>
        </div>
    </section>
@endsection
