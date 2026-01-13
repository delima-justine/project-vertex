<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function backup()
    {
        try {
            $jobPostings = JobPosting::with('postedBy', 'applications')->get();
            $filename = 'job_postings_backup_' . date('Y-m-d_H-i-s') . '.json';
            
            $backup = [
                'backup_date' => now(),
                'total_postings' => count($jobPostings),
                'job_postings' => $jobPostings
            ];

            return response()->json($backup, 200, [
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ])->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Backup failed: ' . $e->getMessage()]);
        }
    }

    public function restoreForm()
    {
        return view('hr.job_postings.restore');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:json'
        ]);

        try {
            $file = $request->file('backup_file');
            $content = json_decode(file_get_contents($file->getRealPath()), true);

            if (!isset($content['job_postings']) || !is_array($content['job_postings'])) {
                return back()->withErrors(['error' => 'Invalid backup file format']);
            }

            DB::beginTransaction();

            $restored = 0;
            foreach ($content['job_postings'] as $jobData) {
                $jobPosting = JobPosting::updateOrCreate(
                    ['job_id' => $jobData['job_id']],
                    [
                        'title' => $jobData['title'],
                        'description' => $jobData['description'],
                        'requirements' => $jobData['requirements'] ?? null,
                        'department' => $jobData['department'],
                        'salary_range' => $jobData['salary_range'] ?? null,
                        'posted_by_user_id' => $jobData['posted_by_user_id'],
                        'post_date' => $jobData['post_date'],
                    ]
                );
                $restored++;
            }

            DB::commit();

            return redirect()->route('hr.job-postings.index')->with('success', "Successfully restored {$restored} job postings from backup");
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Job posting restore failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Restore failed: ' . $e->getMessage()]);
        }
    }
}