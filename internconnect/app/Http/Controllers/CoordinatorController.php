<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CoordinatorController extends Controller
{
    /**
     * Display the coordinator dashboard.
     */
    public function dashboard()
    {
        return view('coordinator.dashboard');
    }

    /**
     * Display the monitor interns page.
     */
    public function monitorInterns()
    {
        $user = Auth::user();
        
        if (!$user->coordinator_id) {
            return view('coordinator.monitor-interns', [
                'interns' => collect([]),
                'totalInterns' => 0,
                'activeInterns' => 0,
                'needsSupport' => 0,
                'avgProgress' => 0
            ]);
        }
        
        $interns = User::where('user_role', 'Intern')
            ->where('coordinator_id', $user->coordinator_id)
            ->with(['progress', 'attendances', 'jobApplications.job'])
            ->get()
            ->map(function ($intern) {
                $progress = $intern->progress;
                $attendances = $intern->attendances;
                $application = $intern->jobApplications->first();
                
                $totalAttendances = $attendances->count();
                $totalHoursLogged = $attendances->sum('total_hours');
                $attendancePercentage = $progress && $progress->required_hours > 0 
                    ? round(($totalHoursLogged / $progress->required_hours) * 100) 
                    : 0;
                
                $progressPercentage = $progress && $progress->logged_hours > 0 && $progress->required_hours > 0
                    ? round(($progress->logged_hours / $progress->required_hours) * 100)
                    : 0;
                
                $totalTasks = 15;
                $completedTasks = $progress ? min($totalTasks, round($totalTasks * ($progressPercentage / 100))) : 0;
                
                $lastAttendance = $attendances->sortByDesc('created_at')->first();
                $lastActive = $lastAttendance ? $lastAttendance->created_at->diffForHumans() : 'Never';
                
                $statusBadge = $intern->status === 'Active' ? 'success' : 'warning';
                $statusText = $intern->status === 'Active' ? 'Active' : 'Needs Support';
                
                if ($progressPercentage < 40 && $intern->status === 'Active') {
                    $statusBadge = 'warning';
                    $statusText = 'Needs Support';
                }
                
                return [
                    'id' => $intern->user_id,
                    'first_name' => $intern->first_name,
                    'last_name' => $intern->last_name,
                    'full_name' => $intern->first_name . ' ' . $intern->last_name,
                    'initials' => strtoupper(substr($intern->first_name, 0, 1) . substr($intern->last_name, 0, 1)),
                    'email' => $intern->email,
                    'contact_number' => $intern->contact_number,
                    'position' => $application && $application->job ? $application->job->title : 'Intern',
                    'department' => $application && $application->job ? $application->job->department : 'General',
                    'status_badge' => $statusBadge,
                    'status_text' => $statusText,
                    'progress_percentage' => $progressPercentage,
                    'attendance_percentage' => $attendancePercentage,
                    'completed_tasks' => $completedTasks,
                    'total_tasks' => $totalTasks,
                    'last_active' => $lastActive,
                    'required_hours' => $progress->required_hours ?? 0,
                    'logged_hours' => $progress->logged_hours ?? 0,
                    'milestone' => $progress->milestone ?? null,
                    'milestone_date' => $progress->milestone_achieved_date ?? null,
                    'created_at' => $intern->created_at,
                ];
            });
        
        $totalInterns = $interns->count();
        $activeInterns = $interns->where('status_text', 'Active')->count();
        $needsSupport = $interns->where('status_text', 'Needs Support')->count();
        $avgProgress = $totalInterns > 0 ? round($interns->avg('progress_percentage')) : 0;
        
        return view('coordinator.monitor-interns', compact('interns', 'totalInterns', 'activeInterns', 'needsSupport', 'avgProgress'));
    }

    /**
     * Display the support documents page.
     */
    public function supportDocs()
    {
        return view('coordinator.support-docs');
    }

    /**
     * Display the settings page.
     */
    public function settings()
    {
        return view('coordinator.settings');
    }
}
