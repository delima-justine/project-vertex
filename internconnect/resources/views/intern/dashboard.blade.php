@extends('layouts.intern')

@section('title', 'ROC.PH Intern Portal')

@section('styles')
    @vite(['resources/sass/intern.dashboard.scss'])
@endsection

@section('content')
    <section class="welcome">
        <h1>Welcome back, Intern!</h1>
        <p>You have 3 upcoming tasks and 5 applications in review.</p>
    </section>

    <section class="stats">
        <div class="card blue">
            <span>12</span>
            <p>Applications</p>
        </div>
        <div class="card yellow">
            <span>5</span>
            <p>In Review</p>
        </div>
        <div class="card green">
            <span>3</span>
            <p>Interviews</p>
        </div>
        <div class="card red">
            <span>1</span>
            <p>Offers</p>
        </div>
    </section>

    <section class="lower">
        <div class="panel">
            <h3>Recent Activity</h3>
            <ul>
                <li>Applied to Marketing Intern at Tech Corp <small>2 hours ago</small></li>
                <li>Profile viewed by Digital Agency <small>5 hours ago</small></li>
                <li>Interview scheduled with StartupXYZ <small>1 day ago</small></li>
                <li>Application updated for Social Media Role <small>2 days ago</small></li>
            </ul>
        </div>

        <div class="panel">
            <h3>Upcoming Tasks</h3>
            <div class="task high">
                Interview with StartupXYZ
                <small>Tomorrow, 2:00 PM</small>
            </div>
            <div class="task medium">
                Submit portfolio for TechCorp
                <small>Dec 10, 5:00 PM</small>
            </div>
            <div class="task low">
                Follow up with Digital Agency
                <small>Dec 12</small>
            </div>
        </div>
    </section>
@endsection