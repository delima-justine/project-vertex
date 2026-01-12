<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class InternController extends Controller
{
    // Profile of the intern
    public function profile($id) {
        // Fetch profile logic here
        $intern_details = User::findOrFail($id);

        return view('intern.profile', compact('intern_details'));
    }

    // Update Profile of the intern
    public function updateProfile(Request $request) {
       // Update profile logic here
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
