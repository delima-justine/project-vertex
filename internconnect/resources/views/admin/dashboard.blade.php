@extends('layouts.admin')

@section('content')
    <div class="header">
        <h2>Dashboard</h2>
        <div>
            <a href="/admin/users" class="btn btn-primary">Manage Users</a>
        </div>
    </div>

    <div class="card-wrap">
        @component('components.dashboard-card', ['count' => 3, 'label' => 'Active Jobs'])
        @endcomponent
        @component('components.dashboard-card', ['count' => 55, 'label' => 'Applications'])
        @endcomponent
        @component('components.dashboard-card', ['count' => 12, 'label' => 'Active Interns'])
        @endcomponent
        @component('components.dashboard-card', ['count' => 8, 'label' => 'Pending Review'])
        @endcomponent
    </div>

    <div class="table">
        <div style="padding:18px">
            <h3 style="margin:0 0 12px 0">Pending Applications</h3>
            <p style="color:#6b7280; margin:0">Quick overview cards above are reusable components.</p>
        </div>
    </div>

@endsection
