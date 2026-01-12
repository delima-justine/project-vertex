<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JobApplication;
use App\Models\Document;
use App\Models\JobPosting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Count total interns
        $totalInterns = User::where('user_role', 'Intern')->count();

        // Count active applications (pending status)
        try {
            $activeApplications = JobApplication::where('hr_status', 'pending')->count();
        } catch (\Exception $e) {
            $activeApplications = 0;
        }

        // Count pending documents
        try {
            $pendingDocuments = Document::where('verification_status', 'pending')->count();
        } catch (\Exception $e) {
            $pendingDocuments = 0;
        }

        // Fetch all recent activities from multiple sources
        $activities = collect([]);

        // Fetch recent job postings
        try {
            $jobPostings = JobPosting::orderBy('post_date', 'desc')
                ->with('postedBy')
                ->take(10)
                ->get()
                ->map(function ($job) {
                    return [
                        'type' => 'Job Posted',
                        'type_badge' => 'primary',
                        'id' => $job->job_id,
                        'title' => $job->title,
                        'subtitle' => $job->department,
                        'date' => $job->post_date ? $job->post_date->timestamp : now()->timestamp,
                        'display_date' => $job->post_date ? $job->post_date->format('M d, Y') : 'N/A',
                        'description' => $job->postedBy ? 'Posted by ' . $job->postedBy->first_name . ' ' . $job->postedBy->last_name : 'Posted by Unknown',
                    ];
                });
            $activities = $activities->concat($jobPostings);
        } catch (\Exception $e) {
            // Silently fail
        }

        // Fetch recent job applications
        try {
            $jobApplications = JobApplication::orderBy('application_date', 'desc')
                ->with('user', 'job')
                ->take(10)
                ->get()
                ->map(function ($app) {
                    return [
                        'type' => 'New Application',
                        'type_badge' => 'info',
                        'id' => $app->application_id,
                        'title' => $app->user ? $app->user->first_name . ' ' . $app->user->last_name . ' applied for ' . ($app->job ? $app->job->title : 'a position') : 'Unknown Application',
                        'subtitle' => $app->job ? $app->job->title : 'N/A',
                        'date' => $app->application_date ? $app->application_date->timestamp : now()->timestamp,
                        'display_date' => $app->application_date ? $app->application_date->format('M d, Y') : 'N/A',
                        'description' => $app->user ? $app->user->email : 'Unknown',
                    ];
                });
            $activities = $activities->concat($jobApplications);
        } catch (\Exception $e) {
            // Silently fail
        }

        // Fetch recent new interns
        try {
            $newInterns = User::where('user_role', 'Intern')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($intern) {
                    return [
                        'type' => 'New Intern',
                        'type_badge' => 'success',
                        'id' => $intern->user_id,
                        'title' => $intern->first_name . ' ' . $intern->last_name . ' registered',
                        'subtitle' => $intern->email,
                        'date' => $intern->created_at ? $intern->created_at->timestamp : now()->timestamp,
                        'display_date' => $intern->created_at ? $intern->created_at->format('M d, Y') : 'N/A',
                        'description' => 'New Intern Registered',
                    ];
                });
            $activities = $activities->concat($newInterns);
        } catch (\Exception $e) {
            // Silently fail
        }

        // Fetch recent document uploads
        try {
            $documents = Document::orderBy('submission_date', 'desc')
                ->with('user')
                ->take(10)
                ->get()
                ->map(function ($doc) {
                    return [
                        'type' => 'Document Uploaded',
                        'type_badge' => 'warning',
                        'id' => $doc->doc_id,
                        'title' => ($doc->user ? $doc->user->first_name . ' ' . $doc->user->last_name : 'Unknown') . ' uploaded ' . ($doc->doc_type ?? 'a document'),
                        'subtitle' => $doc->doc_type ?? 'Document',
                        'date' => $doc->submission_date ? $doc->submission_date->timestamp : now()->timestamp,
                        'display_date' => $doc->submission_date ? $doc->submission_date->format('M d, Y') : 'N/A',
                        'description' => $doc->verification_status ? 'Status: ' . ucfirst($doc->verification_status) : 'Pending Review',
                    ];
                });
            $activities = $activities->concat($documents);
        } catch (\Exception $e) {
            // Silently fail
        }

        // Sort by date (newest first) and take only 10 recent activities
        $recentActivities = $activities->sortByDesc('date')->take(10);

        return view('hr.dashboard', compact('totalInterns', 'activeApplications', 'pendingDocuments', 'recentActivities'));
    }
}