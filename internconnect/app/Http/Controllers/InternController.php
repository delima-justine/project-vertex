<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\User;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class InternController extends Controller
{
    // Dashboard for the intern
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get statistics for the logged-in intern
        $totalApplications = JobApplication::where('user_id', $user->user_id)->count();
        $pendingApplications = JobApplication::where('user_id', $user->user_id)
            ->where('hr_status', 'Pending Review')
            ->count();
        $interviewingApplications = JobApplication::where('user_id', $user->user_id)
            ->where('hr_status', 'Interviewing')
            ->count();
        $offersCount = JobApplication::where('user_id', $user->user_id)
            ->where('hr_status', 'Hired')
            ->count();

        // Get recent job applications
        $recentApplications = JobApplication::where('user_id', $user->user_id)
            ->with('jobPosting')
            ->orderBy('application_date', 'desc')
            ->limit(4)
            ->get();

        return view('intern.dashboard', compact(
            'user',
            'totalApplications',
            'pendingApplications',
            'interviewingApplications',
            'offersCount',
            'recentApplications'
        ));
    }

    // Profile of the intern
    public function profile($id) {
        // Fetch profile logic here
        $intern_details = User::findOrFail($id);

        return view('intern.profile', compact('intern_details'));
    }

    // Show edit profile form
    public function editProfile()
    {
        $user = Auth::user();
        return view('intern.profile-edit', compact('user'));
    }

    // Update Profile of the intern
    public function updateProfile(Request $request) {
        $user = Auth::user();
        
        $validated = $request->validate([
            'about' => 'nullable|string|max:1000',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
        ]);

        $user->update($validated);

        return redirect()->route('intern.profile', $user->user_id)
            ->with('success', 'Profile updated successfully!');
    }

    // Returns available jobs for the intern
    public function getJobs() {
        // Fetch jobs logic here
        $jobs = JobPosting::orderBy('created_at', 'desc')->paginate(10);

        return view('intern.job_search', compact('jobs'));
    }

    // Get single job details
    public function getJobDetails($jobId) {
        // Fetch job details logic here
    }

    public function applyJob($jobId) {
        // Job application logic here
    }
}
