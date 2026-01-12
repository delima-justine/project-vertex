<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InternController extends Controller
{
    // Profile of the intern
    public function profile() {
        // Fetch and return profile logic here
    }

    // Update Profile of the intern
    public function updateProfile(Request $request) {
       // Update profile logic here
    }

    // Returns available jobs for the intern
    public function getJobs() {
        // Fetch jobs logic here
    }

    // Get single job details
    public function getJobDetails($jobId) {
        // Fetch job details logic here
    }

    public function applyJob($jobId) {
        // Job application logic here
    }
}
