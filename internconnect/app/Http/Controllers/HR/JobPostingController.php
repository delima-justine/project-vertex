<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use App\Models\User;
use App\Services\NotificationService;
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

        $jobPosting = JobPosting::create($validated);

        // Send notifications to all interns about new job post
        NotificationService::notifyNewJobPost($jobPosting);

        // Send recommendations to matching interns
        $interns = User::where('user_role', 'Intern')->get();
        foreach ($interns as $intern) {
            // Check if job matches intern's profile
            $internAbout = strtolower($intern->about ?? '');
            $jobTitle = strtolower($jobPosting->title);
            $jobDept = strtolower($jobPosting->department ?? '');
            $jobDesc = strtolower($jobPosting->description ?? '');
            
            // Simple matching logic - if job title or department contains keywords from intern's about
            if (!empty($internAbout)) {
                $keywords = array_filter(explode(' ', $internAbout), function($word) {
                    return strlen($word) > 3;
                });
                
                $isMatch = false;
                foreach ($keywords as $keyword) {
                    if (strpos($jobTitle, $keyword) !== false || 
                        strpos($jobDept, $keyword) !== false ||
                        strpos($jobDesc, $keyword) !== false) {
                        $isMatch = true;
                        break;
                    }
                }
                
                if ($isMatch) {
                    NotificationService::notifyRecommendedJobPost($jobPosting, $intern);
                }
            }
        }
        return redirect()->route('hr.job-postings.index')->with('success', 'Job posting created successfully');
    }

    public function show(JobPosting $jobPosting)
    {
        return view('hr.job_postings.show', compact('jobPosting'));
    }

    public function edit(JobPosting $jobPosting)
    {
        return view('hr.job_postings.edit', compact('jobPosting'));
    }

    public function update(Request $request, JobPosting $jobPosting)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'department' => 'required|string|max:50',
            'salary_range' => 'nullable|string|max:50',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
        ]);

        $jobPosting->update($validated);

        return redirect()->route('hr.job-postings.index')->with('success', 'Job posting updated successfully');
    }

    public function destroy(JobPosting $jobPosting)
    {
        $jobPosting->delete();
        return redirect()->route('hr.job-postings.index')->with('success', 'Job posting deleted successfully');
    }
}