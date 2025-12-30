@extends('layouts.admin')

@section('content')
    <div class="header">
        <h2>Create User</h2>
        <div><a href="{{ route('admin.users.index') }}" class="btn">Back</a></div>
    </div>

    <div class="card" style="max-width:720px">
        <form method="post" action="{{ route('admin.users.store') }}">
            @csrf
            <div style="padding:12px">
                <div style="margin-bottom:8px"><label>Name</label><br>
                    <input type="text" name="name" value="{{ old('name') }}" style="width:100%; padding:8px;border-radius:6px;border:1px solid #e5e7eb"></div>
                <div style="margin-bottom:8px"><label>Email</label><br>
                    <input type="email" name="email" value="{{ old('email') }}" style="width:100%; padding:8px;border-radius:6px;border:1px solid #e5e7eb"></div>
                <div style="margin-bottom:8px"><label>Password</label><br>
                    <input type="password" name="password" style="width:100%; padding:8px;border-radius:6px;border:1px solid #e5e7eb"></div>
                <div style="margin-bottom:8px"><label>Confirm Password</label><br>
                    <input type="password" name="password_confirmation" style="width:100%; padding:8px;border-radius:6px;border:1px solid #e5e7eb"></div>
                <div><button class="btn btn-primary">Create</button></div>
            </div>
        </form>
    </div>

@endsection
