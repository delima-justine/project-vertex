@extends('layouts.intern')

@section('title', 'Edit Profile | ROC.PH')

@section('styles')
    @vite(['resources/sass/intern.profile.scss'])
@endsection

@section('content')
    <section class="profile-header">
        <div>
            <h3>Edit Profile</h3>
            <p>Update your profile information</p>
        </div>
    </section>

    <section class="card">
        <form action="{{ route('intern.profile.update') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1.5rem;">
                <label for="about" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">About Me</label>
                <textarea 
                    id="about" 
                    name="about" 
                    rows="5" 
                    style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; font-size: inherit;"
                    placeholder="Tell us about yourself, your skills, and career goals..."
                >{{ old('about', $user->about) }}</textarea>
                @error('about')
                    <span style="color: #dc3545; font-size: 0.875rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="linkedin_url" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">LinkedIn URL</label>
                <input 
                    type="url" 
                    id="linkedin_url" 
                    name="linkedin_url" 
                    value="{{ old('linkedin_url', $user->linkedin_url) }}"
                    style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: inherit;"
                    placeholder="https://linkedin.com/in/yourprofile"
                >
                @error('linkedin_url')
                    <span style="color: #dc3545; font-size: 0.875rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="github_url" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">GitHub URL</label>
                <input 
                    type="url" 
                    id="github_url" 
                    name="github_url" 
                    value="{{ old('github_url', $user->github_url) }}"
                    style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: inherit;"
                    placeholder="https://github.com/yourprofile"
                >
                @error('github_url')
                    <span style="color: #dc3545; font-size: 0.875rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="portfolio_url" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Portfolio URL</label>
                <input 
                    type="url" 
                    id="portfolio_url" 
                    name="portfolio_url" 
                    value="{{ old('portfolio_url', $user->portfolio_url) }}"
                    style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: inherit;"
                    placeholder="https://yourportfolio.com"
                >
                @error('portfolio_url')
                    <span style="color: #dc3545; font-size: 0.875rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" style="padding: 0.75rem 1.5rem; background-color: #0dcaf0; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: inherit; font-weight: 500;">
                    Save Changes
                </button>
                <a href="{{ route('intern.profile', $user->user_id) }}" style="padding: 0.75rem 1.5rem; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: inherit; font-weight: 500; text-decoration: none; display: inline-block;">
                    Cancel
                </a>
            </div>
        </form>
    </section>

    @if ($errors->any())
        <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem; border-radius: 4px; margin-top: 1rem;">
            <strong>Errors:</strong>
            <ul style="margin-bottom: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 4px; margin-top: 1rem;">
            {{ session('success') }}
        </div>
    @endif
@endsection
