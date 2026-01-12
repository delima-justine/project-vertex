<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class HRController extends Controller
{
    public function dashboard()
    {
        return view('hr.dashboard');
    }

    public function interns()
    {
        // Fetch all users with role 'Intern' and their progress data
        $interns = User::where('user_role', 'Intern')
            ->with('progress')
            ->get()
            ->map(function ($intern) {
                $progress = $intern->progress;
                
                // Calculate progress percentage
                $requiredHours = $progress && $progress->required_hours ? $progress->required_hours : 0;
                $loggedHours = $progress && $progress->logged_hours ? $progress->logged_hours : 0;
                
                if ($requiredHours > 0) {
                    $progressPercentage = round(($loggedHours / $requiredHours) * 100);
                } else {
                    $progressPercentage = 0;
                }
                
                // Calculate week number from start_date (assuming start_date is in User model)
                // If no start_date, use created_at
                $startDate = $intern->created_at ? Carbon::parse($intern->created_at) : now();
                $weekNumber = now()->diffInWeeks($startDate) + 1;
                
                // Determine status
                if ($progressPercentage >= 100) {
                    $status = 'completed';
                    $statusBadge = 'success';
                    $statusText = 'Completed';
                } elseif ($progressPercentage < 50 && $weekNumber >= 4) {
                    $status = 'needs-attention';
                    $statusBadge = 'warning';
                    $statusText = 'Needs Attention';
                } else {
                    $status = 'on-track';
                    $statusBadge = 'info';
                    $statusText = 'On Track';
                }
                
                return [
                    'id' => $intern->user_id,
                    'name' => $intern->first_name . ' ' . $intern->last_name,
                    'email' => $intern->email,
                    'department' => $intern->school ? $intern->school->school_name : 'N/A',
                    'week' => $weekNumber,
                    'of' => 8, // Standard internship duration
                    'progress' => $progressPercentage,
                    'logged_hours' => $loggedHours,
                    'required_hours' => $requiredHours,
                    'status' => $status,
                    'status_badge' => $statusBadge,
                    'status_text' => $statusText,
                ];
            });

        return view('hr.interns', compact('interns'));
    }

    public function viewInternProfile($internId)
    {
        // Fetch intern with progress and related data
        $intern = User::where('user_role', 'Intern')
            ->where('user_id', $internId)
            ->with(['progress', 'school', 'documents', 'jobApplications'])
            ->firstOrFail();

        // Calculate progress data
        $progress = $intern->progress;
        $requiredHours = $progress && $progress->required_hours ? $progress->required_hours : 0;
        $loggedHours = $progress && $progress->logged_hours ? $progress->logged_hours : 0;
        
        if ($requiredHours > 0) {
            $progressPercentage = round(($loggedHours / $requiredHours) * 100);
        } else {
            $progressPercentage = 0;
        }

        // Calculate week number
        $startDate = $intern->created_at ? Carbon::parse($intern->created_at) : now();
        $weekNumber = now()->diffInWeeks($startDate) + 1;

        // Determine status
        if ($progressPercentage >= 100) {
            $status = 'completed';
            $statusBadge = 'success';
            $statusText = 'Completed';
        } elseif ($progressPercentage < 50 && $weekNumber >= 4) {
            $status = 'needs-attention';
            $statusBadge = 'warning';
            $statusText = 'Needs Attention';
        } else {
            $status = 'on-track';
            $statusBadge = 'info';
            $statusText = 'On Track';
        }

        $internData = [
            'id' => $intern->user_id,
            'name' => $intern->first_name . ' ' . $intern->last_name,
            'email' => $intern->email,
            'contact_number' => $intern->contact_number,
            'department' => $intern->school ? $intern->school->school_name : 'N/A',
            'week' => $weekNumber,
            'of' => 8,
            'progress' => $progressPercentage,
            'logged_hours' => $loggedHours,
            'required_hours' => $requiredHours,
            'status' => $status,
            'status_badge' => $statusBadge,
            'status_text' => $statusText,
            'documents_count' => $intern->documents ? count($intern->documents) : 0,
            'applications_count' => $intern->jobApplications ? count($intern->jobApplications) : 0,
            'created_at' => $intern->created_at,
        ];

        return view('hr.intern-profile', compact('internData', 'intern'));
    }
}