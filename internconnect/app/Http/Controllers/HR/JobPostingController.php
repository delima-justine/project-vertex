<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;

class JobPostingController extends Controller
{
    public function index()
    {
        $jobPostings = JobPosting::withCount('applications')
            ->orderBy('post_date', 'desc')
            ->get()
            ->map(function ($job) {
                $job->is_active = $job->post_date && $job->post_date->isAfter(now()->subDays(30));
                return $job;
            });

        return view('hr.job_postings.index', compact('jobPostings'));
    }

    public function create()
    {
        return view('hr.job_postings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'department' => 'required|string|max:50',
            'salary_range' => 'nullable|string|max:50',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
        ]);

        $validated['posted_by_user_id'] = Auth::id();
        $validated['post_date'] = now();

        JobPosting::create($validated);

        return redirect()->route('hr.job-postings.index')->with('success', 'Job posting created successfully.');
    }
}
