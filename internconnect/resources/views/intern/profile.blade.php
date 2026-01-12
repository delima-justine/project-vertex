@extends('layouts.intern')

@section('title', 'Profile | ROC.PH')

@section('styles')
    @vite(['resources/sass/intern.profile.scss'])
@endsection

@section('content')
    {{-- PROFILE HEADER --}}
    <section class="profile-header">
        <div>
            <h3>Profile Management</h3>
            <p>Manage your profile and showcase your skills</p>
        </div>
        @if(auth()->id() == $intern_details->user_id)
            <a href="{{ route('intern.profile.edit') }}" class="edit-btn">Edit Profile</a>
        @endif
    </section>

    <section class="cover">
        <div class="avatar-large"></div>
        <div class="info">
            <h2>{{ $intern_details->first_name }} {{ $intern_details->last_name }}</h2>
            {{-- 📍 Manila, Philippines &nbsp;  --}}
            <p> 
                ✉ {{ $intern_details->email }} &nbsp; 
                ☎ {{ $intern_details->contact_number }}
            </p>
        </div>
    </section>

    {{-- ABOUT --}}
    <section class="card">
        <h3>About Me</h3>
        <p>
            {{ $intern_details->about ?? 'No bio added yet.' }}
        </p>

        <div class="links">
            @if($intern_details->linkedin_url)
                <a href="{{ $intern_details->linkedin_url }}" target="_blank">{{ $intern_details->linkedin_url }}</a>
            @endif
            @if($intern_details->github_url)
                <a href="{{ $intern_details->github_url }}" target="_blank">{{ $intern_details->github_url }}</a>
            @endif
            @if($intern_details->portfolio_url)
                <a href="{{ $intern_details->portfolio_url }}" target="_blank">{{ $intern_details->portfolio_url }}</a>
            @endif
        </div>
    </section>

    {{-- SKILLS --}}
    <section class="grid">

        {{-- SKILLS --}}
        <div class="card">
            <h3>Skills</h3>

            <div class="skill">
                <span>Social Media Marketing</span><span>85%</span>
                <div class="bar"><div style="width:85%"></div></div>
            </div>

            <div class="skill">
                <span>Content Creation</span><span>90%</span>
                <div class="bar"><div style="width:90%"></div></div>
            </div>

            <div class="skill">
                <span>SEO & Analytics</span><span>75%</span>
                <div class="bar"><div style="width:75%"></div></div>
            </div>

            <div class="skill">
                <span>Copywriting</span><span>80%</span>
                <div class="bar"><div style="width:80%"></div></div>
            </div>

            <div class="skill">
                <span>Email Marketing</span><span>70%</span>
                <div class="bar"><div style="width:70%"></div></div>
            </div>
        </div>

        {{-- EXPERIENCE --}}
        <div class="card">
            <h3>Experience</h3>

            <div class="exp">
                <strong>Marketing Volunteer</strong>
                <span class="org">Local Non-Profit Organization</span>
                <small>Jan 2024 – Present</small>
                <p>Managing social media accounts and creating engaging content</p>
            </div>

            <div class="exp">
                <strong>Content Creator</strong>
                <span class="org">University Marketing Club</span>
                <small>Sep 2023 – Dec 2023</small>
                <p>Produced marketing materials for campus events</p>
            </div>
        </div>

    </section>
@endsection